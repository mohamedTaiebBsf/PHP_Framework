<?php

use App\Blog\DemoExtension;
use App\Blog\BlogModule;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use function DI\{add, get, create};

return [
    'blog.prefix' => '/blog',
    'twig.extensions' => add([
        get(DemoExtension::class)
    ]),
    BlogModule::class => create()->constructor(get('blog.prefix'), get(Router::class), get(RendererInterface::class))
];