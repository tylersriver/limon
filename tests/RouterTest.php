<?php

namespace Yocto\Tests;

use Exception;
use Yocto\Container;
use Yocto\Request;
use Yocto\Response;
use Yocto\Router;
use Yocto\Views;

class RouterTest extends TestCase
{
    private Router $r;

    protected function setUp(): void
    {
        $this->r = new Router();
    }

    protected function testInstance()
    {
        $this->assertEquals($this->r, Router::class);
    }

    public function testAddGet()
    {
        $this->r->get('index', fn(Request $request) => new Response(200, 'Hi'));
        $this->assertEquals($this->r->hasRoute('GET', 'index'), true);
    }

    public function testAddPost()
    {
        $this->r->post('index', fn(Request $request) => new Response(200, 'Hi'));
        $this->assertEquals($this->r->hasRoute('POST', 'index'), true);
    }

    public function testAddPut()
    {
        $this->r->put('index', fn(Request $request) => new Response(200, 'Hi'));
        $this->assertEquals($this->r->hasRoute('PUT', 'index'), true);
    }

    public function testAddDelete()
    {
        $this->r->delete('index', fn(Request $request) => new Response(200, 'Hi'));
        $this->assertEquals($this->r->hasRoute('DELETE', 'index'), true);
    }

    public function testAddOptions()
    {
        $this->r->options('index', fn(Request $request) => new Response(200, 'Hi'));
        $this->assertEquals($this->r->hasRoute('OPTIONS', 'index'), true);
    }

    public function testAddGroup()
    {
        $this->r->addGroup('group', function (Router $r) {
            $r->get('index', fn(Request $request) => new Response(200, 'Hi'));
        });
        $this->assertEquals($this->r->hasRoute('GET', 'group/index'), true);
    }

    public function testAddInvalidRoute()
    {
        $this->expectException('Exception');
        $this->r->get('index-aasdfas', fn(Request $request) => new Response(200, 'Hi'));
    }

    public function testDispatch()
    {
        $this->r->addGroup('group', function (Router $r) {
            $r->get('index', fn(Request $request) => new Response(200, 'Hi'));
            $r->get('home', 'ClassThatDoesntExist');
            $r->get('some', TestAction::class);
        });

        $callable = $this->r->dispatch(new Request(server: ['REQUEST_URI' => '/group/index']));
        $this->assertEquals(is_callable($callable), true);

        $callable = $this->r->dispatch(new Request(server: ['REQUEST_URI' => 'group/index']));
        $this->assertEquals(is_callable($callable), false);

        $callable = $this->r->dispatch($this->createServerRequest('/group/some'));
        $this->assertInstanceOf(TestAction::class, $callable);

        $callable = $this->r->dispatch(new Request(server: ['REQUEST_URI' => '/group/home']));
        $this->assertEquals(null, $callable);
    }
}