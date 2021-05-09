<?php

namespace Yocto;

class Client
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string[]
     */
    private $headers;

    /**
     * Client constructor.
     *
     * @param string $baseUrl
     * @param array  $headers
     */
    public function __construct(string $baseUrl, array $headers = [])
    {
        $this->baseUrl = $baseUrl;
        $this->headers = $headers;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function addHeader(string $name, $value): void
    {
        $this->headers[$name] = $value;
    }

    /**
     * @param  string      $method
     * @param  string      $endPoint
     * @param  string|null $body
     * @return Response
     */
    final protected function request(string $method = 'GET', string $endPoint = '', string $body = null): Response
    {
        $url = $this->baseUrl . $endPoint;

        $ch = curl_init();

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);

            if ($body !== null) {
                $this->addHeader('Content-Length', strlen($body));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->flattenHeaders());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        $data = curl_exec($ch);
        if ($data === false) {
            $response = new Response(500, curl_error($ch));
        } else {
            // Extract Headers
            $data = (string)$data;
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($data, 0, $header_size);
            $response = $this::formatResponseHeaders($header);
            $response->setBody(substr($data, $header_size, strlen($data) - $header_size));
        }

        curl_close($ch);

        return $response;
    }

    /**
     * @return array
     */
    private function flattenHeaders(): array
    {
        $flatHeaders = [];
        foreach ($this->headers as $name => $header) {
            $flatHeaders[] = "$name: $header";
        }
        return $flatHeaders;
    }

    /**
     * @param  string $headers
     * @return Response
     */
    private static function formatResponseHeaders(string $headers): Response
    {
        $headersArr = preg_split("/\r\n|\n|\r/", $headers);
        if ($headersArr === false) {
            return new Response();
        }

        $response = new Response();
        foreach ($headersArr as $item) {
            // Empty header
            if ($item === '') {
                continue;
            }

            // Status header
            if (substr($item, 0, 4) === 'HTTP') {
                $statusParts = explode(' ', $item);
                $response->setStatus((int)$statusParts[1]);
                $response->setReasonPhrase(implode(" ", array_slice($statusParts, 2)));
                continue;
            }

            // Other headers
            $parts = explode(': ', $item);
            $response->addHeader($parts[0], $parts[1]);
        }

        return $response;
    }

    /**
     * @param  string      $endPoint
     * @param  string|null $body
     * @return Response
     */
    public function post(string $endPoint = '', string $body = null): Response
    {
        return $this->request('POST', $endPoint, $body);
    }

    /**
     * @param  string $endPoint
     * @param  array  $queryParams
     * @return Response
     */
    public function get(string $endPoint = '', array $queryParams = [])
    {
        return $this->request('GET', $endPoint . '?' . \http_build_query($queryParams));
    }
}
