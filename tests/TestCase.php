<?php

namespace Yocto\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Yocto\Request;

abstract class TestCase extends PHPUnitTestCase
{
    public function createServerRequest(string $uri, string $method = 'GET', array $get = [], array $post = []) : Request
    {
        $_SERVER = [
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.3',
            'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.8',
            'HTTP_HOST' => 'localhost',
            'HTTP_USER_AGENT' => 'Yocto Framework',
            'QUERY_STRING' => '',
            'REMOTE_ADDR' => '127.0.0.1',
            'REQUEST_METHOD' => $method,
            'REQUEST_TIME' => time(),
            'REQUEST_TIME_FLOAT' => microtime(true),
            'REQUEST_URI' => $uri,
            'SCRIPT_NAME' => '/index.php',
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => 80,
            'SERVER_PROTOCOL' => 'HTTP/1.1',
        ];

        $_GET = $get;

        $_POST = $post;

        return Request::fromGlobals();
    }
}