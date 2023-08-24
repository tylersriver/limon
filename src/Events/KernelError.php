<?php

namespace Yocto\Events;

use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Yocto\Action;

class KernelError extends KernelEvent
{
    public function __construct(
        ServerRequestInterface $request,
        private Throwable $error,
    ) {
        parent::__construct($request);
    }

    public function getError(): Throwable
    {
        return $this->error;
    }
}