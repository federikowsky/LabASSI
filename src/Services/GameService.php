<?php 

namespace App\Services;

use App\Models\Game;


class GameService
{
    protected $gameModel;

    public function __construct(Game $gameModel)
    {
        $this->gameModel = $gameModel;
    }

    public function games()
    {
        return $this->gameModel->games();
    }

    public function get_game(int $id)
    {
        return $this->gameModel->get_game($id);
    }

    public function get_game_by_name(string $name)
    {
        return $this->gameModel->get_game_by_name($name);
    }

    public function game_stats(string $name): array
    {
        return $this->gameModel->game_stats($name);
    }

    public function game_exits(string $name)
    {
        return $this->gameModel->game_exits($name);
    }

    public function game_stats_update(string $name, string $username, float $score)
{
    // Verifica se il record esiste, poi chiama aggiorna o crea
    if ($this->gameModel->record_exist($name, $username)) {
        return $this->gameModel->update_game_stats($name, $username, $score);
    } else {
        return $this->gameModel->create_game_stats($name, $username, $score);
    }
}

    // public function createStatistics() {
    //     // Ottieni tutti gli utenti
    //     $users = $this->gameModel->getAllUsers();

    //     // Per ogni utente, inserisci punteggi per 4 giochi
    //     foreach ($users as $user) {
    //         $this->assignScoresToUser($user['id']);
    //     }
    // }

    // private function assignScoresToUser($userId) {
    //     // Definizione dei giochi e assegnazione dei punteggi
    //     $games = ['SIMON', 'DOT', 'MEMORY', 'GTW'];
        
    //     foreach ($games as $game) {
    //         // Ottieni l'ID del gioco
    //         $gameId = $this->gameModel->getGameIdByName($game);

    //         // Genera un punteggio casuale (come da funzione SQL)
    //         $score = $this->generateRandomScore($game);

    //         // Inserisci il punteggio nel database
    //         if ($gameId) {
    //             $this->gameModel->insertGameData($userId, $gameId, $score);
    //         }
    //     }
    // }

    // private function generateRandomScore($game) {
    //     // Assegna punteggi casuali basati sul gioco
    //     if (in_array($game, ['MEMORY', 'GTW'])) {
    //         return round(mt_rand(0, 100) + mt_rand(0, 99) / 100, 2); // Numero con 2 decimali
    //     } else {
    //         return round(mt_rand(0, 100)); // Numero intero
    //     }
    // }
}