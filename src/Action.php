<?php

namespace Yocto;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Action
{
    protected ServerRequestInterface $request;

    protected Container $container;

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;
        return $this->action();
    }

    abstract public function action(): ResponseInterface;

    abstract public function validate(): bool;
}
