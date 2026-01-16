<?php
/**
 * View Helper Functions
 * 
 * Provides utility functions for use in views.
 * 
 * @package App\Views
 */

use App\Core\Session;
use App\Core\Security\CSRF;

/**
 * Get CSRF token field HTML
 * 
 * @return string
 */
function csrf_field(): string
{
    return CSRF::tokenField();
}

/**
 * Get CSRF token value
 * 
 * @return string
 */
function csrf_token(): string
{
    return CSRF::getToken();
}

/**
 * Get flash message
 * 
 * @param string $key Flash key
 * @param mixed $default Default value
 * @return mixed
 */
function flash(string $key, $default = null)
{
    return Session::getFlash($key, $default);
}

/**
 * Get session value
 * 
 * @param string $key Session key
 * @param mixed $default Default value
 * @return mixed
 */
function session(string $key, $default = null)
{
    return Session::get($key, $default);
}

/**
 * Escape HTML
 * 
 * @param string $string String to escape
 * @return string
 */
function e(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Get base URL
 * 
 * @return string
 */
function base_url(): string
{
    return BASE_URL ?? '';
}

/**
 * Generate asset URL
 * 
 * @param string $path Asset path
 * @return string
 */
function asset(string $path): string
{
    return base_url() . '/' . ltrim($path, '/');
}

