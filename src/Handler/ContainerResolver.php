<?php

namespace Yocto\Handler;

use Psr\Container\ContainerInterface;
use Throwable;
use Yocto\Action\ActionInterface;

class ContainerResolver implements HandlerResolverInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolve(string $handler): ActionInterface
    {
        if (!$this->container->has($handler)) {
            throw new HandlerNotFoundException($handler);
        }

        try {
            $action = $this->container->get($handler);
            if (!$action instanceof ActionInterface) {
                throw new InvalidHandlerException();
            }

            return $action;
        } catch (Throwable $e) {
            throw new FailedToCreateHandlerException($handler, $e);
        }
    }
}