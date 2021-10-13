<?php

namespace Yocto;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionUnionType;
use Yocto\Attributes\Parameter;
use Yocto\Attributes\Required;

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
        $reflected = new ReflectionClass($this);
        foreach ($reflected->getProperties() as $prop) {
            $parameterAttr = $prop->getAttributes(Parameter::class);
            $requiredAttr = $prop->getAttributes(Required::class);

            // If no Attr we can't do anything else
            if (count($parameterAttr) === 0) {
                continue;
            }

            /** @var Parameter */
            $parameterAttr = $parameterAttr[0]->newInstance();
            $name = $parameterAttr->name;

            // Check request method tied to prop
            $allParameters = $this->collectParameters($request);
            if (!array_key_exists($name, $allParameters)) {
                if (count($requiredAttr) > 0) {
                    return error("Property $name is required.");
                }
                continue;
            }

            $value = $allParameters[$name];

            // Validate the param
            // Don't allow invalid, regardless of required or not
            // Prop must have validator
            $pattern = $parameterAttr->pattern;
            $valid = preg_match("/$pattern/", $value);
            if ($valid === 0 or $valid === false) {
                return new Response(500, "Property $name is invalid.");
            }

            // Set the type
            $type = $prop->getType();
            if ($type === null) {
                return error("Property $name must have a type.");
            } elseif ($type instanceof ReflectionNamedType) {
                $type = $type->getName();
                settype($value, $type);
            }

            $this->{$prop->getName()} = $value;
        }

        return new Response(200);
    }

    private function collectParameters(Request $request): array
    {
        return array_merge(
            $request->getGet(),
            $request->getPost(),
            $request->getServer()
        );
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
