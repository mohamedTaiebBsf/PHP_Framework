<?php

use App\Blog\DemoExtension;

use function DI\add;
use function DI\get;

return [
    'blog.prefix' => '/blog',
    'twig.extensions' => add([
        get(DemoExtension::class),
    ])
];
