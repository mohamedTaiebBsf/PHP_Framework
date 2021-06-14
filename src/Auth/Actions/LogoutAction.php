<?php

namespace App\Auth\Actions;

use App\Auth\DatabaseAuth;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface as Request;

class LogoutAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var DatabaseAuth
     */
    private $auth;

    /**
     * @var FlashService
     */
    private $flash;

    public function __construct(RendererInterface $renderer, DatabaseAuth $auth, FlashService $flash)
    {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->flash = $flash;
    }

    public function __invoke(Request $request)
    {
        $this->auth->logout();
        $this->flash->success('Vous êtes maintenant déconnecté.');

        return new RedirectResponse('/');
    }
}
