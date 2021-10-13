<?php

namespace Yocto\Tests\App\Service;

class SampleService
{
    private OtherService $OtherService;

    public function __construct(OtherService $OtherService)
    {
        $this->OtherService = $OtherService;
    }

    public function getFoo()
    {
        return $this->OtherService->foo;
    }
}