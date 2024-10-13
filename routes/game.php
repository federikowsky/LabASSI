<?php

return [
    'middleware' => ['EncryptCookiesMiddleware'], // Middleware condiviso per tutte le rotte admin
    'routes' => [
        '/game/{name}' => [
            'controller' => 'GameController',
            'method' => 'index',
            'name' => 'game.index',
            'middleware' => []
        ],
        '/game/ranking' => [
            'controller' => 'GameController',
            'method' => 'ranking',
            'name' => 'game.ranking',
            'middleware' => []
        ],
    ]
];
