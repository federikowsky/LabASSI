<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\UserService;

class UserController extends BaseController
{
    protected $userService;
    protected $user;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->user = $this->userService->user();
    }

    public function index()
    {
        [$errors, $inputs] = session_flash('errors', 'inputs');
        return view('user/profile')->with_input([
            'username' => $this->user['username'],
            'email' => $this->user['email'],
            'errors' => $errors,
            'inputs' => $inputs,
        ]);
    }
}