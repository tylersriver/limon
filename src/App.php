<?php

namespace Limon;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class App
{
    public function __construct(
        private RequestHandlerInterface $applicationEntry
    ) {
    }

    /**
     * @param MiddlewareInterface $middleware
     */
    public function use(MiddlewareInterface $middleware): void
    {
        $this->applicationEntry = new Middleware($middleware, $this->applicationEntry);
    }

    /**
     * @param  ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->applicationEntry->handle($request);
    }
}
