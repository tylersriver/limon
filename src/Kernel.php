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

        // Execute Action
        return $route($request);
    }
}