<?php

namespace App\Core\Security;

use App\Core\Session;
use App\Core\Config;

/**
 * CSRF Protection Handler
 * 
 * Implements Cross-Site Request Forgery protection using token-based validation.
 * Generates and validates tokens for all state-changing operations.
 * 
 * @package App\Core\Security
 */
class CSRF
{
    private const TOKEN_LENGTH = 32;
    private const TOKEN_LIFETIME = 3600; // 1 hour

    /**
     * Generate CSRF token
     * 
     * @return string
     */
    public static function generateToken(): string
    {
        Session::start();
        
        $tokenName = Config::getInstance()->get('CSRF_TOKEN_NAME', '_token');
        $token = bin2hex(random_bytes(self::TOKEN_LENGTH));
        
        // Store token with timestamp
        Session::set("csrf.{$tokenName}", [
            'token' => $token,
            'created' => time()
        ]);
        
        return $token;
    }

    /**
     * Get current CSRF token
     * 
     * @return string
     */
    public static function getToken(): string
    {
        Session::start();
        
        $tokenName = Config::getInstance()->get('CSRF_TOKEN_NAME', '_token');
        $stored = Session::get("csrf.{$tokenName}");
        
        // Generate new token if none exists or expired
        if (!$stored || (time() - $stored['created']) > self::TOKEN_LIFETIME) {
            return self::generateToken();
        }
        
        return $stored['token'];
    }

    /**
     * Validate CSRF token
     * 
     * @param string|null $token Token to validate (if null, reads from POST/GET)
     * @return bool
     */
    public static function validateToken(?string $token = null): bool
    {
        Session::start();
        
        $tokenName = Config::getInstance()->get('CSRF_TOKEN_NAME', '_token');
        
        // Get token from parameter or request
        if ($token === null) {
            $token = $_POST[$tokenName] ?? $_GET[$tokenName] ?? null;
        }
        
        if (empty($token)) {
            return false;
        }
        
        // Get stored token
        $stored = Session::get("csrf.{$tokenName}");
        
        if (!$stored) {
            return false;
        }
        
        // Check if token expired
        if ((time() - $stored['created']) > self::TOKEN_LIFETIME) {
            Session::remove("csrf.{$tokenName}");
            return false;
        }
        
        // Compare tokens using constant-time comparison
        return hash_equals($stored['token'], $token);
    }

    /**
     * Generate CSRF token field HTML
     * 
     * @return string HTML hidden input field
     */
    public static function tokenField(): string
    {
        $tokenName = Config::getInstance()->get('CSRF_TOKEN_NAME', '_token');
        $token = self::getToken();
        
        return sprintf(
            '<input type="hidden" name="%s" value="%s">',
            htmlspecialchars($tokenName, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($token, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Generate CSRF token meta tag for AJAX requests
     * 
     * @return string HTML meta tag
     */
    public static function tokenMeta(): string
    {
        $tokenName = Config::getInstance()->get('CSRF_TOKEN_NAME', '_token');
        $token = self::getToken();
        
        return sprintf(
            '<meta name="csrf-token" content="%s">',
            htmlspecialchars($token, ENT_QUOTES, 'UTF-8')
        );
    }
}

