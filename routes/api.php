<?php

return [
    'middleware' => ['EncryptCookiesMiddleware', 'JWTMiddleware'], // Middleware condiviso per tutte le rotte admin
    'routes' => [
        '/api/games' => [
            'controller' => 'GameController',
            'method' => 'games',
            'name' => 'api.games',
            'middleware' => []
        ],
        '/api/{name}_stats' => [
            'controller' => 'GameController',
            'method' => 'game_stats',
            'name' => 'api.game.stats',
            'middleware' => []
        ],
        '/api/{name}_stats_update' => [
            'controller' => 'GameController',
            'method' => 'game_stats_update',
            'name' => 'api.game.update',
            'middleware' => []
        ],
    ]
];