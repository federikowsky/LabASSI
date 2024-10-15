<?php

return [
    'middleware' => ['EncryptCookiesMiddleware', 'CSRFMiddleware'], // Middleware condiviso per tutte le rotte auth
    'routes' => [
        '/auth/login' => [
            'controller' => 'AuthController',
            'method' => 'login',
            'name' => 'auth.login',
            'middleware' => []
        ],
        '/auth/logout' => [
            'controller' => 'AuthController',
            'method' => 'logout',
            'name' => 'auth.logout',
            'middleware' => []
        ],
        '/auth/register' => [
            'controller' => 'AuthController',
            'method' => 'register',
            'name' => 'auth.register',
            'middleware' => []
        ],
        '/auth/activate' => [
            'controller' => 'AuthController',
            'method' => 'activate',
            'name' => 'auth.activate',
            'middleware' => ['EWTMiddleware']
        ],
        '/auth/password/forgot' => [
            'controller' => 'AuthController',
            'method' => 'forgot_password',
            'name' => 'auth.forgot_password',
            'middleware' => []
        ],
        '/auth/password/reset' => [
            'controller' => 'AuthController',
            'method' => 'reset_password',
            'name' => 'auth.reset_password',
            'middleware' => ['EWTMiddleware']
        ],
        '/auth/password/update' => [
            'controller' => 'AuthController',
            'method' => 'update_password',
            'name' => 'auth.update_password',
            'middleware' => []
        ],
        '/auth/{provider}' => [
            'controller' => 'OAuthController',
            'method' => 'redirect_to_provider',
            'name' => 'auth.provider.redirect',
            'middleware' => []
        ],
        '/auth/{provider}/callback' => [
            'controller' => 'OAuthController',
            'method' => 'handle_provider_callback',
            'name' => 'auth.provider.callback',
            'middleware' => []
        ],
    ]
];
