<?php

namespace Yocto;

use Attribute;

class Request
{
    /**
     * @var string
     */
    private string $method;

    /**
     * @var string
     */
    private string $uri;

    /**
     * @var string[]
     */
    private array $attributes = [];

    /**
     * Request constructor.
     *
     * @param array $get
     * @param array $post
     * @param array $server
     * @param array $request
     * @param array $files
     */
    public function __construct(
        private array $get = [],
        private array $post = [],
        private array $server = [],
        private array $request = [],
        private array $files = [],
        private array $headers = []
    ) {
        $this->files = $files;
        $this->get = $get;
        $this->post = $post;
        $this->request = $request;
        $this->server = $server;

        $this->method = $server['REQUEST_METHOD'] ?? 'GET';
        $this->uri = explode('?', $server['REQUEST_URI'])[0];
        $this->headers = $headers;
    }

    /**
     * @return self
     */
    public static function fromGlobals(): self
    {
        return new self($_GET, $_POST, $_SERVER, $_REQUEST, $_FILES, getallheaders());
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

    /**
     * @param mixed $value
     */
    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function withParsedBody(): Request
    {
        if (
            isset($this->headers['Content-Type'])
            && in_array($this->headers['Content-Type'], ['json', 'application/json', 'text/json'])
        ) {
            $inputJSON = file_get_contents('php://input');
            if ($inputJSON === false) {
                return $this;
            }
            $input = json_decode($inputJSON, true);
            $this->post = array_merge($this->post, $input);
        }

        return $this;
    }
}
