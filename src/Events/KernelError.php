<?php

namespace Yocto\Events;

use Psr\Http\Message\ServerRequestInterface;
use Yocto\Action\ActionInterface;

class KernelError extends KernelEvent
{
    public function __construct(
        ServerRequestInterface $request,
        private ?ActionInterface $handler = null,
    ) {
        parent::__construct($request);
    }

    public function getHandler(): ?ActionInterface
    {
        return $this->handler;
    }
}