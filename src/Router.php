<?php

namespace Yocto;

class Router
{
    private array $routeMap;

    private string $routeRegex = '/^(\/:?[\w|-]*)*$/';

    private string $currentGroup = '';

    private array $previousGroup = [];

    /**
     * @param  string|array    $method
     * @param  string          $path
     * @param  string|callable $class
     * @return Router
     * @throws \Exception
     */
    private function addRoute($method, string $path, $class): Router
    {
        if (!is_array($method)) {
            $method = [$method];
        }

        $path = $this->currentGroup . $path;

        if (preg_match($this->routeRegex, $path) !== 1) {
            throw new \Exception('Invalid route pattern');
        }

        $pathParts = explode('/', $path);
        unset($pathParts[0]);

        foreach ($method as $m) {
            $current = &$this->routeMap[$m];
            foreach ($pathParts as $part) {
                if (!is_array($current)) {
                    $current = [$current];
                }

                if (!array_key_exists($part, $current)) {
                    $current[$part] = [];
                }
                $current = &$current[$part];
            }

            $current = $class;
        }

        return $this;
    }

    /**
     * Add a route group
     *
     * @param string   $name
     * @param callable $callback
     */
    public function addGroup(string $name, callable $callback): void
    {
        $this->previousGroup[] = $this->currentGroup;
        $this->currentGroup .= $name;

        $callback($this);

        $this->currentGroup = array_pop($this->previousGroup);
    }

    /**
     * @param  Request $request
     * @return array|null
     */
    public function parseRoute(Request $request): ?array
    {
        // Grab URI
        $uri = $request->getUri();

        // split URI by /
        $uriParts = explode('/', $uri);
        unset($uriParts[0]);

        // Place method at front
        array_unshift($uriParts, $request->getMethod());

        // traverse the route map
        $routeMap = $this->routeMap;
        $current = &$routeMap;
        $attrs = [];
        foreach ($uriParts as $part) {
            // When the key doesn't exist we look for
            // dynamic attributes
            if (!is_array($current)) {
                return null;
            }

            if (!array_key_exists($part, $current)) {
                foreach ($current as $key => $section) {
                    if (substr($key, 0, 1) === ':') {
                        $attr = substr($key, 1);
                        $attrs[$attr] = $part;
                        $current = &$current[$key];
                        continue 2;
                    }
                }

                return null;
            }
            $current = &$current[$part];
        }

        if (is_array($current)) {
            if (!array_key_exists(0, $current)) {
                return null;
            }

            $current = $current[0];
        }

        return [$current, $attrs];
    }

    /**
     * @param  Request $request
     * @return array|null
     */
    public function dispatch(Request $request): ?array
    {
        // Determine the route
        $route = $this->parseRoute($request);
        if ($route === null) {
            return null;
        }

        $routeExecutable = $route[0];

        // If route is callable return
        if (is_callable($routeExecutable)) {
            return $route;
        }

        // If the route is not callable we assume class
        // This has the benefit of having the container passed to the constructor
        if (!class_exists($routeExecutable)) {
            return null;
        }

        return [container()->get($routeExecutable), $route[1]];
    }

    /**
     * @param  string          $path
     * @param  string|callable $class
     * @return Router
     */
    public function get(string $path, $class)
    {
        return $this->addRoute('GET', $path, $class);
    }

    /**
     * @param  string          $path
     * @param  string|callable $class
     * @return Router
     */
    public function post(string $path, $class)
    {
        return $this->addRoute('POST', $path, $class);
    }

    /**
     * @param  string          $path
     * @param  string|callable $class
     * @return Router
     */
    public function put(string $path, $class)
    {
        return $this->addRoute('PUT', $path, $class);
    }

    /**
     * @param  string          $path
     * @param  string|callable $class
     * @return Router
     */
    public function delete(string $path, $class)
    {
        return $this->addRoute('DELETE', $path, $class);
    }

    /**
     * @param  string          $path
     * @param  string|callable $class
     * @return Router
     */
    public function options(string $path, $class)
    {
        return $this->addRoute('OPTIONS', $path, $class);
    }
}
