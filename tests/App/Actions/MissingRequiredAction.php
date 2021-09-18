<?php

namespace Yocto\Tests\App\Actions;

use Yocto\Action;
use Yocto\Response;

class MissingRequiredAction extends Action
{
    /**
     * @required
     * @name foo
     * @method GET
     */
    protected string $var;

    public function action(): Response
    {
        return new Response(200, ['message' => 'success']);
    }
}