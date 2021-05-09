<?php

namespace Yocto;

class App
{
    /**
     * @var Router
     */
    private Router $router;

    /**
     * @var Container
     */
    private Container $container;

    /**
     * @var App
     */
    private static App $instance;

    /**
     * App constructor.
     * @param Container|null $container
     */
    private function __construct(?Container $container = null)
    {
        if($container !== null) {
            $this->container = $container;
        }
    }

    /**
     * Create a new app
     *
     * @param Container|null $container
     * @return App
     */
    public static function create(?Container $container = null) : App
    {
        self::$instance = new App($container);
        return self::$instance;
    }

    /**
     * @return App
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * @param Router $router
     */
    public function setRouter(Router $router): void
    {
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        // CORS Pre flight check
        $method = $request->getMethod();
        if ($method == 'OPTIONS') {
            return new Response(200, '', [
                'Access-Control-Allow-Headers' => $request->getServer()['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']
            ]);
        }

        // Get action
        $route = $this->router->dispatch($request);
        if($route === null) {
            return error("Route Not Found");
        }

        // Execute Action
        return $route($request);
    }

    /**
     * @param Container $container
     */
    public function setContainer(Container $container): void
    {
        $this->container = $container;
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }
}
