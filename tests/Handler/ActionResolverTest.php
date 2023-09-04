<?php

use Limon\Action;
use Limon\Handler\ActionResolver;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Container\NotFoundExceptionInterface;
use Limon\Handler\Exception\InvalidHandlerException;
use Limon\Handler\Exception\HandlerNotFoundException;
use Limon\Handler\Exception\FailedToCreateHandlerException;
use Limon\Handler\Exception\HandlerAttributeNotSetException;

it('throws HandlerAttributeNotSetException', function() {
    $container = Mockery::mock(ContainerInterface::class);
    $request = Mockery::mock(ServerRequestInterface::class);
    $request->allows('getAttribute')->once()->with('request-handler', null)->andReturn(null);

    $resolver = new ActionResolver($container);

    $resolver->resolve($request);
})->throws(HandlerAttributeNotSetException::class);

it('throws HandlerNotFoundException', function() {
    $container = Mockery::mock(ContainerInterface::class);
    $request = Mockery::mock(ServerRequestInterface::class);
    $exception = new class extends Exception implements NotFoundExceptionInterface{};
    $container->allows('get')->once()->andThrow($exception);
    $request->allows('getAttribute')->once()->with('request-handler', null)->andReturn('test-handler');

    $resolver = new ActionResolver($container);

    $resolver->resolve($request);
})->throws(HandlerNotFoundException::class);

it('has handler name in HandlerNotFoundException', function() {
    $container = Mockery::mock(ContainerInterface::class);
    $exception = new class extends Exception implements NotFoundExceptionInterface{};
    $container->allows('get')->once()->andThrow($exception);
    $request = Mockery::mock(ServerRequestInterface::class);
    $request->allows('getAttribute')->once()->with('request-handler', null)->andReturn('test-handler');

    $resolver = new ActionResolver($container);

    try {
        $resolver->resolve($request);
    } catch(HandlerNotFoundException $e) {
        expect($e->getHandlerName())->toBe('test-handler');
    }
});

it('throws FailedToCreateHandlerException', function() {
    $container = Mockery::mock(ContainerInterface::class);
    $request = Mockery::mock(ServerRequestInterface::class);
    $request->allows('getAttribute')->once()->with('request-handler', null)->andReturn('test-handler');
    $container->allows('get')->once()->with('test-handler')->andReturns('fake');

    $resolver = new ActionResolver($container);

    $resolver->resolve($request);
})->throws(FailedToCreateHandlerException::class);

it('throws FailedToCreateHandlerException and HandlerName and exception are set', function() {
    $container = Mockery::mock(ContainerInterface::class);
    $request = Mockery::mock(ServerRequestInterface::class);
    $request->allows('getAttribute')->once()->with('request-handler', null)->andReturn('test-handler');
    $container->allows('get')->once()->with('test-handler')->andReturns('fake');

    $resolver = new ActionResolver($container);

    try {
        $resolver->resolve($request);
    } catch(FailedToCreateHandlerException $ex) {
        expect($ex->getHandlerName())->toBe('test-handler');
        expect($ex->getOriginalException())->toBeInstanceOf(InvalidHandlerException::class);
    }
});

it('returns Action Instance', function() {
    $container = Mockery::mock(ContainerInterface::class);
    $request = Mockery::mock(ServerRequestInterface::class);
    $request->allows('getAttribute')->once()->with('request-handler', null)->andReturn('test-handler');
    $container->allows('get')->once()->with('test-handler')->andReturns(new class implements Action {
        public function __invoke(ServerRequestInterface $request): ResponseInterface
        {
            return Mockery::mock(ResponseInterface::class);
        }
    });

    $resolver = new ActionResolver($container);

    $obj = $resolver->resolve($request);
    expect($obj)->toBeInstanceOf(Action::class);
});
