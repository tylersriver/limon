<?php

namespace Yocto\Tests\App\Actions;

use Yocto\Action;
use Yocto\Attributes\Parameter;
use Yocto\Attributes\Required;
use Yocto\Response;

use function Yocto\render;
use function Yocto\html;

class HomeAction extends Action
{

    #[Parameter('title', Parameter::GET, '^(\w|\s)+$')]
    #[Required]
    protected string $title;

    public function action(): Response
    {
        return html(render('home', ['title' => $this->title]));
    }
}