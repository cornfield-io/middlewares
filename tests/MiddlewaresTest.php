<?php

declare(strict_types=1);

namespace Cornfield\Middlewares\Tests;

use Cornfield\Middlewares\Exception\RuntimeException;
use Cornfield\Middlewares\Middlewares;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class MiddlewaresTest extends TestCase
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        $this->request = $this->createMock(ServerRequestInterface::class);
    }

    /**
     * @throws RuntimeException
     * @throws \ReflectionException
     */
    public function testUnshift(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('Hello, world!');
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);
        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware->method('process')->willReturn($response);

        $errorMiddleware = $this->createMock(MiddlewareInterface::class);
        $errorMiddleware->method('process')->willThrowException(new \Exception());

        $middlewares = new Middlewares();
        $middlewares->unshift($errorMiddleware);
        $middlewares->unshift($middleware);

        $this->assertSame('Hello, world!', $middlewares->handle($this->request)->getBody()->getContents());
    }

    /**
     * @throws RuntimeException
     * @throws \ReflectionException
     */
    public function testPush(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('Hello, world!');
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);
        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware->method('process')->willReturn($response);

        $errorMiddleware = $this->createMock(MiddlewareInterface::class);
        $errorMiddleware->method('process')->willThrowException(new \Exception());

        $middlewares = new Middlewares();
        $middlewares->push($middleware);
        $middlewares->push($errorMiddleware);

        $this->assertSame('Hello, world!', $middlewares->handle($this->request)->getBody()->getContents());
    }

    /**
     * @throws RuntimeException
     * @throws \ReflectionException
     */
    public function testHandleRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Middlewares must return a ResponseInterface!');

        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware->method('process')->willReturnCallback(
            function (ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
                return $handler->handle($request);
            }
        );

        $middlewares = new Middlewares();
        $middlewares->push($middleware);
        $this->assertSame('Hello, world!', $middlewares->handle($this->request)->getBody()->getContents());
    }
}
