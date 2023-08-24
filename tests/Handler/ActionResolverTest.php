<?php

use Yocto\Handler\ActionResolver;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yocto\Action;
use Yocto\Handler\Exception\InvalidHandlerException;
use Yocto\Handler\Exception\HandlerNotFoundException;
use Yocto\Handler\Exception\FailedToCreateHandlerException;
use Yocto\Handler\Exception\HandlerAttributeNotSetException;

it('throws HandlerAttributeNotSetException', function() {
    $container = Mockery::mock(ContainerInterface::class);
    $request = Mockery::mock(ServerRequestInterface::class);
    $request->allows('getAttribute')->once()->with('request-handler')->andReturn(null);

    $resolver = new ActionResolver($container);

    $resolver->resolve($request);
})->throws(HandlerAttributeNotSetException::class);

it('throws HandlerNotFoundException', function() {
    $container = Mockery::mock(ContainerInterface::class);
    $request = Mockery::mock(ServerRequestInterface::class);
    $request->allows('getAttribute')->once()->with('request-handler')->andReturn('test-handler');
    $container->allows('has')->once()->with('test-handler')->andReturns(false);

    $resolver = new ActionResolver($container);

    $resolver->resolve($request);
})->throws(HandlerNotFoundException::class);

it('has handler name in HandlerNotFoundException', function() {
    $container = Mockery::mock(ContainerInterface::class);
    $request = Mockery::mock(ServerRequestInterface::class);
    $request->allows('getAttribute')->once()->with('request-handler')->andReturn('test-handler');
    $container->allows('has')->once()->with('test-handler')->andReturns(false);

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
    $request->allows('getAttribute')->once()->with('request-handler')->andReturn('test-handler');
    $container->allows('has')->once()->with('test-handler')->andReturns('true');
    $container->allows('get')->once()->with('test-handler')->andReturns('fake');

    $resolver = new ActionResolver($container);

    $resolver->resolve($request);
})->throws(FailedToCreateHandlerException::class);

it('throws FailedToCreateHandlerException and HandlerName and exception are set', function() {
    $container = Mockery::mock(ContainerInterface::class);
    $request = Mockery::mock(ServerRequestInterface::class);
    $request->allows('getAttribute')->once()->with('request-handler')->andReturn('test-handler');
    $container->allows('has')->once()->with('test-handler')->andReturns('true');
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
    $request->allows('getAttribute')->once()->with('request-handler')->andReturn('test-handler');
    $container->allows('has')->once()->with('test-handler')->andReturns('true');
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
