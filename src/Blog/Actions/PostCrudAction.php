<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\PostUpload;
use App\blog\Table\CategoryTable;
use App\blog\Table\PostTable;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostCrudAction extends CrudAction
{
    protected $viewPath = "@blog/admin/posts";

    protected $rootPrefix = "blog.admin";

    protected $messages = [
        'create' => 'L\article a bien été créé',
        'edit' => 'L\article a bien été modifié'
    ];

    /**
     * @var CategoryTable
     */
    private CategoryTable $categoryTable;

    /**
     * @var PostUpload
     */
    private $postUpload;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $table,
        FlashService $flash,
        CategoryTable $categoryTable,
        PostUpload $postUpload
    ) {
        parent::__construct($renderer, $router, $table, $flash);
        $this->categoryTable = $categoryTable;
        $this->postUpload = $postUpload;
    }

    protected function getNewEntity()
    {
        $post = new Post();
        $post->created_at = new \DateTime();

        return $post;
    }

    /**
     * @param Request $request
     * @param Post $post
     * @return array
     */
    protected function getParams(Request $request, $post): array
    {
        $params = array_merge($request->getParsedBody(), $request->getUploadedFiles());
        //Upload File
        $params['image'] = $this->postUpload->upload($params['image'], $post->image);
        $params = array_filter($params, function ($key) {
            return in_array($key, ['name', 'slug', 'content', 'created_at', 'category_id', 'image']);
        }, ARRAY_FILTER_USE_KEY);

        return array_merge($params, [
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function getValidator(Request $request)
    {
        $validator =  parent::getValidator($request)
            ->required('content', 'name', 'slug', 'created_at', 'category_id')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->exists('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo())
            ->datetime('created_at')
            ->extension('image', ['jpg', 'png'])
            ->slug('slug');

        if (is_null($request->getAttribute('id'))) {
            $validator->uploaded('image');
        }

        return $validator;
    }

    protected function formParams(array $params): array
    {
        $params['categories'] = $this->categoryTable->findList();

        return $params;
    }
}
