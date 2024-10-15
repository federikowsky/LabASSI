<?php

namespace App\Middlewares;

class JWTMiddleware
{
    protected $container;

    public function __construct()
    {
    }

    public function handle(callable $next)
    {
        return $next();
    }
}

