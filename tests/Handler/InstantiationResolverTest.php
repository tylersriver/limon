<?php

use Yocto\Action\ActionInterface;
use Yocto\Action\BaseAction;
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
    $action = Mockery::mock(ActionInterface::class);

    $resolver = new InstantiationResolver;

    $action = $resolver->resolve($action::class);

    expect($action)->toBe($action);
});

it('Creates BaseAction class', function() {
    $action = Mockery::mock(BaseAction::class);

    $resolver = new InstantiationResolver;

    $action = $resolver->resolve($action::class);

    expect($action)->toBe($action);
    expect($action)->toBeInstanceOf(BaseAction::class);
});