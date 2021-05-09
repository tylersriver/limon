<?php

namespace Yocto;

abstract class Action
{
    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var Container
     */
    protected Container $container;

    /**
     * @param  Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $this->request = $request;

        $validateRes = $this->validate($request);
        if ($validateRes->getStatus() !== 200) {
            return $validateRes;
        }

        return $this->action();
    }

    private function validate(Request $request): Response
    {
        // Store handler props
        $reflected = new \ReflectionClass($this);
        foreach ($reflected->getProperties() as $prop) {
            // Get name to match against params
            // USe actual prop name unless @name set
            if (preg_match('/@name\s+([^\s]+)/', $prop->getDocComment(), $matches)) {
                list(, $propName) = $matches;
            } else {
                $propName = $prop->getName();
            }

            // Check request method tied to prop
            if (preg_match('/@method\s+(GET|POST|SERVER)/', $prop->getDocComment(), $matches)) {
                list(, $method) = $matches;
                switch ($method) {
                    case 'GET':
                        $params = $request->getGet();
                        break;
                    case 'POST':
                        $params = $request->getPost();
                        break;
                    case 'SERVER':
                        $params = $request->getServer();
                        break;
                    default:
                        $params = [];
                }
            } else {
                // If no method doesn't come from globals
                continue;
            }

            // Check prop is given in params
            if (!in_array($propName, array_keys($params))) {
                // Die if missing required param
                if (preg_match('/@required\s+([^\s]+)/', $prop->getDocComment(), $matches)) {
                    list(, $required) = $matches;
                    if ($required === 'true') {
                        return new Response(500, "Property '$propName' is required.");
                    }
                }

                // Doesn't exist and not required, let's go to next iteration
                continue;
            }
            $paramValue = $params[$propName];

            // Validate the param
            if (preg_match('/@pattern\s+([^\s]+)/', $prop->getDocComment(), $matches)) {
                list(, $validator) = $matches;

                // Don't allow invalid, regardless of required or not
                $valid = preg_match("/$validator/", $paramValue);
                if ($valid === 0 or $valid === false) {
                    return new Response(500, "Property $propName is invalid.");
                }
            } else {
                // Prop must have validator
                return new Response(500, "Property $propName is invalid.");
            }

            // get type of prop and change type of incoming value
            if (preg_match('/@var\s+([^\s]+)/', $prop->getDocComment(), $matches)) {
                list(, $type) = $matches;
                settype($paramValue, $type);
            } else {
                // Must have a type
                return new Response(500, "Property $propName is must have a type declared.");
            }

            // Set the prop
            $this->{$prop->getName()} = $paramValue;
        }

        return new Response(200);
    }

    /**
     * @return Response
     */
    abstract public function action(): Response;

    /**
     * @param Container $container
     */
    public function setContainer(Container $container): void
    {
        $this->container = $container;
    }
}
