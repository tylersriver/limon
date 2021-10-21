<?php

namespace Yocto\Tests\App\Actions;

use Yocto\Action;
use Yocto\Attributes\Parameter;
use Yocto\Attributes\Required;
use Yocto\Attributes\Route;
use Yocto\Response;

use function Yocto\success;

#[Route(Route::GET, '/foo/action')]
class FooAction extends Action
{
    #[Parameter('foo', Parameter::GET, '^bar$')]
    #[Required]
    protected string $var;

    public function action(): Response
    {
        return success(['message' => 'success']);
    }
}