<?php

namespace Yocto;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yocto\Events\KernelAction;
use Yocto\Events\KernelRequest;
use Yocto\Events\KernelResponse;
use Yocto\Handler\FailedToCreateHandlerException;
use Yocto\Handler\HandlerAttributeNotSetException;
use Yocto\Handler\HandlerNotFoundException;
use Yocto\Handler\HandlerResolverInterface;

class Kernel implements RequestHandlerInterface
{
    public function __construct(
        private HandlerResolverInterface $resolver,
        private EventDispatcherInterface $eventDispatcher
    ){
    }

    /**
     * @throws FailedToCreateHandlerException
     * @throws HandlerNotFoundException
     * @throws HandlerAttributeNotSetException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->eventDispatcher->dispatch(new KernelRequest($request));

        $requestHandler = $request->getAttribute('request-handler', null);

        // We only accept request-handler as a string to resolve 
        // to an ActionInterface instance, all other options are not valid
        if(!is_string($requestHandler)) {
            throw new HandlerAttributeNotSetException;
        }

        $handler = $this->resolver->resolve($requestHandler);
        $this->eventDispatcher->dispatch(new KernelAction($request));
        $response = $handler($request);
        $this->eventDispatcher->dispatch(new KernelResponse($request, $response));
        return $response;
    }
}