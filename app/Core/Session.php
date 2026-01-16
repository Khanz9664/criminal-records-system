<?php

namespace App\Core;

/**
 * Secure Session Manager
 * 
 * Handles session initialization with security best practices:
 * - HttpOnly cookies
 * - Secure flag (HTTPS only in production)
 * - SameSite protection
 * - Session regeneration on login
 * - Timeout management
 * 
 * @package App\Core
 */
class Session
{
    private static bool $started = false;

    /**
     * Start secure session
     */
    public static function start(): void
    {
        if (self::$started) {
            return;
        }

        $config = Config::getInstance();

        // Configure session parameters
        ini_set('session.cookie_httponly', $config->get('SESSION_HTTPONLY', true) ? '1' : '0');
        ini_set('session.cookie_secure', $config->get('SESSION_SECURE', false) ? '1' : '0');
        ini_set('session.cookie_samesite', $config->get('SESSION_SAMESITE', 'Lax'));
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_lifetime', $config->get('SESSION_LIFETIME', 7200));

        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check session timeout
        self::checkTimeout($config->get('SESSION_LIFETIME', 7200));

        // Regenerate session ID periodically (every 30 requests) to prevent fixation
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
            $_SESSION['requests'] = 0;
        }

        $_SESSION['requests']++;
        if ($_SESSION['requests'] > 30) {
            self::regenerate();
            $_SESSION['requests'] = 0;
        }

        self::$started = true;
    }

    /**
     * Check if session has timed out
     * 
     * @param int $lifetime Session lifetime in seconds
     */
    private static function checkTimeout(int $lifetime): void
    {
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > $lifetime) {
                self::destroy();
                return;
            }
        }
        $_SESSION['last_activity'] = time();
    }

    /**
     * Regenerate session ID (call after login)
     * 
     * @param bool $deleteOldSession Whether to delete old session
     */
    public static function regenerate(bool $deleteOldSession = true): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id($deleteOldSession);
            $_SESSION['created'] = time();
        }
    }

    /**
     * Get session value
     * 
     * @param string $key Session key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set session value
     * 
     * @param string $key Session key
     * @param mixed $value Session value
     */
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
        $_SESSION['last_activity'] = time();
    }

    /**
     * Check if session key exists
     * 
     * @param string $key Session key
     * @return bool
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Remove session value
     * 
     * @param string $key Session key
     */
    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Destroy session
     */
    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }
            
            session_destroy();
        }
        self::$started = false;
    }

    /**
     * Flash a message to session (one-time use)
     * 
     * @param string $key Flash key
     * @param mixed $value Flash value
     */
    public static function flash(string $key, $value): void
    {
        self::set("_flash.{$key}", $value);
    }

    /**
     * Get and remove flash message
     * 
     * @param string $key Flash key
     * @param mixed $default Default value
     * @return mixed
     */
    public static function getFlash(string $key, $default = null)
    {
        $value = self::get("_flash.{$key}", $default);
        self::remove("_flash.{$key}");
        return $value;
    }
}

