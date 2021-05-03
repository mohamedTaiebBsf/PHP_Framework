<?php
namespace Framework\Renderer;

interface RendererInterface
{
    /**
     * Add new path to load views
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void;

    /**
     * Render the View
     * Path can be more detail by adding a namespace using addPath()
     * $this->render('@blog/index')
     * $this->render('view')
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string;

    /**
     * Add global variable that can be accessed by any views
     * @param string $key
     * @param mixed $value
     */
    public function addGlobal(string $key, $value): void;
}
