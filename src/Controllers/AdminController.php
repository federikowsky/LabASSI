<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\UserService;

class AdminController extends BaseController
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        if (!$this->userService->is_admin()) {
            return view('home');
        }

        return view('admin');
    }
}