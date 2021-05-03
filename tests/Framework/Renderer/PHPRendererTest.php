<?php

namespace Tests\Framework;

use Framework\Renderer\PHPRenderer;
use PHPUnit\Framework\TestCase;

class PHPRendererTest extends TestCase
{
    private $renderer;

    public function setUp(): void
    {
        $this->renderer = new PHPRenderer(dirname(__DIR__) . '/views');
    }

    public function testRenderTheRightPath()
    {
        $this->renderer->addPath('blog', dirname(__DIR__) . '/views');
        $content = $this->renderer->render('@blog/demo');
        $this->assertEquals('Salut les gens', $content);
    }

    public function testRenderDefaultPath()
    {
        $content = $this->renderer->render('demo');
        $this->assertEquals('Salut les gens', $content);
    }

    public function testRenderWithParams()
    {
        $content = $this->renderer->render('demoparams', [
            'nom' => 'Marc'
        ]);
        $this->assertEquals('Salut Marc', $content);
    }

    public function testGlobalParameters()
    {
        $this->renderer->addGlobal('nom', 'Marc');
        $content = $this->renderer->render('demoparams', [
            'nom' => 'Marc'
        ]);
        $this->assertEquals('Salut Marc', $content);
    }
}