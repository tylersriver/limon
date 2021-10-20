<?php

namespace Yocto;

class Response
{
    /**
     *
     *
     * @var int
     */
    private int $status;

    /**
     * @var string
     */
    private string $reasonPhrase;

    /**
     * @var string
     */
    private string $protocol = '1.1';

    /**
     * @var array
     */
    private array $headers = [
        "Content-Type" => "application/json; charset=utf-8",
        "Access-Control-Max-Age" => "1000",
        "Access-Control-Allow-Origin" => "*",
        "Access-Control-Allow-Methods" => "GET, POST, PUT, DELETE, OPTIONS",
        "Access-Control-Allow-Credentials" => "true"
    ];

    /**
     * @var string|array
     */
    private $body;

    /**
     * @var string[]
     */
    private array $errors;

    /**
     * @var bool
     */
    private bool $isJsonResponse = true;

    /**
     * @var string[]
     */
    private array $phrases = [
        200 => 'OK',
        201 => 'Created',
        202 => 'Modified',
        302 => 'Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        500 => 'Internal Server Error'
    ];

    /**
     * Response constructor.
     *
     * @param int|null     $status
     * @param string|array $body
     * @param array        $headers
     */
    public function __construct(?int $status = null, string|array $body = '', array $headers = [])
    {
        if ($status !== null) {
            $this->status = $status;
        }

        $this->headers = array_merge($this->headers, $headers);

        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }


    /**
     * @param string $name
     * @param mixed  $value
     */
    public function addHeader(string $name, $value): void
    {
        $this->headers[$name] = $value;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        if (!is_string($this->body)) {
            return (string)json_encode($this->body);
        }

        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase ?? $this->phrases[$this->status];
    }

    /**
     * @param string $reasonPhrase
     */
    public function setReasonPhrase(string $reasonPhrase): void
    {
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->protocol;
    }

    /**
     * @param string $protocol
     */
    public function setProtocol(string $protocol): void
    {
        $this->protocol = $protocol;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * @return array|string[]
     */
    public function getErrors(): array
    {
        return $this->errors ?? [];
    }

    /**
     * @param  string $error
     * @return Response
     */
    public function withError(string $error): Response
    {
        $this->errors[] = $error;
        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function toJson(): string
    {
        if ($this->body != '') {
            $res['data'] = $this->body;
        }

        // Add error
        if (count($this->getErrors()) > 0) {
            $res['errors'] = $this->getErrors();
        }

        $res = json_encode($res);
        if ($res === false) {
            throw new \Exception("Invalid Json: " . json_last_error_msg());
        }

        return $res;
    }

    /**
     * @return $this
     */
    public function htmlResponse(): Response
    {
        $this->isJsonResponse = false;
        $this->headers['Content-Type'] = 'text/html; charset=utf-8';

        return $this;
    }

    /**
     * @return $this
     */
    public function jsonResponse(): Response
    {
        $this->isJsonResponse = true;
        $this->headers['Content-Type'] = 'application/json; charset=utf-8';

        return $this;
    }

    /**
     * @return bool
     */
    public function isJsonResponse(): bool
    {
        return $this->isJsonResponse;
    }

    /**
     * @return bool
     */
    public function isHtmlResponse(): bool
    {
        return !$this->isJsonResponse;
    }
}
