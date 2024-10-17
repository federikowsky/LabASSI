<?php

return [
    'middleware' => ['EncryptCookiesMiddleware', 'AdminMiddleware'], // Middleware condiviso per tutte le rotte admin
    'routes' => [
        '/admin' => [
            'controller' => 'AdminController',
            'method' => 'index',
            'name' => 'admin.index',
            'middleware' => []
        ],
        '/admin/tournaments/create' => [
            'controller' => 'TournamentController',
            'method' => 'create_tournament',
            'name' => 'admin.tournaments.create',
            'middleware' => []
        ],
        '/admin/tournaments/start' => [
            'controller' => 'TournamentController',
            'method' => 'start_tournament',
            'name' => 'admin.tournaments.start',
            'middleware' => []
        ],
        '/admin/tournaments/delete' => [
            'controller' => 'TournamentController',
            'method' => 'delete_tournament',
            'name' => 'admin.tournaments.delete',
            'middleware' => []
        ],
    ]
];
