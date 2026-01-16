<?php

namespace App\Core\Middleware;

/**
 * Middleware Interface
 * 
 * All middleware classes must implement this interface.
 * Middleware can modify requests, responses, and control flow.
 * 
 * @package App\Core\Middleware
 */
interface MiddlewareInterface
{
    /**
     * Handle the incoming request
     * 
     * @param mixed $request The request object
     * @param callable $next The next middleware in the chain
     * @return mixed
     */
    public function handle($request, callable $next);
}

