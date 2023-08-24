<?php

namespace Yocto\Handler;

use Throwable;
use Yocto\Action;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yocto\Handler\Exception\InvalidHandlerException;
use Yocto\Handler\Exception\HandlerNotFoundException;
use Yocto\Handler\Exception\FailedToCreateHandlerException;
use Yocto\Handler\Exception\HandlerAttributeNotSetException;

class ActionResolver implements HandlerResolverInterface
{
    public function __construct(
        private ?ContainerInterface $container = null
    ) {
        $this->container = $container;
    }

    public function resolve(ServerRequestInterface $request): callable
    {
        $handler = $request->getAttribute('request-handler');
        if (!is_string($handler)) {
            throw new HandlerAttributeNotSetException(
                'request-handler Attribute must be set in the Request object and be a string'
            );
        }

        if ($this->container !== null) {
            return $this->fromContainer($handler);
        }

        return $this->fromInstantiation($handler);
    }

    private function fromContainer(string $handler): Action
    {
        if ($this->container === null || !$this->container->has($handler)) {
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

    private function fromInstantiation(string $handler): Action
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