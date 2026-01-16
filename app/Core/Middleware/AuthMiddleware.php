<?php

namespace App\Core\Middleware;

use App\Core\Session;
use App\Core\Config;

/**
 * Authentication Middleware
 * 
 * Ensures user is authenticated before accessing protected routes.
 * Redirects to login if not authenticated.
 * 
 * @package App\Core\Middleware
 */
class AuthMiddleware implements MiddlewareInterface
{
    /**
     * Handle authentication check
     * 
     * @param mixed $request
     * @param callable $next
     * @return mixed
     */
    public function handle($request, callable $next)
    {
        Session::start();
        
        if (!Session::has('user_id')) {
            // Store intended URL for redirect after login
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'];
            $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
            $baseUrl = rtrim($protocol . "://" . $host . $scriptDir, '/');
            Session::flash('intended_url', $_SERVER['REQUEST_URI'] ?? '/dashboard');
            
            header("Location: {$baseUrl}/login");
            exit();
        }
        
        return $next($request);
    }
}

