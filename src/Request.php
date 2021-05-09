<?php

namespace Yocto;

class Request
{
    /**
     * @var array
     */
    private array $get;

    /**
     * @var array
     */
    private array $post;

    /**
     * @var array
     */
    private array $request;

    /**
     * @var array
     */
    private array $server;

    /**
     * @var array
     */
    private array $files;

    /**
     * @var string
     */
    private string $method;

    /**
     * @var string
     */
    private string $uri;

    /**
     * Request constructor.
     *
     * @param array $get
     * @param array $post
     * @param array $server
     * @param array $request
     * @param array $files
     */
    private function __construct(
        array $get = [],
        array $post = [],
        array $server = [],
        array $request = [],
        array $files = []
    ) {
        $this->files = $files;
        $this->get = $get;
        $this->post = $post;
        $this->request = $request;
        $this->server = $server;
        $this->method = $server['REQUEST_METHOD'] ?? 'GET';
        $this->uri = explode('?', $server['REQUEST_URI'])[0];
    }

    /**
     * @return self
     */
    public static function fromGlobals(): self
    {
        return new self($_GET, $_POST, $_SERVER, $_REQUEST, $_FILES);
    }

    /**
     * @return array
     */
    public function getGet(): array
    {
        return $this->get;
    }

    /**
     * @return array
     */
    public function getPost(): array
    {
        return $this->post;
    }

    /**
     * @return array
     */
    public function getRequest(): array
    {
        return $this->request;
    }

    /**
     * @return array
     */
    public function getServer(): array
    {
        return $this->server;
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }
}
