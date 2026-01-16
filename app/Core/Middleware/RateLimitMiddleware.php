<?php

namespace App\Core\Middleware;

use App\Core\Config;
use App\Core\Session;

/**
 * Rate Limiting Middleware
 * 
 * Prevents abuse by limiting requests per time window.
 * Uses simple in-memory storage (can be upgraded to Redis).
 * 
 * @package App\Core\Middleware
 */
class RateLimitMiddleware implements MiddlewareInterface
{
    /**
     * Handle rate limiting
     * 
     * @param mixed $request
     * @param callable $next
     * @return mixed
     */
    public function handle($request, callable $next)
    {
        $config = Config::getInstance();
        
        if (!$config->get('RATE_LIMIT_ENABLED', true)) {
            return $next($request);
        }
        
        Session::start();
        
        $maxRequests = $config->get('RATE_LIMIT_MAX_REQUESTS', 60);
        $window = $config->get('RATE_LIMIT_WINDOW', 60); // seconds
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = "rate_limit.{$ip}";
        
        $data = Session::get($key, [
            'count' => 0,
            'reset' => time() + $window
        ]);
        
        // Reset if window expired
        if (time() > $data['reset']) {
            $data = [
                'count' => 0,
                'reset' => time() + $window
            ];
        }
        
        // Increment counter
        $data['count']++;
        Session::set($key, $data);
        
        // Check if limit exceeded
        if ($data['count'] > $maxRequests) {
            http_response_code(429);
            header('Retry-After: ' . ($data['reset'] - time()));
            die(json_encode([
                'error' => 'Too many requests. Please try again later.',
                'retry_after' => $data['reset'] - time()
            ]));
        }
        
        return $next($request);
    }
}

