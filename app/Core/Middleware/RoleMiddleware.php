<?php

namespace App\Core\Middleware;

use App\Core\Session;

/**
 * Role-Based Access Control Middleware
 * 
 * Ensures user has required role(s) to access a route.
 * 
 * @package App\Core\Middleware
 */
class RoleMiddleware implements MiddlewareInterface
{
    private array $allowedRoles;

    /**
     * @param array|string $roles Allowed role(s)
     */
    public function __construct($roles)
    {
        $this->allowedRoles = is_array($roles) ? $roles : [$roles];
    }

    /**
     * Handle role check
     * 
     * @param mixed $request
     * @param callable $next
     * @return mixed
     */
    public function handle($request, callable $next)
    {
        Session::start();
        
        $userRole = Session::get('role');
        
        if (!in_array($userRole, $this->allowedRoles)) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'];
            $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
            $baseUrl = rtrim($protocol . "://" . $host . $scriptDir, '/');
            
            http_response_code(403);
            header("Location: {$baseUrl}/dashboard?error=Unauthorized");
            exit();
        }
        
        return $next($request);
    }
}

