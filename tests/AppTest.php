<?php

use Psr\EventDispatcher\EventDispatcherInterface;
use Yocto\App;
use Yocto\Handler\ContainerResolver;
use Yocto\Kernel;

it('can create app', function() {
    $app = new App(new Kernel(
        Mockery::mock(ContainerResolver::class),
        Mockery::mock(EventDispatcherInterface::class)
    ));

    expect($app)->toBeInstanceOf(App::class);
});