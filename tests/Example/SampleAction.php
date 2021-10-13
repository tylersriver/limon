<?php

namespace Example;

use Yocto\Action;
use Yocto\Response;

use function Yocto\success;

class SampleAction extends Action
{
    private SampleController $controller;

    public function __construct(SampleController $sampleController)
    {
        $this->controller = $sampleController;
    }

    public function action(): Response
    {
        return success([]);
    }
}