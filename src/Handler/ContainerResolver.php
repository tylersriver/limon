<?php

namespace Yocto\Handler;

use Psr\Container\ContainerInterface;
use Throwable;

class ContainerResolver implements HandlerResolverInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolve(string $handler)
    {
        if(!$this->container->has($handler)) {
            throw new HandlerNotFoundException($handler);
        }

        try {
            return $this->container->get($handler);
        } catch(Throwable $e) {
            throw new FailedToCreateHandlerException($handler, $e);
        }
    }
}