<?php

namespace Yocto\Tests\App\Actions;

use Yocto\Tests\App\Service\SampleService;

use Yocto\Action;
use Yocto\Attributes\Parameter;
use Yocto\Attributes\Required;
use Yocto\Response;

class SampleAction extends Action
{
    #[Parameter('foo', Parameter::POST, '^bar$')]
    #[Required]
    protected string $foo;

    #[Parameter('id', Parameter::ATTRIBUTE, '^\d+$')]
    #[Required]
    protected string $id;
    
    #[Parameter('role', Parameter::ATTRIBUTE, '^\d+$')]
    #[Required]
    protected string $role;

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