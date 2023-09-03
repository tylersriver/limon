<?php

namespace Limon;

use Psr\Http\Message\ResponseInterface;

use function function_exists;

if (!function_exists('Limon\emit')) {
    /**
     * Emit the response to the client
     *
     * @param ResponseInterface $response
     */
    function emit(ResponseInterface $response): void
    {
        // Additional headers
        foreach ($response->getHeaders() as $name => $values) {
            $first = strtolower($name) !== 'set-cookie';
            foreach ($values as $value) {
                $header = sprintf('%s: %s', $name, $value);
                header($header, $first);
                $first = false;
            }
        }
        
        // Set status
        header(sprintf(
            'HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        ), true, $response->getStatusCode());

        // Output body and end request
        $body = $response->getBody();
        if ($body->isSeekable()) {
            $body->rewind();
        }
        while (!$body->eof()) {
            echo $body->read(4096);
            if (connection_status() !== CONNECTION_NORMAL) {
                break;
            }
        }
    }
}
