<?php

namespace Framework\Actions;

use Framework\Database\Table;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class CrudAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var mixed
     */
    protected $table;

    /**
     * @var FlashService
     */
    private $flash;

    /**
     * @var string
     */
    protected $viewPath;

    /**
     * @var string
     */
    protected $rootPrefix;

    protected $messages = [
        'create' => 'L\élément a bien été créé',
        'edit' => 'L\élément a bien été modifié'
    ];

    use RouterAwareAction;

    /**
     * PostCrudAction constructor.
     *
     * @param RendererInterface $renderer
     * @param Router $router
     * @param Table $table
     * @param FlashService $flash
     */
    public function __construct(
        RendererInterface $renderer,
        Router $router,
        Table $table,
        FlashService $flash
    ) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->table = $table;
        $this->flash = $flash;
    }

    /**
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function __invoke(Request $request)
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('rootPrefix', $this->rootPrefix);

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
     * Affiche la liste des éléments
     *
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->table->findPaginated(12, $params['p'] ?? 1);

        return $this->renderer->render($this->viewPath . '/index', compact('items'));
    }

    /**
     * Editer un élément
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function edit(Request $request): string|ResponseInterface
    {
        $item = $this->table->find($request->getAttribute('id'));

        if ($request->getMethod() === "POST") {
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);

            if ($validator->isValid()) {
                $this->table->update($item->id, $params);
                $this->flash->success($this->messages['edit']);
                return $this->redirect($this->rootPrefix . '.index');
            }
            $errors = $validator->getErrors();
        }

        $params = $this->formParams([
            'item' => $item,
            'errors' => $errors ?? null
        ]);

        return $this->renderer->render($this->viewPath . '/edit', $params);
    }

    /**
     * Créer un élément
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function create(Request $request): string|ResponseInterface
    {
        $item = $this->getNewEntity();

        if ($request->getMethod() === "POST") {
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);

            if ($validator->isValid()) {
                $this->table->insert($params);
                $this->flash->success($this->messages['create']);
                return $this->redirect($this->rootPrefix . '.index');
            }
            $errors = $validator->getErrors();
        }

        $params = $this->formParams([
            'item' => $item,
            'errors' => $errors ?? null
        ]);

        return $this->renderer->render($this->viewPath . '/create', $params);
    }

    /**
     * Supprimer un article
     *
     * @param Request $request
     * @return ResponseInterface
     */
    public function delete(Request $request): ResponseInterface
    {
        $this->table->delete($request->getAttribute('id'));

        return $this->redirect($this->rootPrefix . '.index');
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getParams(Request $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, []);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function getValidator(Request $request)
    {
        return new Validator($request->getParsedBody());
    }

    protected function getNewEntity()
    {
        return [];
    }

    /**
     * Permet de traiter les paramètres à envoyer à la vue
     *
     * @param $params
     * @return array
     */
    protected function formParams(array $params): array
    {
        return $params;
    }
}
