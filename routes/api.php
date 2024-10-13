<?php

return [
    'middleware' => ['EncryptCookiesMiddleware'], // Middleware condiviso per tutte le rotte admin
    'routes' => [
        '/api/get_games' => [
            'controller' => 'GameController',
            'method' => 'get_games',
            'name' => 'api.get_games',
            'middleware' => []
        ],
    ]
];
