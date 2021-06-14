<?php

namespace App\Admin;

use Framework\Renderer\RendererInterface;
use Psr\Container\ContainerInterface;

class DashboardAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke()
    {
        $widgets = array_reduce(
            $this->container->get('admin.widgets'),
            function (string $html, AdminWidgetInterface $widget) {
                return $html . $widget->render();
            },
            ''
        );

        return $this->container->get(RendererInterface::class)
            ->render('@admin/dashboard', compact('widgets'));
    }
}
