<?php

namespace Yocto\Handler\Exception;

use Throwable;

class FailedToCreateHandlerException extends HandlerException
{
    private string $handlerName;

    private Throwable $originalException;

    public function __construct(string $handlerName, Throwable $originalException)
    {
        $this->handlerName = $handlerName;
        $this->originalException = $originalException;
        parent::__construct("Failed to create handler with name: $handlerName");
    }

    public function getHandlerName(): string
    {
        return $this->handlerName;
    }

    public function getOriginalException(): Throwable
    {
        return $this->originalException;
    }
}