<?php

namespace Yocto\Tests\App\Actions;

use Yocto\Action;
use Yocto\Response;

use function Yocto\render;
use function Yocto\html;

class HomeAction extends Action
{
    public function action(): Response
    {
        return html(render('index'));
    }
}