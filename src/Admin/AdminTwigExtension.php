<?php

namespace App\Admin;

use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminTwigExtension extends AbstractExtension
{

    private $widgets;

    public function __construct(ContainerInterface $container)
    {
        $this->widgets = $container->get('admin.widgets');
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin_menu', [$this, 'renderMenu'], ['is_safe' => ['html']])
        ];
    }

    public function renderMenu(): string
    {
        return array_reduce(
            $this->widgets,
            function (string $html, AdminWidgetInterface $widget) {
                return $html . $widget->renderMenu();
            },
            ''
        );
    }
}
