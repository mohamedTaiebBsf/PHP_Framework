<?php

namespace Tests\Framework\Middleware;

use Framework\Middleware\MethodMiddleware;
use GuzzleHttp\Psr7\ServerRequest as Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MethodMiddlewareTest extends TestCase
{
    /**
     * @var MethodMiddleware
     */
    private $middleware;

    protected function setUp(): void
    {
        $this->middleware = new MethodMiddleware();
    }

    public function testAddMethod()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handler->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($request) {
                return $request->getMethod() === 'DELETE';
            }));
        $request = (new Request('POST', '/demo'))
            ->withParsedBody(['_method' => 'DELETE']);

        $this->middleware->process($request, $handler);

//        call_user_func_array($this->middleware, [$request, function (ServerRequestInterface $request) {
//            $this->assertEquals('DELETE', $request->getMethod());
//        }]);
    }
}