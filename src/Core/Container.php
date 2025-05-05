<?php

namespace Tray\Core;

class Container
{
    protected array $bindings = [];
    protected array $instances = [];

    /**
     * Bind closure atau object
     */
    public function bind(string $key, callable $resolver): void
    {
        $this->bindings[$key] = $resolver;
    }

    /**
     * Resolve sekali sahaja (cache instance)
     */
    public function resolve(string $key): mixed
    {
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        if (!isset($this->bindings[$key])) {
            throw new \Exception("Container: No binding found for '$key'");
        }

        $this->instances[$key] = call_user_func($this->bindings[$key]);
        return $this->instances[$key];
    }

    /**
     * Clear binding (optional)
     */
    public function unbind(string $key): void
    {
        unset($this->bindings[$key], $this->instances[$key]);
    }
    public function make(string $class): object
    {
        $reflect = new \ReflectionClass($class);
        $params = $reflect->getConstructor()?->getParameters() ?? [];

        $dependencies = array_map(function($param) {
            return $this->get($param->getType()->getName());
        }, $params);

        return $reflect->newInstanceArgs($dependencies);
    }
    public function get(string $key): mixed
    {
        if (!isset($this->bindings[$key])) {
            throw new Exception("Service $key not bound");
        }
        return $this->bindings[$key]();
    }
}

