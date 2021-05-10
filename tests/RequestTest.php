<?php

namespace Yocto\Tests;

use Yocto\Request;

class RequestTest extends TestCase
{
    public function testRequest()
    {
        $r = new Request(
            ['foo' => 'bar'],
            ['foo' => 'bar'],
            ['REQUEST_URI' => 'group/index'],
            ['foo' => 'bar'],
            ['foo' => 'bar']
        );

        $this->assertEquals($r->getGet()['foo'], 'bar');
        $this->assertEquals($r->getServer()['REQUEST_URI'], 'group/index');
        $this->assertEquals($r->getFiles()['foo'], 'bar');
        $this->assertEquals($r->getRequest()['foo'], 'bar');
        $this->assertEquals($r->getPost()['foo'], 'bar');
    }
    
}