<?php

namespace Yocto\Handler;

class HandlerNotFoundException extends \Exception
{
    private string $handlerName;

    public function __construct(string $handlerName)
    {
        $this->handlerName = $handlerName;
        parent::__construct("unable to resolve handler with name: $handlerName");
    }

    public function getHandlerName(): string
    {
        return $this->handlerName;
    }
}