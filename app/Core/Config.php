<?php

namespace App\Core;

/**
 * Configuration Manager
 * 
 * Loads environment variables and provides centralized configuration access.
 * Implements singleton pattern for performance.
 * 
 * @package App\Core
 */
class Config
{
    private static ?self $instance = null;
    private array $config = [];

    private function __construct()
    {
        $this->loadEnvironment();
        $this->loadDefaults();
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Load environment variables from .env file
     */
    private function loadEnvironment(): void
    {
        $envFile = dirname(__DIR__, 2) . '/.env';
        
        if (!file_exists($envFile)) {
            // Try to create from .env.example if it exists
            $exampleFile = dirname(__DIR__, 2) . '/.env.example';
            if (file_exists($exampleFile)) {
                copy($exampleFile, $envFile);
            } else {
                // Use defaults if no .env file exists
                return;
            }
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse KEY=VALUE pairs
            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                $value = trim($value, '"\'');
                
                // Convert boolean strings
                if (in_array(strtolower($value), ['true', 'false'])) {
                    $value = strtolower($value) === 'true';
                }
                
                // Convert numeric strings
                if (is_numeric($value)) {
                    $value = strpos($value, '.') !== false ? (float)$value : (int)$value;
                }
                
                $this->config[$key] = $value;
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }

    /**
     * Load default configuration values
     */
    private function loadDefaults(): void
    {
        $defaults = [
            'APP_ENV' => 'production',
            'APP_DEBUG' => false,
            'APP_URL' => 'http://localhost',
            'DB_HOST' => 'localhost',
            'DB_PORT' => 3306,
            'DB_NAME' => 'CriminalRecordsDB',
            'DB_USER' => 'root',
            'DB_PASS' => '',
            'DB_CHARSET' => 'utf8mb4',
            'SESSION_LIFETIME' => 7200,
            'SESSION_SECURE' => false,
            'SESSION_HTTPONLY' => true,
            'SESSION_SAMESITE' => 'Lax',
            'CSRF_TOKEN_NAME' => '_token',
            'RATE_LIMIT_ENABLED' => true,
            'RATE_LIMIT_MAX_REQUESTS' => 60,
            'RATE_LIMIT_WINDOW' => 60,
            'UPLOAD_MAX_SIZE' => 5242880, // 5MB
            'LOG_CHANNEL' => 'file',
            'LOG_LEVEL' => 'info',
            'LOG_PATH' => dirname(__DIR__, 2) . '/storage/logs',
        ];

        foreach ($defaults as $key => $value) {
            if (!isset($this->config[$key])) {
                $this->config[$key] = $value;
            }
        }
    }

    /**
     * Get configuration value
     * 
     * @param string $key Configuration key (supports dot notation, e.g., 'db.host')
     * @param mixed $default Default value if key not found
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        // Support dot notation
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $value = $this->config;
            
            foreach ($keys as $k) {
                if (!isset($value[$k])) {
                    return $default;
                }
                $value = $value[$k];
            }
            
            return $value;
        }

        return $this->config[$key] ?? $default;
    }

    /**
     * Set configuration value
     * 
     * @param string $key Configuration key
     * @param mixed $value Configuration value
     */
    public function set(string $key, $value): void
    {
        $this->config[$key] = $value;
    }

    /**
     * Check if configuration key exists
     * 
     * @param string $key Configuration key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->config[$key]);
    }

    /**
     * Get all configuration
     * 
     * @return array
     */
    public function all(): array
    {
        return $this->config;
    }

    /**
     * Check if application is in debug mode
     * 
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->get('APP_DEBUG', false) === true;
    }

    /**
     * Check if application is in production
     * 
     * @return bool
     */
    public function isProduction(): bool
    {
        return $this->get('APP_ENV', 'production') === 'production';
    }
}

