<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\ {
    GameService
};
use PHPUnit\Util\Json;

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
    public function get_games()
    {
        $games = $this->gameService->get_games();

        $games = array_map(function ($game) {
            $game['link'] = empty($game['name']) 
                ? '#' 
                : '/game/' . urlencode(strtolower(str_replace(' ', '_', $game['name'])));
            return $game;
        }, $games);

        return response()->json($games);
    }

    public function index(string $game_name)
    {
        // check if the game exists 
        $game = $this->gameService->game_exits($game_name);

        // if the game does not exist redirect to home
        if (!$game) {
            return redirect()->to('/');
        }

        $stats = $this->gameService->get_game_stats($game_name);

        return view('games/' . $game_name)->with_input([
            'stats' => $stats
        ]);
    }

    public function ranking()
    {
        $games = $this->gameService->get_games();

        $stats = [];

        foreach ($games as $game) {
            if ($game['name']) {
                $stats[$game['name']] = $this->gameService->get_game_stats($game['name']);
            }
        }

        return view('games/ranking')->with_input([
            'stats' => $stats
        ]);
    }
}