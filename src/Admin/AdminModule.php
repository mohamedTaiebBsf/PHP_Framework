<?php

namespace App\Admin;

use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

class AdminModule extends Module
{
    const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(ContainerInterface $container)
    {
        $renderer = $container->get(RendererInterface::class);
        $renderer->addPath('admin', __DIR__ . '/views');
        $router = $container->get(Router::class);
        $router->get($container->get('admin.prefix'), DashboardAction::class, 'admin');

        if ($renderer instanceof RendererInterface) {
            $renderer->getTwig()->addExtension($container->get(AdminTwigExtension::class));
        }
    }
}
