<?php

namespace Framework;

use Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;

/**
 * Class Router
 *
 * Register and match routes
 */
class Router
{
    private $router;

    public function __construct()
    {
        $this->router = new FastRouteRouter();
    }

    /**
     * @param string $path
     * @param $callback
     * @param string $name
     */
    public function get(string $path, $callback, ?string $name = null)
    {
        $this->router->addRoute(new ZendRoute($path, $callback, ['GET'], $name));
    }

    /**
     * @param string $path
     * @param $callback
     * @param string|null $name
     */
    public function post(string $path, $callback, ?string $name = null)
    {
        $this->router->addRoute(new ZendRoute($path, $callback, ['POST'], $name));
    }

    /**
     * @param string $path
     * @param $callback
     * @param string|null $name
     */
    public function delete(string $path, $callback, ?string $name = null)
    {
        $this->router->addRoute(new ZendRoute($path, $callback, ['DELETE'], $name));
    }

    /**
     * Générer les routes du CRUD
     *
     * @param string $prefixPath
     * @param $callback
     * @param string $prefixName
     */
    public function crud(string $prefixPath, $callback, string $prefixName)
    {
        $this->get("$prefixPath", $callback, $prefixName . '.index');
        $this->get("$prefixPath/new", $callback, $prefixName . '.create');
        $this->post("$prefixPath/new", $callback);
        $this->get("$prefixPath/{id:\d+}", $callback, $prefixName . '.edit');
        $this->post("$prefixPath/{id:\d+}", $callback);
        $this->delete("$prefixPath/{id:\d+}", $callback, $prefixName . '.delete');
    }

    /**
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->router->match($request);

        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),
                $result->getMatchedMiddleware(),
                $result->getMatchedParams()
            );
        }

        return null;
    }

    public function generateUri(string $name, array $params = [], array $queryParams = []): ?string
    {
        $uri = $this->router->generateUri($name, $params);
        if (!empty($queryParams)) {
            $uri .= '?' . http_build_query($queryParams);
        }

        return $uri;
    }
}
