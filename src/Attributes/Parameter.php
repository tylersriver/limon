<?php

namespace Yocto\Attributes;

use Psr\Http\Message\ServerRequestInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Parameter
{
    public const GET = 'GET';
    public const POST = 'POST';
    public const SERVER = 'SERVER';
    public const ATTRIBUTE = 'ATTRIBUTE';

    public function __construct(
        public string $name,
        public string $in,
        public string $pattern,
    ) {
    }
}