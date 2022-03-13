<?php

namespace Yocto;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class App
{
    private RequestHandlerInterface $applicationStack;

    private function __construct(
        RequestHandlerInterface $kernel
    ) {
        $this->applicationStack = $kernel;
    }

    /**
     * @param MiddlewareInterface $middleware
     */
    public function use(MiddlewareInterface $middleware): void
    {
        $this->applicationStack = new Middleware($middleware, $this->applicationStack);
    }

    /**
     * @param  ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->applicationStack->handle($request);
    }
}
