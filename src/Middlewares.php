<?php

declare(strict_types=1);

namespace Cornfield\Middlewares;

use Cornfield\Middlewares\Exception\RuntimeException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Middlewares implements RequestHandlerInterface
{
    /**
     * @var ?ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $middlewares = [];

    /**
     * Middlewares constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(?ContainerInterface $container = null)
    {
        if (null !== $container) {
            $this->container = $container;
        }
    }

    /**
     * Prepend one or more MiddlewareInterface to the beginning of the Middlewares' array.
     *
     * @param string|string[]|MiddlewareInterface|MiddlewareInterface[] $middlewares
     */
    public function unshift($middlewares): void
    {
        if (is_array($middlewares)) {
            array_unshift($this->middlewares, ...$middlewares);

            return;
        }

        array_unshift($this->middlewares, $middlewares);
    }

    /**
     * Push one or more MiddlewareInterface at the end of the Middlewares' array.
     *
     * @param string|string[]|MiddlewareInterface|MiddlewareInterface[] $middlewares
     */
    public function push($middlewares): void
    {
        if (is_array($middlewares)) {
            array_push($this->middlewares, ...$middlewares);

            return;
        }

        $this->middlewares[] = $middlewares;
    }

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = array_shift($this->middlewares);

        if (null === $middleware) {
            throw new RuntimeException('Middlewares must return a ResponseInterface!');
        }

        if (false === ($middleware instanceof MiddlewareInterface)) {
            if (null === $this->container) {
                throw new RuntimeException('Middlewares must be a instance of MiddlewareInterface!');
            }

            $middleware = $this->container->get($middleware);
        }

        return $middleware->process($request, $this);
    }
}
