<?php

namespace Yocto\Handler;

use Yocto\Action\ActionInterface;

class InvalidHandlerException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Handler must resolve to an instance of " . ActionInterface::class);
    }
}