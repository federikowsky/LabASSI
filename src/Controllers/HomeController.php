<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\ {
    AuthService,
    GameService
};

class HomeController extends BaseController
{
    protected $gameService;
    protected $authService;

    public function __construct(AuthService $authService, GameService $gameService)
    {
        $this->authService = $authService;
        $this->gameService = $gameService;
    }

    public function index()
    {
        $games = $this->gameService->games();

        $games = array_map(function ($game) {
            $game['link'] = empty($game['name']) 
                ? '#' 
                : '/game/' . urlencode(strtolower(str_replace(' ', '_', $game['name'])));
            return $game;
        }, $games);

        return view('home')->with_input([
            'games' => $games,
        ]);
    }

    public function about_us()
    {
        return view('about_us');
    }
}