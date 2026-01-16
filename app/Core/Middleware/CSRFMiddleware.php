<?php

namespace App\Core\Middleware;

use App\Core\Security\CSRF;

/**
 * CSRF Protection Middleware
 * 
 * Validates CSRF tokens for all POST/PUT/DELETE requests.
 * Skips validation for GET requests.
 * 
 * @package App\Core\Middleware
 */
class CSRFMiddleware implements MiddlewareInterface
{
    /**
     * Handle CSRF validation
     * 
     * @param mixed $request
     * @param callable $next
     * @return mixed
     */
    public function handle($request, callable $next)
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        // Only validate state-changing methods
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            if (!CSRF::validateToken()) {
                http_response_code(403);
                die(json_encode([
                    'error' => 'CSRF token validation failed. Please refresh the page and try again.'
                ]));
            }
        }
        
        return $next($request);
    }
}

