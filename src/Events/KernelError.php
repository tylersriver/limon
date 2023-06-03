<?php

namespace Yocto\Events;

use Psr\Http\Message\ServerRequestInterface;
use Yocto\Action;

class KernelError extends KernelEvent
{
    public function __construct(
        ServerRequestInterface $request,
        private ?Action $handler = null,
    ) {
        parent::__construct($request);
    }

    public function getHandler(): ?Action
    {
        return $this->handler;
    }
}