<?php

return [
    'middleware' => ['EncryptCookiesMiddleware'], // Middleware condiviso per tutte le rotte admin
    'routes' => [
        '/' => [
            'controller' => 'HomeController',
            'method' => 'index',
            'name' => 'home',
            'middleware' => []
        ],
        '/about-us' => [
            'controller' => 'HomeController',
            'method' => 'about_us',
            'name' => 'about_us',
            'middleware' => []
        ],
    ]
];
