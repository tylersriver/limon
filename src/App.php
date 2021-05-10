<?php

namespace Yocto;

use Exception;

class App
{
    /**
     * @var Container
     */
    private Container $container;

    /**
     * @var App
     */
    private static App $instance;

    /**
     * @var middleware
     */
    private Middleware $applicationStack;

    /**
     * App constructor.
     *
     * @param Container|null $container
     */
    private function __construct(
        ?Container $container = null
    ) {
        if ($container !== null) {
            $this->container = $container;
        }
    }

    /**
     * @param Middleware $middleware
     * @throws \Exception
     */
    public function add(Middleware $middleware): void
    {
        if ($this->applicationStack === null) {
            throw new Exception("Kernel has not been bootstrapped, please call setRouter() before adding middeleware");
        }

        $middleware->setNext($this->applicationStack);
        $this->applicationStack = $middleware;
    }

    /**
     * Create a new app
     *
     * @param  Container|null $container
     * @return App
     */
    public static function create(?Container $container = null): App
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
     * NOTE: This function will reset the middleware stack
     *
     * @param Router $router
     */
    public function setRouter(Router $router): void
    {
        $this->applicationStack = new Kernel($router);
    }

    /**
     * @param  Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        return $this->applicationStack->process($request);
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
