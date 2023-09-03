<?php

namespace Limon\Handler;

use Psr\Http\Message\ServerRequestInterface;
use Limon\Handler\Exception\InvalidHandlerException;
use Limon\Handler\Exception\HandlerNotFoundException;
use Limon\Handler\Exception\FailedToCreateHandlerException;

interface HandlerResolverInterface
{
    /**
     * @throws HandlerNotFoundException
     * @throws FailedToCreateHandlerException
     * @throws InvalidHandlerException
     */
    public function resolve(ServerRequestInterface $handler): callable;
}