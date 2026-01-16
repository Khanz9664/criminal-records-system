<?php

namespace App\Core;

/**
 * Simple Dependency Injection Container
 * 
 * Provides service location and dependency injection capabilities.
 * Implements singleton pattern for shared services.
 * 
 * @package App\Core
 */
class Container
{
    private static ?self $instance = null;
    private array $bindings = [];
    private array $singletons = [];

    private function __construct()
    {
        // Register core services as singletons
        $this->singleton(Config::class, function() {
            return Config::getInstance();
        });

        $this->singleton(Database::class, function() {
            return Database::getInstance();
        });
    }

    /**
     * Get singleton instance
     * 
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Bind a class or interface to a concrete implementation
     * 
     * @param string $abstract Abstract class or interface
     * @param callable|string $concrete Concrete implementation or factory
     */
    public function bind(string $abstract, $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * Register a singleton
     * 
     * @param string $abstract Abstract class or interface
     * @param callable|string $concrete Concrete implementation or factory
     */
    public function singleton(string $abstract, $concrete): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'singleton' => true
        ];
    }

    /**
     * Resolve a class from the container
     * 
     * @param string $abstract Class to resolve
     * @return mixed
     */
    public function make(string $abstract)
    {
        // Check if it's a registered singleton
        if (isset($this->singletons[$abstract])) {
            return $this->singletons[$abstract];
        }

        // Check if it's bound
        if (isset($this->bindings[$abstract])) {
            $binding = $this->bindings[$abstract];
            
            // Handle singleton bindings
            if (is_array($binding) && isset($binding['singleton']) && $binding['singleton']) {
                $concrete = $binding['concrete'];
                $instance = $this->resolveConcrete($concrete);
                $this->singletons[$abstract] = $instance;
                return $instance;
            }
            
            return $this->resolveConcrete($binding);
        }

        // Auto-resolve if not bound
        return $this->autoResolve($abstract);
    }

    /**
     * Resolve concrete implementation
     * 
     * @param callable|string $concrete
     * @return mixed
     */
    protected function resolveConcrete($concrete)
    {
        if (is_callable($concrete)) {
            return $concrete($this);
        }

        if (is_string($concrete)) {
            return $this->autoResolve($concrete);
        }

        return $concrete;
    }

    /**
     * Auto-resolve class dependencies
     * 
     * @param string $class Class name
     * @return object
     */
    protected function autoResolve(string $class): object
    {
        if (!class_exists($class)) {
            throw new \RuntimeException("Class {$class} not found");
        }

        $reflection = new \ReflectionClass($class);

        // If no constructor, instantiate directly
        if (!$reflection->hasMethod('__construct')) {
            return new $class();
        }

        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            
            if ($type === null) {
                // No type hint, try default value
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new \RuntimeException("Cannot resolve parameter {$parameter->getName()} for {$class}");
                }
            } else {
                $typeName = $type->getName();
                
                // Try to resolve from container
                if ($this->has($typeName)) {
                    $dependencies[] = $this->make($typeName);
                } elseif (class_exists($typeName)) {
                    // Recursively resolve
                    $dependencies[] = $this->autoResolve($typeName);
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new \RuntimeException("Cannot resolve dependency {$typeName} for {$class}");
                }
            }
        }

        return $reflection->newInstanceArgs($dependencies);
    }

    /**
     * Check if abstract is bound
     * 
     * @param string $abstract
     * @return bool
     */
    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->singletons[$abstract]);
    }

    /**
     * Get singleton instance directly
     * 
     * @param string $abstract
     * @return mixed|null
     */
    public function getSingleton(string $abstract)
    {
        return $this->singletons[$abstract] ?? null;
    }
}

