<?php

namespace Yocto\Handler\Exception;

use Yocto\Action\ActionInterface;

class InvalidHandlerException extends HandlerException
{
    public function __construct()
    {
        parent::__construct("Handler must resolve to an instance of " . ActionInterface::class);
    }
}