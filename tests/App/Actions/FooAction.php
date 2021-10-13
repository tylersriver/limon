<?php

namespace Yocto\Tests\App\Actions;

use Yocto\Action;
use Yocto\Attributes\Parameter;
use Yocto\Attributes\Required;
use Yocto\Response;

class FooAction extends Action
{
    #[Parameter('foo', Parameter::GET, '^bar$')]
    #[Required]
    protected string $var;

    public function action(): Response
    {
        return new Response(200, ['message' => 'success']);
    }
}