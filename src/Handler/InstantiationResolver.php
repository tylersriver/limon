<?php

namespace Yocto\Handler;

use Yocto\Action;
use Yocto\Handler\Exception\HandlerNotFoundException;
use Yocto\Handler\Exception\InvalidHandlerException;
use Yocto\Handler\HandlerResolverInterface;

class InstantiationResolver implements HandlerResolverInterface
{
    public function resolve(string $handler): Action
    {
        if (!class_exists($handler)) {
            throw new HandlerNotFoundException($handler);
        }

        if (!is_subclass_of($handler, Action::class)) {
            throw new InvalidHandlerException();
        }

        return new $handler();
    }
}