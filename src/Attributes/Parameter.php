<?php

namespace Yocto\Attributes;

use Yocto\Request;

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

    public function getSearchArray(Request $request): array
    {
        switch ($this->in) {
            case self::GET:
                return $request->getGet();
            case self::POST:
                return $request->getPost();
            case self::SERVER:
                return $request->getServer();
            case self::ATTRIBUTE:
                return $request->getAttributes();
        }

        return [];
    }
}