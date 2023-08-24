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
        private ?EventDispatcherInterface $eventDispatcher = null
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
        $this->eventDispatcher?->dispatch(new KernelRequest($request));

        // Resolve the handler callable, exceptions should be handled
        // elsewhere in middleware
        $handler = $this->resolver->resolve($request);
        $this->eventDispatcher?->dispatch(new KernelAction($request));

        // Try the handler, when error dispatch event and re-throw
        try {
            $response = $handler($request);
            $this->eventDispatcher?->dispatch(new KernelResponse($request, $response));
            return $response;
        } catch (\Throwable $e) {
            $this->eventDispatcher?->dispatch(new KernelError($request, $e));
            throw $e;
        }
    }
}