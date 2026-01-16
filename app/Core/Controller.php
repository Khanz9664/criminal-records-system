<?php

namespace App\Core;

use App\Core\Session;
use App\Core\Security\CSRF;
use App\Core\Config;

/**
 * Base Controller
 * 
 * Provides common functionality for all controllers:
 * - View rendering
 * - Redirects
 * - Session access
 * - CSRF token generation
 * - Flash messages
 * 
 * @package App\Core
 */
class Controller
{
    protected Config $config;
    protected string $baseUrl;

    public function __construct()
    {
        $this->config = Config::getInstance();
        $this->baseUrl = $this->getBaseUrl();
    }

    /**
     * Render a view
     * 
     * @param string $view View name (e.g., 'auth/login')
     * @param array $data Data to pass to view
     */
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        
        // Add CSRF token to all views
        $data['csrf_token'] = CSRF::getToken();
        $data['csrf_field'] = CSRF::tokenField();
        $data['csrf_meta'] = CSRF::tokenMeta();
        $data['base_url'] = $this->baseUrl;
        
        extract($data);
        
        $viewPath = dirname(__DIR__, 2) . '/views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            if ($this->config->isDebug()) {
                die("View '{$view}' not found at {$viewPath}");
            } else {
                http_response_code(500);
                die("View not found");
            }
        }

        require_once $viewPath;
    }

    /**
     * Redirect to URL
     * 
     * @param string $url URL to redirect to
     * @param int $code HTTP status code
     */
    protected function redirect(string $url, int $code = 302): void
    {
        // If relative URL, prepend base URL
        if (strpos($url, 'http') !== 0) {
            $url = $this->baseUrl . '/' . ltrim($url, '/');
        }
        
        http_response_code($code);
        header("Location: {$url}");
        exit();
    }

    /**
     * Get base URL
     * 
     * @return string
     */
    protected function getBaseUrl(): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        return rtrim($protocol . "://" . $host . $scriptDir, '/');
    }

    /**
     * Require user to have specific role(s)
     * 
     * @param array|string $roles Required role(s)
     * @throws \Exception If user doesn't have required role
     */
    protected function requireRole($roles): void
    {
        Session::start();

        if (!Session::has('user_id')) {
            Session::flash('intended_url', $_SERVER['REQUEST_URI'] ?? '/dashboard');
            $this->redirect('/login');
        }

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        $userRole = Session::get('role');
        if (!in_array($userRole, $roles)) {
            Session::flash('error', 'You do not have permission to access this resource.');
            $this->redirect('/dashboard');
        }
    }

    /**
     * Get current authenticated user ID
     * 
     * @return int|null
     */
    protected function getUserId(): ?int
    {
        Session::start();
        return Session::get('user_id');
    }

    /**
     * Get current authenticated user role
     * 
     * @return string|null
     */
    protected function getUserRole(): ?string
    {
        Session::start();
        return Session::get('role');
    }

    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    protected function isAuthenticated(): bool
    {
        Session::start();
        return Session::has('user_id');
    }

    /**
     * Return JSON response
     * 
     * @param mixed $data Data to encode
     * @param int $code HTTP status code
     */
    protected function json($data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * Return error response
     * 
     * @param string $message Error message
     * @param int $code HTTP status code
     */
    protected function error(string $message, int $code = 400): void
    {
        $this->json(['error' => $message], $code);
    }

    /**
     * Return success response
     * 
     * @param mixed $data Response data
     * @param int $code HTTP status code
     */
    protected function success($data = null, int $code = 200): void
    {
        $response = ['success' => true];
        if ($data !== null) {
            $response['data'] = $data;
        }
        $this->json($response, $code);
    }
}
