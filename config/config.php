<?php

use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router;
use function DI\get;

return [
    'views.path' => dirname(__DIR__) . '/views',
    'twig.extensions' => [
        get(Router\RouterTwigExtension::class)
    ],
    Router::class => \DI\create(),
    RendererInterface::class => \DI\factory(TwigRendererFactory::class),
];