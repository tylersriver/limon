<?php

namespace Yocto;

abstract class Middleware
{
    protected Middleware $next;

    abstract public function process(Request $request): Response;

    public function setNext(Middleware $middleware): void
    {
        $this->next = $middleware;
    }
}