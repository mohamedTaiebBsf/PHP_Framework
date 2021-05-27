<?php

namespace App\Blog\Actions;

use App\blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostShowAction
{
    private $renderer;
    private $postTable;
    private $router;

    use RouterAwareAction;

    /**
     * PostShowAction constructor.
     *
     * @param RendererInterface $renderer
     * @param Router $router
     * @param PostTable $postTable
     */
    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $postTable
    ) {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->router = $router;
    }

    public function __invoke(Request $request)
    {
        $slug = $request->getAttribute('slug');
        $post = $this->postTable->findWithCategory($request->getAttribute('id'));

        if ($post->slug !== $slug) {
            return $this->redirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }

        return $this->renderer->render('@blog/show', compact('post'));
    }
}
