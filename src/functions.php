<?php

namespace Yocto;

/**
 * Emit the response to the client
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

/**
 * Simplified redirect response creation
 * @param string $location
 * @return Response
 */
function redirect(string $location): Response
{
    return new Response(302,  '', ['Location' => $location]);
}

/**
 * Simplified error response creation
 * @param string $error
 * @return Response
 */
function error(string $error): Response
{
    return (new Response(400))->withError($error);
}

/**
 * Simplified success response creation
 * @param $body
 * @return Response
 */
function success($body): Response
{
    return new Response(200, $body);
}

/**
 * Simplified html response creation
 * @param string $body
 * @return Response
 */
function html(string $body): Response
{
    return (new Response(200, $body))->htmlResponse();
}

/**
 * @return Container
 */
function container(): Container
{
    return App::getInstance()->getContainer();
}

/**
 * Simplify container()->get() command
 * @param string $name
 * @return mixed
 */
function get(string $name)
{
    return container()->get($name);
}

/**
 * Simplify retrieving Views instance
 * @param string $viewName
 * @param array $params
 * @return string
 * @throws \Exception
 */
function render(string $viewName = 'index', array $params = []): string
{
    if(!container()->has('Views')) {
        throw new \Exception('Application container does not have an instance of "Framework\Views"');
    }

    return get('Views')->render($viewName, $params);
}