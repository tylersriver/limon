<?php

use Yocto\App;
use Yocto\Kernel;
use Yocto\Handler\ActionResolver;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

it('can create app', function() {
    $app = new App(new Kernel(
        Mockery::mock(ActionResolver::class),
        Mockery::mock(EventDispatcherInterface::class)
    ));

    expect($app)->toBeInstanceOf(App::class);
});

it('app calls kernel handle', function() {

    $response = Mockery::mock(ResponseInterface::class);
    $request = Mockery::mock(ServerRequestInterface::class);
    $kernel = Mockery::mock(Kernel::class);
    $kernel->allows('handle')->once()->with($request)->andReturns($response);

    $app = new App($kernel);
    $res = $app->handle($request);

    expect($res)->toBeInstanceOf(ResponseInterface::class);
});

it('app can add middleware', function() {

    $response = Mockery::mock(ResponseInterface::class);
    $request = Mockery::mock(ServerRequestInterface::class);
    $kernel = Mockery::mock(Kernel::class);

    $middleware = Mockery::mock(MiddlewareInterface::class);
    $middleware->allows('process')->once()->with($request, $kernel)->andReturns($response);

    $app = new App($kernel);
    $app->use($middleware);
    $res = $app->handle($request);

    expect($res)->toBeInstanceOf(ResponseInterface::class);
});