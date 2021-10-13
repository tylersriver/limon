<?php

namespace Yocto\Tests\App\Actions;

use Yocto\Tests\App\Service\SampleService;

use Yocto\Action;
use Yocto\Attributes\Parameter;
use Yocto\Attributes\Required;
use Yocto\Response;

class SampleAction extends Action
{
    #[
        Parameter(
            'foo',
            Parameter::GET,
            '^bar$',
        )
    ]
    #[Required]
    protected string $foo;

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