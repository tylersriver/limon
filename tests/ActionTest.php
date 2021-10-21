<?php
namespace Yocto\Tests;

use Yocto\Tests\App\Actions\FooAction;
use Yocto\Response;
use Yocto\Tests\App\Actions\AnotherAction;
use Yocto\Tests\App\Actions\IndexAction;
use Yocto\Tests\App\Actions\MissingRequiredAction;

class ActionTest extends TestCase
{
    public function testFooAction(): void
    {
        $request = $this->createServerRequest('', 'GET', ['foo' => 'bar']);

        $action = new FooAction();
    
        /** @var Response */
        $response = $action($request);

        $this->assertInstanceOf(Response::class, $action($request));

        $this->assertEquals(200, $response->getStatus());
    }

    public function testInvalidGetValueAction(): void
    {
        $request = $this->createServerRequest('', 'GET', ['foo' => 'barzz']);

        $action = new FooAction();
    
        /** @var Response */
        $response = $action($request);

        $this->assertEquals(400, $response->getStatus());
    }

    public function testInvalidAnotationAction(): void
    {
        $request = $this->createServerRequest('', 'GET', ['foo' => 'bar']);

        // No Type
        $action = new IndexAction();
        /** @var Response */
        $response = $action($request);
        $this->assertEquals(400, $response->getStatus());

        // Missing required parameter
        $action = new MissingRequiredAction();
        $request = $this->createServerRequest('', 'GET', []);
        $response = $action($request);
        $this->assertEquals(400, $response->getStatus());
    }

}