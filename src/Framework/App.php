<?php

namespace Framework;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class App implements RequestHandlerInterface
{
    /**
     * List of modules
     *
     * @var array|mixed
     */
    private $modules = [];

    /**
     * @var string
     */
    private $definition;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string[]
     */
    private $middlewares;

    /**
     * @var int
     */
    private $index = 0;

    public function __construct(string $definition)
    {
        $this->definition = $definition;
    }

    /**
     * Ajoute un module à l'application
     *
     * @param string $module
     * @return $this
     */
    public function addModule(string $module): self
    {
        $this->modules[] = $module;

        return $this;
    }

    /**
     * Ajoute un middleware
     *
     * @param string $middleware
     * @return $this
     */
    public function pipe(string $middleware): self
    {
        $this->middlewares[] = $middleware;

        return $this;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();

        if (is_null($middleware)) {
            throw new \Exception('Aucun middleware n\'a intercepté cette requête.');
        } elseif (is_callable($middleware)) {
            return call_user_func_array($middleware, [$request, [$this, 'handle']]);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }

        return $this->handle($request);
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();
            $env = getenv('ENV') ?: 'production';

            if ($env === 'production') {
                $builder->enableCompilation('tmp/di');
                $builder->writeProxiesToFile(true, 'tmp/proxies');
            }
            $builder->addDefinitions($this->definition);
            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }
            $this->container = $builder->build();
        }

        return $this->container;
    }

    private function getMiddleware()
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            $middleware = $this->container->get($this->middlewares[$this->index]);
            $this->index++;

            return $middleware;
        }

        return null;
    }

    /**
     * @return array|mixed
     */
    public function getModules()
    {
        return $this->modules;
    }
}
