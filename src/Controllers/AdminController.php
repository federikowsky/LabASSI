<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\TournamentService;
use App\Services\UserService;

class AdminController extends BaseController
{
    protected $userService;
    protected $tournamentService;

    public function __construct(UserService $userService, TournamentService $tournamentService)
    {
        $this->userService = $userService;
        $this->tournamentService = $tournamentService;
    }

    public function index()
    {
        if (!$this->userService->is_admin()) {
            return view('home');
        }

        $users = $this->userService->get_users();
        $tournaments = $this->tournamentService->get_tournaments();

        return view('admin/admin')->with_input([
            'users' => $users,
            'tournaments' => $tournaments,
        ]);
    }
}