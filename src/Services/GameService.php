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

    public function get_games()
    {
        return $this->gameModel->get_games();
    }

    public function get_game(int $id)
    {
        return $this->gameModel->get_game($id);
    }

    public function get_game_by_name(string $name)
    {
        return $this->gameModel->get_game_by_name($name);
    }

    public function get_game_stats(string $name): array
    {
        return $this->gameModel->get_game_stats($name);
    }

    public function game_exits(string $name)
    {
        return $this->gameModel->game_exits($name);
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