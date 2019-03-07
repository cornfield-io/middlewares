<?php

declare(strict_types=1);

namespace Cornfield\Middlewares;

use Cornfield\Middlewares\Exception\RuntimeException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Middlewares implements RequestHandlerInterface
{
    /**
     * @var MiddlewareInterface[]
     */
    private $middlewares = [];

    /**
     * Prepend one or more MiddlewareInterface to the beginning of the Middlewares' array.
     *
     * @param MiddlewareInterface ...$middlewares
     */
    public function unshift(MiddlewareInterface ...$middlewares): void
    {
        array_unshift($this->middlewares, ...$middlewares);
    }

    /**
     * Push one or more MiddlewareInterface at the end of the Middlewares' array.
     *
     * @param MiddlewareInterface ...$middlewares
     */
    public function push(MiddlewareInterface ...$middlewares): void
    {
        foreach ($middlewares as $middleware) {
            $this->middlewares[] = $middleware;
        }
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

        return $middleware->process($request, $this);
    }
}
