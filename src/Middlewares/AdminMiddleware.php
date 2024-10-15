<?php
// src/Middleware/authmiddleware.php

namespace App\Middlewares;

use App\Services\UserService;

class AdminMiddleware
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function handle(callable $next)
    {
        // Verifica se l'utente è loggato
        if (!$this->userService->is_admin()) {
            // L'utente non è autenticato, quindi reindirizza al login
            return redirect('/');
        }

        // L'utente è autenticato, quindi esegui il prossimo step della richiesta
        // call_user_func($next);
        return $next();
    }
}