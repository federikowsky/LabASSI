<?php

use PHPUnit\Framework\TestCase;
use App\Services\AuthService;
use App\Models\User;
use App\Core\ServiceContainer;
use App\Facades\BaseFacade;
use App\Core\Session;
use App\Session\SessionManager;

use PDO;

class RegisterTest extends TestCase
{
    private $pdo;
    private $authService;
    private $container;

    protected function setUp(): void
    {

        define('SECRET_KEY', "secret");

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
        $authService = new AuthService($userModel);

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

        $this->container->register(User::class, function() use ($userModel) {
            return $userModel;
        });

        $this->container->register(AuthService::class, function() use ($authService) {
            return $authService;
        });

        $this->authService = $authService;
    }

    public function testValidRegistration()
    {
        // Simula una registrazione valida
        $result = $this->authService->register('newuser', 'newuser@example.com', 'securepassword', "activationCode");

        // Verifica che il risultato sia true
        $this->assertTrue($result, 'La registrazione con credenziali valide dovrebbe essere completata con successo.');
    }

    public function testEmptyUsername()
    {
        // Simula una registrazione con username vuoto
        $result = $this->authService->register('', 'valid@example.com', 'securepassword', "activationCode");

        // Verifica che la registrazione fallisca
        $this->assertFalse($result, 'La registrazione dovrebbe fallire se l\'username è vuoto.');
    }

    public function testEmptyEmail()
    {
        // Simula una registrazione con email vuota
        $result = $this->authService->register('validuser', '', 'securepassword', "activationCode");

        // Verifica che la registrazione fallisca
        $this->assertFalse($result, 'La registrazione dovrebbe fallire se l\'email è vuota.');
    }

    public function testEmptyPassword()
    {
        // Simula una registrazione con password vuota
        $result = $this->authService->register('validuser', 'valid@example.com', '', "activationCode");

        // Verifica che la registrazione fallisca
        $this->assertFalse($result, 'La registrazione dovrebbe fallire se la password è vuota.');
    }

    public function testLongUsername()
    {
        $longUsername = str_repeat('a', 26); // Oltre la lunghezza massima

        // Simula una registrazione con username lungo
        $result = $this->authService->register($longUsername, 'valid@example.com', 'securepassword', "activationCode");

        // Verifica che la registrazione fallisca
        $this->assertFalse($result, 'La registrazione dovrebbe fallire con un username troppo lungo.');
    }
}