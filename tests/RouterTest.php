<?php

namespace Yocto\Tests;

use Yocto\Tests\App\Actions\FooAction;
use Yocto\Request;
use Yocto\Response;
use Yocto\Router;

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
        $this->r->get('/index', fn(Request $request) => new Response(200, 'Hi'));
        $this->assertEquals(is_callable($this->r->dispatch(new Request(server: ['REQUEST_URI' => '/index']))[0]), true);
    }

    public function testAddPost()
    {
        $this->r->post('/index', fn(Request $request) => new Response(200, 'Hi'));
        $this->assertEquals(is_callable($this->r->dispatch(new Request(server: ['REQUEST_URI' => '/index', 'REQUEST_METHOD' => 'POST']))[0]), true);
    }

    public function testAddPut()
    {
        $this->r->put('/index', fn(Request $request) => new Response(200, 'Hi'));
        $this->assertEquals(is_callable($this->r->dispatch(new Request(server: ['REQUEST_URI' => '/index', 'REQUEST_METHOD' => 'PUT']))[0]), true);
    }

    public function testAddDelete()
    {
        $this->r->delete('/index', fn(Request $request) => new Response(200, 'Hi'));
        $this->assertEquals(is_callable($this->r->dispatch(new Request(server: ['REQUEST_URI' => '/index', 'REQUEST_METHOD' => 'DELETE']))[0]), true);
    }

    public function testAddOptions()
    {
        $this->r->options('/index', fn(Request $request) => new Response(200, 'Hi'));
        $this->assertEquals(is_callable($this->r->dispatch(new Request(server: ['REQUEST_URI' => '/index', 'REQUEST_METHOD' => 'OPTIONS']))[0]), true);
    }

    public function testAddGroup()
    {
        $this->r->group('/group', function (Router $r) {
            $r->get('/index', fn(Request $request) => new Response(200, 'Hi'));
        });
        $this->assertEquals(is_callable($this->r->dispatch(new Request(server: ['REQUEST_URI' => '/group/index']))[0]), true);
    }

    public function testAddInvalidRoute()
    {
        $this->expectException('Exception');
        $this->r->get('index_aasdfas', fn(Request $request) => new Response(200, 'Hi'));
        $this->r->get('test/route', fn(Request $request) => new Response(200, 'Hi'));
    }

    public function testDispatch()
    {
        $app = $this->getBaseAppInstance();

        $this->r->group('/group', function (Router $r) {
            $r->get('/index', fn(Request $request) => new Response(200, 'Hi'));
            $r->get('/home', 'ClassThatDoesntExist');
            $r->get('/some', FooAction::class);
            $r->get('/some/:id', FooAction::class);
        });

        $callable = $this->r->dispatch(new Request(server: ['REQUEST_URI' => '/group/index']));
        $this->assertEquals(is_callable($callable[0]), true);

        $callable = $this->r->dispatch(new Request(server: ['REQUEST_URI' => 'group/index']));
        $this->assertEquals($callable, null);

        $callable = $this->r->dispatch($this->createServerRequest('/group/some'));
        $this->assertInstanceOf(FooAction::class, $callable[0]);

        $callable = $this->r->dispatch(new Request(server: ['REQUEST_URI' => '/group/home']));
        $this->assertEquals(null, $callable);

        $callable = $this->r->dispatch(new Request(server: ['REQUEST_URI' => '/group/some/1']));
        $this->assertEquals(is_callable($callable[0]), true);
        $this->assertEquals($callable[1]['id'], 1);
    }
}