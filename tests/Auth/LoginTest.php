<?php

use PHPUnit\Framework\TestCase;
use App\Services\AuthService;
use App\Models\User;
use App\Core\ServiceContainer;
use App\Facades\BaseFacade;
use App\Core\Session;
use App\Session\SessionManager;

use PDO;

class LoginTest extends TestCase
{
    private $pdo;
    private $authService;
    private $container;

    protected function setUp(): void
    {
        // Configura un database SQLite in memoria
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Crea le tabelle necessarie nel database in memoria
        $this->pdo->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username VARCHAR(25) NOT NULL,
                email VARCHAR(255) NOT NULL,
                password VARCHAR(255),
                is_admin TINYINT(1) NOT NULL DEFAULT 0,
                active TINYINT(1) DEFAULT 0,
                activation_code VARCHAR(255),
                activation_expiry DATETIME,
                activated_at DATETIME,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ");

        // Inizializza il modello User e il servizio AuthService con il database di test
        $userModel = new User($this->pdo);
        $this->authService = new AuthService($userModel);

        $session_config = [
            'driver' => 'array',
            'session_path' => __DIR__ . '/../storage/framework/sessions',
        ];
        $this->container = ServiceContainer::get_instance();
        BaseFacade::set_container($this->container);
        $session_manager = new SessionManager($session_config, $this->container);
        $this->container->registerLazy(Session::class, function() use ($session_manager) {
            return new Session($session_manager->driver());;
        });

    }

    public function testValidCredentials()
    {
        // Inserisce un utente di test con credenziali valide
        $hashedPassword = password_hash('validpassword', PASSWORD_BCRYPT);
        $this->pdo->exec("INSERT INTO users (username, email, password, active) VALUES ('validuser', 'test@example.com', '$hashedPassword', 1)");

        // Simula la chiamata di login
        $result = $this->authService->login('validuser', 'validpassword');

        // Verifica che il risultato contenga i dati utente
        $this->assertIsArray($result, 'Il login con credenziali valide dovrebbe restituire un array di dati utente.');
        $this->assertEquals('validuser', $result['username']);
    }

    public function testInvalidCredentials()
    {
        // Inserisce un utente di test con credenziali valide
        $hashedPassword = password_hash('validpassword', PASSWORD_BCRYPT);
        $this->pdo->exec("INSERT INTO users (username, email, password, active) VALUES ('validuser', 'test@example.com', '$hashedPassword', 1)");

        // Simula la chiamata di login con credenziali non valide
        $result = $this->authService->login('validuser', 'invalidpassword');
        
        // Verifica che il risultato sia null per credenziali errate
        $this->assertNull($result, 'Il login con credenziali non valide dovrebbe restituire null.');
    }

    public function testEmptyUsername()
    {
        // Simula il login con username vuoto
        $result = $this->authService->login('', 'password');
        $this->assertNull($result, 'Il login senza username dovrebbe restituire null.');
    }

    public function testEmptyPassword()
    {
        // Simula il login con password vuota
        $result = $this->authService->login('user', '');
        $this->assertNull($result, 'Il login senza password dovrebbe restituire null.');
    }

    public function testSQLInjection()
    {
        // Inserisci un utente valido per testare tentativi di injection
        $this->pdo->exec("INSERT INTO users (username, email, password, active) VALUES ('admin', 'admin@example.com', '" . password_hash('password', PASSWORD_BCRYPT) . "', 1)");

        // Testa il login con un tentativo di SQL injection
        $result = $this->authService->login("admin' OR 1=1 -- ", 'password');

        // Verifica che il login fallisca e restituisca null
        $this->assertNull($result, 'Il login con tentativo di SQL injection dovrebbe restituire null.');
    }

    public function testXSSAttack()
    {
        // Inserisci un utente valido
        $this->pdo->exec("INSERT INTO users (username, email, password, active) VALUES ('user', 'user@example.com', '" . password_hash('password', PASSWORD_BCRYPT) . "', 1)");

        // Testa il login con tentativo di XSS
        $result = $this->authService->login('<script>alert("XSS")</script>', 'password');

        // Verifica che il login fallisca e restituisca null
        $this->assertNull($result, 'Il login con tentativo di XSS dovrebbe restituire null.');
    }

    public function testLongUsername()
    {
        // Testa il login con un username che supera la lunghezza massima
        $longUsername = str_repeat('a', 26); // Oltre la lunghezza massima di 25 caratteri

        $result = $this->authService->login($longUsername, 'password');

        // Verifica che il login fallisca e restituisca null
        $this->assertNull($result, 'Il login con un username lungo dovrebbe restituire null.');
    }

    public function testLongPassword()
    {
        // Inserisci un utente valido
        $this->pdo->exec("INSERT INTO users (username, email, password, active) VALUES ('user', 'user@example.com', '" . password_hash('password', PASSWORD_BCRYPT) . "', 1)");

        // Testa il login con una password molto lunga
        $longPassword = str_repeat('a', 256); // Oltre la lunghezza di password tipica

        $result = $this->authService->login('user', $longPassword);

        // Verifica che il login fallisca e restituisca null
        $this->assertNull($result, 'Il login con una password lunga dovrebbe restituire null.');
    }

    public function testSpecialCharactersInUsername()
    {
        // Testa il login con un username che contiene caratteri speciali
        $result = $this->authService->login('user!@#', 'password');

        // Verifica che il login fallisca e restituisca null
        $this->assertNull($result, 'Il login con caratteri speciali nell\'username dovrebbe restituire null.');
    }

    public function testSpecialCharactersInPassword()
    {
        // Inserisci un utente valido
        $this->pdo->exec("INSERT INTO users (username, email, password, active) VALUES ('user', 'user@example.com', '" . password_hash('password', PASSWORD_BCRYPT) . "', 1)");

        // Testa il login con una password che contiene caratteri speciali
        $result = $this->authService->login('user', 'pass!@#$%^&*()');

        // Verifica che il login fallisca e restituisca null
        $this->assertNull($result, 'Il login con caratteri speciali nella password dovrebbe restituire null.');
    }
}
