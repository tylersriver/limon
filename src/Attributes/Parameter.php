<?php

namespace Yocto\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Parameter
{
    public const GET = 'GET';
    public const POST = 'POST';
    public const SERVER = 'SERVER';

    public function __construct(
        public string $name,
        public string $method,
        public string $pattern,
    ) {
    }
}