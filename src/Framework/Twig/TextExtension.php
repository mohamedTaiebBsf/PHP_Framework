<?php

namespace Framework\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TextExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('excerpt', [$this, 'excerpt'])
        ];
    }

    public function excerpt(?string $content, int $maxLength = 100)
    {
        if (is_null($content)) {
            return '';
        }

        if (mb_strlen($content) > $maxLength) {
            $excerpt = mb_substr($content, 0, $maxLength);
            $lastSpace = mb_strrpos($excerpt, ' ');

            return mb_substr($excerpt, 0, $lastSpace) . '...';
        }
        return $content;
    }
}
