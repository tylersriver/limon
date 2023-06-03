<?php

namespace Yocto\Handler;

use Yocto\Action;
use Yocto\Handler\Exception\InvalidHandlerException;
use Yocto\Handler\Exception\HandlerNotFoundException;
use Yocto\Handler\Exception\FailedToCreateHandlerException;

interface HandlerResolverInterface
{
    /**
     * @throws HandlerNotFoundException
     * @throws FailedToCreateHandlerException
     * @throws InvalidHandlerException
     */
    public function resolve(string $handler): Action;
}