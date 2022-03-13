<?php

namespace Yocto;

use Exception;
use Psr\Http\Message\ResponseInterface;

use function function_exists;

if (!function_exists('Yocto\emit')) {
    /**
     * Emit the response to the client
     *
     * @param ResponseInterface $response
     */
    function emit(ResponseInterface $response, bool $die = true): void
    {
        // Set status
        header(sprintf(
            'HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        ), true, $response->getStatusCode());

        // Additional headers
        foreach ($response->getHeaders() as $name => $values) {
            $first = strtolower($name) !== 'set-cookie';
            foreach ($values as $value) {
                $header = sprintf('%s: %s', $name, $value);
                header($header, $first);
                $first = false;
            }
        }

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

        if($die) {
            exit;
        }
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
