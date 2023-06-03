<?php

namespace Yocto\Handler;

use Throwable;
use Psr\Container\ContainerInterface;
use Yocto\Action;
use Yocto\Handler\Exception\InvalidHandlerException;
use Yocto\Handler\Exception\HandlerNotFoundException;
use Yocto\Handler\Exception\FailedToCreateHandlerException;

class ContainerResolver implements HandlerResolverInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolve(string $handler): Action
    {
        if (!$this->container->has($handler)) {
            throw new HandlerNotFoundException($handler);
        }

        try {
            $action = $this->container->get($handler);
            if (!$action instanceof Action) {
                throw new InvalidHandlerException();
            }

            return $action;
        } catch (Throwable $e) {
            throw new FailedToCreateHandlerException($handler, $e);
        }
    }
}