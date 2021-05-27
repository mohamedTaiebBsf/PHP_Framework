<?php

namespace Tests\Blog\Actions;

use App\Blog\Actions\PostShowAction;
use App\Blog\Entity\Post;
use App\blog\Table\PostTable;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest as Request;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class PostShowActionTest extends TestCase
{
    private $action;
    private $renderer;
    private $router;
    private $postTable;

    use ProphecyTrait;

    protected function setUp(): void
    {
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->router = $this->prophesize(Router::class);
        $this->postTable = $this->prophesize(PostTable::class);

        $this->action = new PostShowAction(
            $this->renderer->reveal(),
            $this->router->reveal(),
            $this->postTable->reveal()
        );
    }

    private function makePost(int $id, string $slug): Post
    {
        $post = new Post();
        $post->id = $id;
        $post->slug = $slug;
        return $post;
    }

    public function testShowRedirect()
    {
        $post = $this->makePost(9, 'azerty');
        $request = (new Request('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', 'demo');

        $this->router->generateUri('blog.show', ['id' => $post->id, 'slug' => $post->slug])->willReturn('/demo2');

        $this->postTable->findWithCategory($post->id)->willReturn($post);
        $response = call_user_func_array($this->action, [$request]);

        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals(['/demo2'], $response->getHeader('location'));
    }

    public function testShowRender()
    {
        $post = $this->makePost(9, 'azerty');
        $request = (new Request('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', $post->slug);

        $this->postTable->findWithCategory($post->id)->willReturn($post);
        $this->renderer->render('@blog/show', ['post' => $post])->willReturn('');
        call_user_func_array($this->action, [$request]);
        $this->assertEquals(true, true);
    }
}