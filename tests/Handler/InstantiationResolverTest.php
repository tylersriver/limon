<?php

use Yocto\Action;
use Yocto\App;
use Yocto\Handler\InstantiationResolver;
use Yocto\Handler\Exception\InvalidHandlerException;
use Yocto\Handler\Exception\HandlerNotFoundException;

it('throws HandlerNotFoundException', function() {
    $resolver = new InstantiationResolver;

    $resolver->resolve('test-handler');
})->throws(HandlerNotFoundException::class);

it('throws InvalidHandlerException', function() {
    $resolver = new InstantiationResolver;

    $resolver->resolve(App::class);
})->throws(InvalidHandlerException::class);

it('Creates class', function() {
    $action = Mockery::mock(Action::class);

    $resolver = new InstantiationResolver;

    $action = $resolver->resolve($action::class);

    expect($action)->toBe($action);
});