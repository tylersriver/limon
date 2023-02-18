<?php

namespace Yocto\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class BaseAction implements ActionInterface
{
    protected ServerRequestInterface $request;

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;
        return $this->action();
    }

    abstract public function action(): ResponseInterface;

    abstract public function validate(): bool;

    abstract public function access(): bool;
}
