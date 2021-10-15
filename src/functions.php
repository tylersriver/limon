<?php

namespace Yocto;

use function function_exists;

if (!function_exists('Yocto\emit')) {
    /**
     * Emit the response to the client
     *
     * @param Response $response
     */
    function emit(Response $response): void
    {
        // Set status
        http_response_code($response->getStatus());

        // Additional headers
        foreach ($response->getHeaders() as $name => $value) {
            header("$name: $value", false);
        }

        // Output body and end request
        echo
            $response->isJsonResponse()
                ? $response->toJson()
                : $response->getBody();

        exit;
    }
}

if (!function_exists('Yocto\redirect')) {
    /**
     * Simplified redirect response creation
     *
     * @param  string $location
     * @return Response
     */
    function redirect(string $location): Response
    {
        return new Response(302, '', ['Location' => $location]);
    }
}

if (!function_exists('Yocto\error')) {
    /**
     * Simplified error response creation
     *
     * @param  string $error
     * @return Response
     */
    function error(string $error): Response
    {
        return (new Response(400))->withError($error);
    }
}

if (!function_exists('Yocto\success')) {
    /**
     * Simplified success response creation
     *
     * @param  string|array $body
     * @return Response
     */
    function success(string|array $body): Response
    {
        return new Response(200, $body);
    }
}

if (!function_exists('Yocto\html')) {
    /**
     * Simplified html response creation
     *
     * @param  string $body
     * @return Response
     */
    function html(string $body): Response
    {
        return (new Response(200, $body))->htmlResponse();
    }
}

if (!function_exists('Yocto\container')) {
    /**
     * @return Container
     */
    function container(): Container
    {
        return App::getInstance()->getContainer();
    }
}

if (!function_exists('Yocto\get')) {
    /**
     * Simplify container()->get() command
     *
     * @param  string $name
     * @return mixed
     */
    function get(string $name)
    {
        return container()->get($name);
    }
}

if (!function_exists('Yocto\render')) {
    /**
     * Simplify retrieving Views instance
     *
     * @param  string $viewName
     * @param  array  $params
     * @return string
     * @throws \Exception
     */
    function render(string $viewName = 'index', array $params = []): string
    {
        if (!container()->has(Views::class)) {
            throw new \Exception('Application container does not have an instance of "Framework\Views"');
        }

        return get(Views::class)->render($viewName, $params);
    }
}

if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}
