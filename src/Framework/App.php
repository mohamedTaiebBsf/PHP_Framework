<?php

namespace Framework;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class App
{
    /**
     * List of modules
     *
     * @var array|mixed
     */
    private $modules = [];

    /**
     * Router
     *
     * @var Router
     */
    private $router;

    /**
     * App constructor.
     * @param string[] $modules Liste des modules à charger
     */
    public function __construct(array $modules = [], array $dependecies = [])
    {
        $this->router = new Router();
        if (array_key_exists('renderer', $dependecies)) {
            $dependecies['renderer']->addGlobal('router', $this->router);
        }
        foreach ($modules as $module) {
            $this->modules[] = new $module($this->router, $dependecies['renderer']);
        }
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri()->getPath();

        if (!empty($uri) && $uri[-1] === '/') {
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
        }

        $route = $this->router->match($request);

        if (is_null($route)) {
            return new Response(404, [], '<h1>Error 404</h1>');
        }

        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);

        $response = call_user_func_array($route->getCallback(), [$request]);

        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof Response) {
            return $response;
        } else {
            throw new \Exception('The response is not a string or an instance of ResponseInterface.');
        }
    }
}
