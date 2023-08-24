<?php

use Yocto\Kernel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yocto\Action;
use Yocto\Handler\ActionResolver;
use Yocto\Handler\Exception\HandlerAttributeNotSetException;

it('calls kernel handle', function() {
    
    $response = Mockery::mock(ResponseInterface::class);

    $action = Mockery::mock(Action::class);
    $action->allows('__invoke')->once()->withAnyArgs()->andReturns($response);

    $dispatcher = Mockery::mock(EventDispatcherInterface::class);
    $dispatcher->allows('dispatch')->times(3)->withAnyArgs();
    
    $request = Mockery::mock(ServerRequestInterface::class);
    $request->allows('getAttribute')->once()->with('request-handler')->andReturn($action::class);

    $resolver = Mockery::mock(ActionResolver::class);
    $resolver->allows('resolve')->once()->with($request)->andReturns($action);

    $kernel = new Kernel(
        $resolver,
        $dispatcher
    );

    $response = $kernel->handle($request);
    expect($response)->toBeInstanceOf(ResponseInterface::class);
});

it('throws HandlerAttributeNotSetException', function() {

    // Arrange
    // ------------------------------------------------------------
    $dispatcher = Mockery::mock(EventDispatcherInterface::class);
    $dispatcher->allows('dispatch')->once()->withAnyArgs();

    $kernel = new Kernel(
        Mockery::mock(ActionResolver::class),
        $dispatcher
    );

    $request = Mockery::mock(ServerRequestInterface::class);
    $request->allows('getAttribute')->once()->with('request-handler', null)->andReturn(null);

    // Act
    // ------------------------------------------------------------
    $kernel->handle($request);

    // Assert
    // ------------------------------------------------------------
})->throws(HandlerAttributeNotSetException::class);


it('throws exception during handle', function() {
    $response = Mockery::mock(ResponseInterface::class);

    $action = Mockery::mock(Action::class);
    $action->allows('__invoke')->once()->withAnyArgs()->andThrow(new \Exception('test message'));

    $dispatcher = Mockery::mock(EventDispatcherInterface::class);
    $dispatcher->allows('dispatch')->times(3)->withAnyArgs();

    $resolver = Mockery::mock(ActionResolver::class);
    $resolver->allows('resolve')->once()->with($action::class)->andReturns($action);

    $kernel = new Kernel(
        $resolver,
        $dispatcher
    );

    $request = Mockery::mock(ServerRequestInterface::class);
    $request->allows('getAttribute')->once()->with('request-handler', null)->andReturn($action::class);

    $kernel->handle($request);
})->throws(\Exception::class);