<?php

namespace Framework\Middleware;

use Framework\Exception\CsrfInvalidException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfMiddleware implements MiddlewareInterface
{
    /**
     * @var string
     */
    private $formKey;

    /**
     * @var string
     */
    private $sessionKey;

    /**
     * @var array
     */
    private $session;

    /**
     * @var int
     */
    private $limit;

    public function __construct($session, int $limit = 50, string $formKey = '_csrf', string $sessionKey = 'csrf')
    {
        $this->validSession($session);
        $this->session = $session;
        $this->formKey = $formKey;
        $this->sessionKey = $sessionKey;
        $this->limit = $limit;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'])) {
            $params = $request->getParsedBody() ?: [];

            if (!array_key_exists($this->formKey, $params)) {
                $this->reject();
            } else {
                $csrfList = $this->session[$this->sessionKey] ?? [];
                if (in_array($params[$this->formKey], $csrfList)) {
                    $this->useToken($params[$this->formKey]);
                    return $handler->handle($request);
                } else {
                    $this->reject();
                }
            }
        } else {
            return $handler->handle($request);
        }
    }

    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(16));
        $csrfList = $this->session[$this->sessionKey] ?? [];
        $csrfList[] = $token;
        $this->session[$this->sessionKey] = $csrfList;
        $this->limitTokens();

        return $token;
    }

    /**
     * @return string
     */
    public function getFormKey(): string
    {
        return $this->formKey;
    }

    private function reject(): void
    {
        throw new CsrfInvalidException();
    }

    private function useToken($token): void
    {
        $tokens = array_filter($this->session[$this->sessionKey], function ($t) use ($token) {
            return $token !== $t;
        });

        $this->session[$this->sessionKey] = $tokens;
    }

    private function limitTokens()
    {
        $tokens = $this->session[$this->sessionKey] ?? [];

        if (count($tokens) > $this->limit) {
            array_shift($tokens);
        }

        $this->session[$this->sessionKey] = $tokens;
    }

    private function validSession($session)
    {
        if (!is_array($session) && !$session instanceof \ArrayAccess) {
            throw new \TypeError("La session passé au middleware CSRF n'est pas traitable comme un tableau.");
        }
    }
}
