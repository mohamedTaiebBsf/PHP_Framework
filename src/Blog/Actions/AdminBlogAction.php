<?php

namespace App\Blog\Actions;

use App\blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Framework\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminBlogAction
{
    private $renderer;
    private $router;
    private $postTable;
    private $flash;

    use RouterAwareAction;

    /**
     * AdminBlogAction constructor.
     *
     * @param RendererInterface $renderer
     * @param Router $router
     * @param PostTable $postTable
     * @param FlashService $flash
     */
    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $postTable,
        FlashService $flash
    ) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postTable = $postTable;
        $this->flash = $flash;
    }

    /**
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function __invoke(Request $request)
    {
        if ($request->getMethod() === "DELETE") {
            return $this->delete($request);
        }
        if (substr((string)$request->getUri(), -3) === 'new') {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        return $this->index($request);
    }

    /**
     * Récupérer tous les article
     *
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->postTable->findPaginated(12, $params['p'] ?? 1);

        return $this->renderer->render('@blog/admin/index', compact('items'));
    }

    /**
     * Editer l'article
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function edit(Request $request): string|ResponseInterface
    {
        $item = $this->postTable->find($request->getAttribute('id'));

        if ($request->getMethod() === "POST") {
            $params = $this->getParams($request);
            $params['updated_at'] = date('Y-m-d H:i:s');
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->postTable->update($item->id, $params);
                $this->flash->success('L\'article a bien été modifié.');
                return $this->redirect('blog.admin.index');
            }
            $errors = $validator->getErrors();
        }

        return $this->renderer->render('@blog/admin/edit', [
            'item' => $item,
            'errors' => $errors ?? null
        ]);
    }

    /**
     * Créer un article
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function create(Request $request): string|ResponseInterface
    {
        if ($request->getMethod() === "POST") {
            $params = $this->getParams($request);
            $params = array_merge($params, [
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->postTable->insert($params);
                $this->flash->success('L\'article a bien été créé.');
                return $this->redirect('blog.admin.index');
            }
            $errors = $validator->getErrors();
        }

        return $this->renderer->render('@blog/admin/create', [
            'errors' => $errors ?? null
        ]);
    }

    /**
     * Supprimer un article
     *
     * @param Request $request
     * @return ResponseInterface
     */
    public function delete(Request $request): ResponseInterface
    {
        $this->postTable->delete($request->getAttribute('id'));

        return $this->redirect('blog.admin.index');
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getParams(Request $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content']);
        }, ARRAY_FILTER_USE_KEY);
    }

    private function getValidator(Request $request)
    {
        return (new Validator($request->getParsedBody()))
            ->required('content', 'name', 'slug')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->slug('slug');
    }
}
