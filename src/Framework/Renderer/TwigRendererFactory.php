<?php

namespace Framework\Renderer;

use Framework\Router\RouterTwigExtension;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRendererFactory
{
    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        $loader = new FilesystemLoader($container->get('views.path'));
        $twig = new Environment($loader);
        if ($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }
//        $twig->addExtension($container->get(RouterTwigExtension::class));

        return new TwigRenderer($loader, $twig);
    }
}
