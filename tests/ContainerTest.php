<?php

namespace Yocto\Tests;

use Exception;
use Yocto\Container;

class ContainerTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = new Container([
            'foo' => 'bar'
        ]);
    }

    public function testCreation()
    {
        $this->assertInstanceOf(Container::class, $this->container);
    }

    public function testHas()
    {
        $this->assertEquals($this->container->has('foo'), true);
        $this->assertEquals($this->container->has('foo2'), false);
    }

    public function testGet()
    {
        $this->assertEquals($this->container->get('foo'), 'bar');

        $this->expectException(\Exception::class);
        $this->container->get('foo2');
    }

    public function testSet()
    {
        $this->container->set('foo4', 'bar4');
        $this->assertEquals($this->container->get('foo4'), 'bar4');
    }
}