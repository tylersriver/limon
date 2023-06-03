<?php

namespace Yocto\Handler\Exception;

use Yocto\Action;

class InvalidHandlerException extends HandlerException
{
    public function __construct()
    {
        parent::__construct("Handler must resolve to an instance of " . Action::class);
    }
}