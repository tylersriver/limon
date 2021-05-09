<?php


namespace Yocto;

use Exception;

class Container
{
    /**
     * @var array
     */
    private array $registry;

    /**
     * Container constructor.
     * @param array $definitions
     */
    public function __construct(array $definitions = [])
    {
        $this->registry = $definitions;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->registry[$key]);
    }

    /**
     * Set a value in the registry
     * @param string $key
     * @param mixed $val
     */
    public function set(string $key, $val): void
    {
        $this->registry[$key] = $val;
    }

    /**
     * @param string $key
     * @return mixed
     * @throws Exception
     */
    public function get(string $key)
    {
        if(!$this->has($key)) {
            throw new Exception('Key not found');
        }
        return $this->registry[$key];
    }
}