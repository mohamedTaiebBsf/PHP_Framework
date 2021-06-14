<?php

use App\Auth\DatabaseAuth;
use App\Auth\Middleware\ForbiddenMiddleware;
use App\Auth\Twig\AuthTwigExtension;
use Framework\Auth;
use Framework\Session\PHPSession;
use function DI\get;

return [
    'auth.login' => '/login',
    'twig.extensions'=>\DI\add([
        get(AuthTwigExtension::class)
    ]),
    Auth::class => \DI\get(DatabaseAuth::class),
    ForbiddenMiddleware::class => \DI\create()->constructor(
        \DI\get('auth.login'),
        \DI\get(PHPSession::class)
    )
];
