<?php


namespace App\Models;

use PDO;

class Game
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function games(): array
    {
        $query = 'SELECT * 
            FROM games';

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_game(int $id)
    {
        $query = 'SELECT * 
            FROM games 
            WHERE id = :id';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_game_by_name(string $name)
    {
        $query = 'SELECT * 
            FROM games 
            WHERE name = :name';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function game_stats(string $name): array
    {
        $query = 'SELECT gd.*, u.username 
                FROM game_data gd
                JOIN users u ON gd.user_id = u.id
                WHERE gd.game_id = :game_id
                ORDER BY 
                    CASE 
                    WHEN gd.score IS NOT NULL AND gd.score = CAST(gd.score AS UNSIGNED) THEN gd.score END DESC,
                    CASE 
                    WHEN gd.score IS NOT NULL AND gd.score != CAST(gd.score AS UNSIGNED) THEN gd.score END ASC
                LIMIT 15';

        $game = $this->get_game_by_name($name);

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':game_id', $game['id']);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function record_exist(string $name, string $username): bool
    {
        // Ottieni l'ID del gioco
        $game = $this->get_game_by_name($name);

        // Controlla se esiste la combinazione di user_id e game_id
        $query = 'SELECT COUNT(*) FROM game_data 
                WHERE user_id = (SELECT id FROM users WHERE username = :username)
                AND game_id = :game_id';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':game_id', $game['id']);
        $stmt->execute();

        return $stmt->fetchColumn() > 0; // Ritorna true se il record esiste
    }

    public function create_game_stats(string $name, string $username, float $score)
    {
        $game = $this->get_game_by_name($name);

        // Inserisci un nuovo record se non esiste
        $query = 'INSERT INTO game_data (user_id, game_id, score, created_at, updated_at) 
                VALUES (
                    (SELECT id FROM users WHERE username = :username),
                    :game_id,
                    :score,
                    NOW(),
                    NOW()
                )';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':game_id', $game['id']);
        $stmt->bindParam(':score', $score);

        $stmt->execute();
    }

    public function update_game_stats(string $name, string $username, float $score)
    {
        $game = $this->get_game_by_name($name);

        // Aggiorna il record esistente
        $query = 'UPDATE game_data 
                SET score = :score, updated_at = NOW()
                WHERE user_id = (SELECT id FROM users WHERE username = :username)
                AND game_id = :game_id';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':game_id', $game['id']);
        $stmt->bindParam(':score', $score);

        $stmt->execute();
    }

    public function game_exits(string $name): bool
    {
        $game = $this->get_game_by_name($name);

        if ($game) {
            return true;
        }

        return false;
    }



    // public function insertGameData($userId, $gameId, $score) {
    //     $query = "INSERT INTO game_data (user_id, game_id, score) VALUES (:userId, :gameId, :score)";
    //     $stmt = $this->db->prepare($query);
    //     $stmt->bindParam(':userId', $userId);
    //     $stmt->bindParam(':gameId', $gameId);
    //     $stmt->bindParam(':score', $score);
    //     $stmt->execute();
    // }

    // // Ottiene tutti gli utenti
    // public function getAllUsers() {
    //     $query = "SELECT id, email, username FROM users";
    //     return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    // }

    // // Ottiene l'ID del gioco basato sul nome
    // public function getGameIdByName($gameName) {
    //     $query = "SELECT id FROM games WHERE name = :gameName LIMIT 1";
    //     $stmt = $this->db->prepare($query);
    //     $stmt->bindParam(':gameName', $gameName);
    //     $stmt->execute();
    //     return $stmt->fetchColumn(); // Restituisce l'ID del gioco
    // }
}