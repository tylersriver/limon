<?php

namespace Yocto\Tests;

use Exception;
use Yocto\Container;
use Yocto\Views;

class ViewTest extends TestCase
{
    private Views $v;

    protected function setUp(): void
    {
        $this->v = new Views(__DIR__ .'/App/Views/');
    }
    
    public function testBadTemplatesDir()
    {
        $this->expectException('Exception');
        new Views(__DIR__ . 'random/dir/for/templates');
    }

    public function testRender()
    {
        $view = $this->v->render('index');
        $this->assertEquals($view, 'Hi');

        $view = $this->v->render('');
        $this->assertEquals($view, '');

        $view = $this->v->render('withParam', ['foo' => 'bar']);
        $this->assertEquals($view, 'bar');
    }

    public function testEscape()
    {
        $view = $this->v->render('withEscapedParam', ['foo' => 'bar']);
        $this->assertEquals($view, 'bar');
    }

    public function testInsert()
    {
        $view = $this->v->render('withInsert', ['insert' => 'insert']);
        $this->assertEquals($view, 'Hello');

        $view = $this->v->render('withInsert', ['insert' => 'notExists']);
    }
}