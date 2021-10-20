<?php

namespace Yocto;

use Exception;

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
     * @param  string|array $error
     * @return Response
     */
    function error(string|array $error): Response
    {
        $res = new Response(400);
        if (is_array($error)) {
            $res->setErrors($error);
        } else {
            $res->withError($error);
        }
        return $res;
    }
}

if (!function_exists('Yocto\fail')) {
    /**
     * Simplified error response creation
     *
     * @param  string|array $error
     * @return Response
     */
    function fail(string|array $error): Response
    {
        $res = new Response(500);
        if (is_array($error)) {
            $res->setErrors($error);
        } else {
            $res->withError($error);
        }
        return $res;
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
    function getallheaders(): array
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

if (!function_exists('Yocto\cachedRouter')) {
    /**
     * The cachedRouter function exists to wrap the creation of a Router object
     * and on the first use cache the routes to a file to retrieve the routes
     * from on future requests.
     *
     * @param callable $routerCollector - callable to get an instance of Router with routes
     * @param array $cacheOptions - data needed to cache the routes in a file
     */
    function cachedRouter(callable $routerCollector, array $cacheOptions): Router
    {
        $cacheEnabled = (bool)(isset($cacheOptions['cacheEnabled']) ? $cacheOptions['cacheEnabled'] : false);

        // If cache disabled create router and exit
        if ($cacheEnabled === false) {
            return $routerCollector(new Router());
        }

        // Cache dir must be supplied at least when cache is enabled
        if (!isset($cacheOptions['cacheDir'])) {
            throw new \Exception('Cache dir is required in $cacheOptions when cache is enabled');
        }
        $cacheDir = $cacheOptions['cacheDir'];
        $version = $cacheOptions['version'] ?? 1;
        $cacheFilePath = $cacheDir . "/routesV$version.php";

        // Get cached routes if exist
        if (file_exists($cacheFilePath)) {
            $routesArray = require $cacheFilePath;
            return new Router($routesArray);
        }

        // Otherwise call collector and cache
        /** @var Router */
        $router = $routerCollector(new Router());
        if ($router->hasClosures()) {
            throw new Exception(
                'Unable to cache routes because the router contains routes that resolve to anonymous functions'
            );
        }

        // Create cache and return router
        if (!is_dir($cacheDir)) {
            $created = mkdir($cacheDir, 0775, true);
            if ($created === false) {
                throw new \Exception('The cache directory is not writable ' . $cacheDir);
            }
        }
        $routesArr = $router->getRoutes();
        file_put_contents($cacheFilePath, '<?php return ' . var_export($routesArr, true) . ';');

        return $router;
    }
}
