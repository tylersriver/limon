<?php

namespace Yocto\Tests\App\Actions;

use Yocto\Action;
use Yocto\Attributes\Parameter;
use Yocto\Attributes\Required;
use Yocto\Response;

class IndexAction extends Action
{
    #[Parameter('foo', Parameter::GET, '^bar$')]
    #[Required]
    protected $var;

    public function action(): Response
    {
        return new Response(200, ['message' => 'success']);
    }
}