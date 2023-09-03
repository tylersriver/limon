<?php

namespace Limon;

use Limon\Events\KernelError;
use Limon\Events\KernelAction;
use Limon\Events\KernelRequest;
use Limon\Events\KernelResponse;
use Psr\Http\Message\ResponseInterface;
use Limon\Handler\HandlerResolverInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Limon\Handler\Exception\InvalidHandlerException;
use Limon\Handler\Exception\HandlerNotFoundException;
use Limon\Handler\Exception\FailedToCreateHandlerException;
use Limon\Handler\Exception\HandlerAttributeNotSetException;

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