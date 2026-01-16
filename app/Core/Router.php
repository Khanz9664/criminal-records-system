<?php

namespace App\Core;

use App\Core\Middleware\MiddlewareInterface;

/**
 * Router with Middleware Support
 * 
 * Handles routing with support for middleware chains.
 * Supports GET, POST, PUT, DELETE, PATCH methods.
 * 
 * @package App\Core
 */
class Router
{
    protected array $routes = [];
    protected array $middleware = [];

    /**
     * Register GET route
     * 
     * @param string $path Route path
     * @param callable|array $callback Controller callback
     * @param array $middleware Middleware stack
     * @return self
     */
    public function get(string $path, $callback, array $middleware = []): self
    {
        return $this->addRoute('GET', $path, $callback, $middleware);
    }

    /**
     * Register POST route
     * 
     * @param string $path Route path
     * @param callable|array $callback Controller callback
     * @param array $middleware Middleware stack
     * @return self
     */
    public function post(string $path, $callback, array $middleware = []): self
    {
        return $this->addRoute('POST', $path, $callback, $middleware);
    }

    /**
     * Register PUT route
     * 
     * @param string $path Route path
     * @param callable|array $callback Controller callback
     * @param array $middleware Middleware stack
     * @return self
     */
    public function put(string $path, $callback, array $middleware = []): self
    {
        return $this->addRoute('PUT', $path, $callback, $middleware);
    }

    /**
     * Register DELETE route
     * 
     * @param string $path Route path
     * @param callable|array $callback Controller callback
     * @param array $middleware Middleware stack
     * @return self
     */
    public function delete(string $path, $callback, array $middleware = []): self
    {
        return $this->addRoute('DELETE', $path, $callback, $middleware);
    }

    /**
     * Register PATCH route
     * 
     * @param string $path Route path
     * @param callable|array $callback Controller callback
     * @param array $middleware Middleware stack
     * @return self
     */
    public function patch(string $path, $callback, array $middleware = []): self
    {
        return $this->addRoute('PATCH', $path, $callback, $middleware);
    }

    /**
     * Add route to routing table
     * 
     * @param string $method HTTP method
     * @param string $path Route path
     * @param callable|array $callback Controller callback
     * @param array $middleware Middleware stack
     * @return self
     */
    protected function addRoute(string $method, string $path, $callback, array $middleware = []): self
    {
        $this->routes[$method][$path] = [
            'callback' => $callback,
            'middleware' => $middleware
        ];
        return $this;
    }

    /**
     * Resolve and execute route
     */
    public function resolve(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Normalize URI
        $uri = $this->normalizeUri();

        // Get route
        $route = $this->routes[$method][$uri] ?? null;

        if ($route === null) {
            $this->handleNotFound($uri);
            return;
        }

        // Build middleware chain
        $middleware = $route['middleware'] ?? [];
        $callback = $route['callback'];

        // Execute middleware chain
        $response = $this->executeMiddleware($middleware, function() use ($callback) {
            return $this->executeCallback($callback);
        });

        // Output response if it's a string
        if (is_string($response)) {
            echo $response;
        }
    }

    /**
     * Normalize request URI
     * 
     * @return string
     */
    protected function normalizeUri(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        // Handle subdirectory
        $scriptName = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        if ($scriptName !== '/' && $scriptName !== '') {
            $uri = str_replace($scriptName, '', $uri);
        }

        // Strip index.php if present
        if (strpos($uri, '/index.php') === 0) {
            $uri = substr($uri, 10);
        }

        // Normalize
        $uri = '/' . trim($uri, '/');
        if ($uri === '/') {
            $uri = '/dashboard';
        }

        return $uri;
    }

    /**
     * Execute middleware chain
     * 
     * @param array $middleware Middleware stack
     * @param callable $final Final callback
     * @return mixed
     */
    protected function executeMiddleware(array $middleware, callable $final)
    {
        if (empty($middleware)) {
            return $final();
        }

        $next = $final;
        
        // Build chain in reverse order
        for ($i = count($middleware) - 1; $i >= 0; $i--) {
            $middlewareInstance = $this->resolveMiddleware($middleware[$i]);
            $next = function($request = null) use ($middlewareInstance, $next) {
                return $middlewareInstance->handle($request, $next);
            };
        }

        return $next();
    }

    /**
     * Resolve middleware instance
     * 
     * @param string|MiddlewareInterface $middleware
     * @return MiddlewareInterface
     */
    protected function resolveMiddleware($middleware): MiddlewareInterface
    {
        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;
        }

        if (is_string($middleware)) {
            return new $middleware();
        }

        throw new \InvalidArgumentException("Invalid middleware type");
    }

    /**
     * Execute route callback
     * 
     * @param callable|array $callback
     * @return mixed
     */
    protected function executeCallback($callback)
    {
        if (is_array($callback)) {
            [$class, $method] = $callback;
            $controller = new $class();
            return call_user_func([$controller, $method]);
        }

        if (is_callable($callback)) {
            return call_user_func($callback);
        }

        throw new \InvalidArgumentException("Invalid callback type");
    }

    /**
     * Handle 404 Not Found
     * 
     * @param string $uri Requested URI
     */
    protected function handleNotFound(string $uri): void
    {
        http_response_code(404);
        
        $config = Config::getInstance();
        if ($config->isDebug()) {
            echo "404 Not Found (URI: {$uri})";
        } else {
            echo "404 Not Found";
        }
    }
}
