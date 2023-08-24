<?php

namespace Yocto\Handler;

use Psr\Http\Message\ServerRequestInterface;
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
    public function resolve(ServerRequestInterface $handler): callable;
}