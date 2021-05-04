<?php

namespace App\Blog;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DemoExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('demo', [$this, 'demo'])
        ];
    }

    public function demo(): string
    {
        return 'Salut';
    }
}
