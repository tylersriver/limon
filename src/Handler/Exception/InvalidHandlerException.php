<?php

namespace Limon\Handler\Exception;

use Limon\Action;

class InvalidHandlerException extends HandlerException
{
    public function __construct()
    {
        parent::__construct("Handler must resolve to an instance of " . Action::class);
    }
}