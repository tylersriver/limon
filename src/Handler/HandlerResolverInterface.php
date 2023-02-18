<?php

namespace Yocto\Handler;

use Yocto\Action\ActionInterface;

interface HandlerResolverInterface
{
    /**
     * @throws HandlerNotFoundException
     * @throws FailedToCreateHandlerException
     * @throws InvalidHandlerException
     */
    public function resolve(string $handler): ActionInterface;
}