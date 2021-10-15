<?php

namespace Yocto;

/**
 * The Kernel is a middleware implementation
 * that's purpose is to execute the route of the
 * request.
 *
 * It will NOT call the $next object, regardless if it is set or not.
 */
class Kernel extends Middleware
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function process(Request $request): Response
    {
        // Get action
        $route = $this->router->dispatch($request);
        if ($route === null) {
            return error("Route Not Found");
        }

        // Inject attributes to the request
        foreach ($route[1] as $key => $val) {
            $request->setAttribute($key, $val);
        }

        // Execute Action
        return $route[0]($request);
    }
}