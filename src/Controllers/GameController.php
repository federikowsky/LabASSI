<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\ {
    GameService
};

class GameController extends BaseController
{
    protected $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * Get all games
     * @api
     */
    public function games()
    {
        $games = $this->gameService->games();

        $games = array_map(function ($game) {
            $game['link'] = empty($game['name']) 
                ? '#' 
                : '/game/' . urlencode(strtolower(str_replace(' ', '_', $game['name'])));
            return $game;
        }, $games);

        return response()->json($games);
    }

    /**
     * Get game stats
     * @api
     */
    public function game_stats(string $game_name)
    {
        $stats = $this->gameService->game_stats($game_name);

        // map the stats to only include username and score
        $stats = array_map(function ($stat) {
            return [
                'username' => $stat['username'],
                'score' => $stat['score']
            ];
        }, $stats);
        
        return response()->json($stats);
    }


    /**
     * Update game stats
     * @api
     */
    public function game_stats_update(string $game_name)
    {
        if (!request()->is_method('post')) {
            return response()->json([
                'error' => 'Only POST requests are allowed'
            ], 405);
        }

        if (!session()->has('username') || !session()->has('email')) {
            return response()->json([
                'error' => 'You need to be logged in to update game stats'
            ], 401);
        }

        $username = session()->get('username');
        $score = request()->input('score');

        if (!is_numeric($score)) {
            return response()->json([
                'error' => 'Score must be a number'
            ], 400);
        }

        $this->gameService->game_stats_update($game_name, $username, (float) $score);

        return response()->json([
            'message' => 'Game stats updated successfully'
        ], 200);
    }

    public function index(string $game_name)
    {
        // check if the game exists 
        $game = $this->gameService->game_exits($game_name);

        // if the game does not exist redirect to home
        if (!$game) {
            return redirect()->to('/');
        }

        $stats = $this->gameService->game_stats($game_name);

        return view('games/' . $game_name)->with_input([
            'stats' => $stats
        ]);
    }

    public function ranking()
    {
        $games = $this->gameService->games();

        $stats = [];

        foreach ($games as $game) {
            if ($game['name']) {
                $stats[$game['name']] = $this->gameService->game_stats($game['name']);
            }
        }

        return view('games/ranking')->with_input([
            'stats' => $stats
        ]);
    }
}