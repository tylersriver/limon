<?php

namespace Yocto\Tests\App\Actions;

use Yocto\Tests\App\Service\SampleService;

use Yocto\Action;
use Yocto\Response;

class SampleAction extends Action
{
    private SampleService $controller;

    public function __construct(SampleService $sampleController)
    {
        $this->controller = $sampleController;
    }

    public function action(): Response
    {
        return new Response(200, ['message' => $this->controller->getFoo()]);
    }
}