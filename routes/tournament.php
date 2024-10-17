<?php

return [
    'middleware' => ['EncryptCookiesMiddleware', 'AuthMiddleware'], // Middleware condiviso per tutte le rotte admin
    'routes' => [
        '/tournaments/{id}/subscribe' => [
            'controller' => 'TournamentController',
            'method' => 'subscribe',
            'name' => 'tournaments.subscribe',
            'middleware' => []
        ],
        '/tournaments/{id}/unsubscribe' => [
            'controller' => 'TournamentController',
            'method' => 'unsubscribe',
            'name' => 'tournaments.unsubscribe',
            'middleware' => []
        ],
        '/tournaments/{id}/dashboard' => [
            'controller' => 'TournamentController',
            'method' => 'dashboard',
            'name' => 'tournaments.dashboard',
            'middleware' => []
        ],
        '/tournaments/{id}/results' => [
            'controller' => 'TournamentController',
            'method' => 'dashboard',
            'name' => 'tournaments.results',
            'middleware' => []
        ],
        '/tournaments/{id}/match/{id}/play' => [
            'controller' => 'TournamentController',
            'method' => 'play',
            'name' => 'tournaments.match.play',
            'middleware' => []
        ],
        '/tournaments/{id}/match/{id}/result' => [
            'controller' => 'TournamentController',
            'method' => 'result',
            'name' => 'tournaments.match.result',
            'middleware' => []
        ],
    ]
];
