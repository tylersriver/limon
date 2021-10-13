<?php

namespace Example;

class SampleController
{
    private OtherController $otherController;

    public function __construct(OtherController $otherController)
    {
        $this->otherController = $otherController;
    }

    public function getFoo()
    {
        return $this->otherController->foo;
    }
}