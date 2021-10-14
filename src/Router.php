<?php

namespace Yocto;

class Router
{
    private array $routeMap;

    private string $routeRegex = '/^(\/[\w]*)*$/';

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

        foreach ($method as $m) {
            $this->routeMap[$m][$path] = $class;
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
     * @return string|null
     */
    public function parseRoute(Request $request): ?string
    {
        return $request->getUri();
    }

    /**
     * @param  string $method
     * @param  string $name
     * @return null|callable
     */
    public function getRouteCallable(string $method, string $name): ?callable
    {
        if (!$this->hasRoute($method, $name)) {
            return null;
        }

        $route = $this->routeMap[$method][$name];

        // If route is callable return
        if (is_callable($route)) {
            return $route;
        }

        // If the route is not callable we assume class
        // This has the benefit of having the container passed to the constructor
        if (!class_exists($route)) {
            return null;
        }
        return container()->get($route);
    }

    /**
     * @param  Request $request
     * @return callable|null
     */
    public function dispatch(Request $request): ?callable
    {
        $action = $this->parseRoute($request);
        $m = $request->getMethod();

        return $this->getRouteCallable($m, $action ?? '');
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

    /**
     * @param  string $method
     * @param  string $path
     * @return bool
     */
    public function hasRoute(string $method, string $path): bool
    {
        return isset($this->routeMap[$method][$path]);
    }
}
