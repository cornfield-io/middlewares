<?php

declare(strict_types=1);

namespace Cornfield\Middlewares\Tests;

use Cornfield\Middlewares\Exception\RuntimeException;
use Cornfield\Middlewares\Middlewares;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionException;

final class MiddlewaresTest extends TestCase implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->method('get')->willReturn($this);
        $this->request = $this->createMock(ServerRequestInterface::class);
    }

    /**
     * @throws ReflectionException
     * @throws RuntimeException
     */
    public function testMiddlewares(): void
    {
        $handler = new Middlewares($this->container);
        $handler->push([$this->getMockMiddlewareTextBefore('1'), $this->getMockMiddlewareTextBefore('2')]);
        $handler->push($this->getMockMiddlewareTextBefore('3'));
        $handler->unshift($this->getMockMiddlewareTextAfter('A'));
        $handler->unshift([$this->getMockMiddlewareTextAfter('C'), $this->getMockMiddlewareTextAfter('B')]);
        $handler->push('MiddlewaresTest');

        $this->assertSame('123Hello, world!ABC', $handler->handle($this->request)->getBody()->getContents());
    }

    /**
     * @throws RuntimeException
     * @throws ReflectionException
     */
    public function testHandleRuntimeExceptionResponseInterface(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Middlewares must return a ResponseInterface!');

        $handler = new Middlewares($this->container);
        $handler->push([$this->getMockMiddlewareTextBefore('1'), $this->getMockMiddlewareTextBefore('2')]);
        $handler->push($this->getMockMiddlewareTextBefore('3'));
        $handler->unshift($this->getMockMiddlewareTextAfter('A'));
        $handler->unshift([$this->getMockMiddlewareTextAfter('C'), $this->getMockMiddlewareTextAfter('B')]);
        $handler->handle($this->request);
    }

    /**
     * @throws RuntimeException
     */
    public function testHandleRuntimeExceptionMiddlewareInterface(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Middlewares must be a instance of MiddlewareInterface!');

        $handler = new Middlewares();
        $handler->push('MiddlewaresTest');
        $handler->handle($this->request);
    }

    /**
     * @param string $value
     *
     * @return MiddlewareInterface
     *
     * @throws \ReflectionException
     */
    private function getMockMiddlewareTextBefore(string $value): MiddlewareInterface
    {
        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware->method('process')->willReturnCallback(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($value): ResponseInterface {
            $content = $handler->handle($request)->getBody()->getContents();

            $stream = $this->createMock(StreamInterface::class);
            $stream->method('getContents')->willReturn($value.$content);
            $response = $this->createMock(ResponseInterface::class);
            $response->method('getBody')->willReturn($stream);

            /* @var ResponseInterface $response */
            return $response;
        });

        /* @var MiddlewareInterface $middleware */
        return $middleware;
    }

    /**
     * @param string $value
     *
     * @return MiddlewareInterface
     *
     * @throws \ReflectionException
     */
    private function getMockMiddlewareTextAfter(string $value): MiddlewareInterface
    {
        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware->method('process')->willReturnCallback(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($value): ResponseInterface {
            $content = $handler->handle($request)->getBody()->getContents();

            $stream = $this->createMock(StreamInterface::class);
            $stream->method('getContents')->willReturn($content.$value);
            $response = $this->createMock(ResponseInterface::class);
            $response->method('getBody')->willReturn($stream);

            /* @var ResponseInterface $response */
            return $response;
        });
        /* @var MiddlewareInterface $middleware */
        return $middleware;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     *
     * @throws ReflectionException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('Hello, world!');
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);

        /* @var ResponseInterface $response */
        return $response;
    }
}
