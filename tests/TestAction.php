<?php
namespace Yocto\Tests;

use Yocto\Action;
use Yocto\Response;

class TestAction extends Action
{
    public function action(): Response
    {
        return new Response(200, 'test');
    }
}