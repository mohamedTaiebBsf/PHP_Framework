<?php

namespace Tests\Framework\Middleware;

use Framework\Exception\CsrfInvalidException;
use Framework\Middleware\CsrfMiddleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest as Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfMiddlewareTest extends TestCase
{
    /**
     * @var CsrfMiddleware
     */
    private $middleware;

    private $session;

    protected function setUp(): void
    {
        $this->session = new \ArrayObject();
        $this->middleware = new CsrfMiddleware($this->session);
    }

    public function testLetGetRequestPass()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response());

        $request = new Request('GET', '/demo');

        $this->middleware->process($request, $handler);
    }

    public function testBlockPostRequestWithoutCsrf()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handler->expects($this->never())->method('handle');

        $request = new Request('POST', '/demo');
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $handler);
    }

    public function testBlockPostRequestWithInvalidCsrf()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handler->expects($this->never())->method('handle');

        $this->middleware->generateToken();
        $request = new Request('POST', '/demo');
        $request = $request->withParsedBody(['_csrf' => 'azerty']);
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $handler);
    }

    public function testLetPostWithTokenPass()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response());

        $request = new Request('POST', '/demo');
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $handler);
    }

    public function testLetPostWithTokenPassOnce()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response());

        $request = new Request('POST', '/demo');
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $handler);
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $handler);
    }

    public function testLimitTheTokenNumber()
    {
        for ($i = 0; $i < 100; $i++) {
            $token = $this->middleware->generateToken();
        }
        $this->assertCount(50, $this->session['csrf']);
        $this->assertEquals($token, $this->session['csrf'][49]);
    }
}