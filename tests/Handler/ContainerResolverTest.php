<?php

use Yocto\Action\ActionInterface;
use Yocto\Handler\ContainerResolver;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yocto\Handler\Exception\InvalidHandlerException;
use Yocto\Handler\Exception\HandlerNotFoundException;
use Yocto\Handler\Exception\FailedToCreateHandlerException;

it('throws HandlerNotFoundException', function() {
    $container = Mockery::mock(ContainerInterface::class);
    $container->allows('has')->once()->with('test-handler')->andReturns(false);

    $resolver = new ContainerResolver($container);

    $resolver->resolve('test-handler');
})->throws(HandlerNotFoundException::class);

it('has handler name in HandlerNotFoundException', function() {
    $container = Mockery::mock(ContainerInterface::class);
    $container->allows('has')->once()->with('test-handler')->andReturns(false);

    $resolver = new ContainerResolver($container);

    try {
        $resolver->resolve('test-handler');
    } catch(HandlerNotFoundException $e) {
        expect($e->getHandlerName())->toBe('test-handler');
    }
});

it('throws FailedToCreateHandlerException', function() {
    $container = Mockery::mock(ContainerInterface::class);
    $container->allows('has')->once()->with('test-handler')->andReturns('true');
    $container->allows('get')->once()->with('test-handler')->andReturns('fake');

    $resolver = new ContainerResolver($container);

    $resolver->resolve('test-handler');
})->throws(FailedToCreateHandlerException::class);

it('throws FailedToCreateHandlerException and HandlerName and exception are set', function() {
    $container = Mockery::mock(ContainerInterface::class);
    $container->allows('has')->once()->with('test-handler')->andReturns('true');
    $container->allows('get')->once()->with('test-handler')->andReturns('fake');

    $resolver = new ContainerResolver($container);

    try {
        $resolver->resolve('test-handler');
    } catch(FailedToCreateHandlerException $ex) {
        expect($ex->getHandlerName())->toBe('test-handler');
        expect($ex->getOriginalException())->toBeInstanceOf(InvalidHandlerException::class);
    }
});

it('returns ActionInterface Instance', function() {
    $container = Mockery::mock(ContainerInterface::class);
    $container->allows('has')->once()->with('test-handler')->andReturns('true');
    $container->allows('get')->once()->with('test-handler')->andReturns(new class implements ActionInterface {
        public function __invoke(ServerRequestInterface $request): ResponseInterface
        {
            return Mockery::mock(ResponseInterface::class);
        }
    });

    $resolver = new ContainerResolver($container);

    $obj = $resolver->resolve('test-handler');
    expect($obj)->toBeInstanceOf(ActionInterface::class);
});