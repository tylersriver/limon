<?php

namespace Yocto\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ActionInterface
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface;
}