<?php

namespace Yocto\Events;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class KernelEvent
{
    public function __construct(
        public readonly ServerRequestInterface $request,
        public readonly ?ResponseInterface $response = null
    ) {
    }
}