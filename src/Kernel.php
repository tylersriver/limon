<?php

namespace Yocto;

use Yocto\Events\KernelError;
use Yocto\Events\KernelAction;
use Yocto\Events\KernelRequest;
use Yocto\Events\KernelResponse;
use Psr\Http\Message\ResponseInterface;
use Yocto\Handler\HandlerResolverInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yocto\Handler\Exception\InvalidHandlerException;
use Yocto\Handler\Exception\HandlerNotFoundException;
use Yocto\Handler\Exception\FailedToCreateHandlerException;
use Yocto\Handler\Exception\HandlerAttributeNotSetException;

class Kernel implements RequestHandlerInterface
{
    public function __construct(
        private HandlerResolverInterface $resolver,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @throws FailedToCreateHandlerException
     * @throws HandlerNotFoundException
     * @throws HandlerAttributeNotSetException
     * @throws InvalidHandlerException
     */
    /** @SuppressWarnings(PHPMD) */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->eventDispatcher->dispatch(new KernelRequest($request));

        // We only accept request-handler as a string to resolve
        // to an ActionInterface instance, all other options are not valid
        $requestHandler = $request->getAttribute('request-handler', null);
        if (!is_string($requestHandler)) {
            throw new HandlerAttributeNotSetException('request-handler Attribute must be set in the Request object');
        }

        // Resolve the handler callable, exceptions should be handled
        // elsewhere in middleware
        $handler = $this->resolver->resolve($requestHandler);
        $this->eventDispatcher->dispatch(new KernelAction($request));

        // Try the handler, when error dispatch event and re-throw
        try {
            $response = $handler($request);
            $this->eventDispatcher->dispatch(new KernelResponse($request, $response));
            return $response;
        } catch (\Throwable $e) {
            $this->eventDispatcher->dispatch(new KernelError($request, $handler));
            throw $e;
        }
    }
}