<?php

namespace App\Core;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

/**
 * Logging Facade
 * 
 * Provides a simple interface to Monolog logging.
 * Handles file-based logging with rotation.
 * 
 * @package App\Core
 */
class Log
{
    private static ?MonologLogger $logger = null;
    private static Config $config;

    /**
     * Initialize logger
     */
    private static function init(): void
    {
        if (self::$logger !== null) {
            return;
        }

        self::$config = Config::getInstance();
        self::$logger = new MonologLogger('crms');

        $logPath = self::getLogPath();
        $logLevel = self::parseLogLevel(self::$config->get('LOG_LEVEL', 'info'));

        $formatter = new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
            'Y-m-d H:i:s'
        );

        // Try to set up file logging
        if ($logPath !== null && self::ensureLogDirectory($logPath)) {
            try {
                // Rotating file handler (keeps 30 days of logs)
                $fileHandler = new RotatingFileHandler(
                    $logPath . '/app.log',
                    30,
                    $logLevel
                );
                $fileHandler->setFormatter($formatter);
                self::$logger->pushHandler($fileHandler);
            } catch (\Exception $e) {
                // If file handler fails, fall back to error_log
                error_log("Failed to initialize file logging: " . $e->getMessage());
            }
        }

        // Always log to error_log as fallback (or primary if file logging fails)
        try {
            $errorLogHandler = new StreamHandler('php://stderr', $logLevel);
            $errorLogHandler->setFormatter($formatter);
            self::$logger->pushHandler($errorLogHandler);
        } catch (\Exception $e) {
            // Last resort: use PHP error_log
            error_log("Failed to initialize stderr logging: " . $e->getMessage());
        }
    }

    /**
     * Get log path, ensuring it's absolute
     * 
     * @return string|null
     */
    private static function getLogPath(): ?string
    {
        $logPath = self::$config->get('LOG_PATH');
        
        // If not set, use default relative to app root
        if (empty($logPath)) {
            $logPath = dirname(__DIR__, 2) . '/storage/logs';
        }
        
        // Convert to absolute path
        if (substr($logPath, 0, 1) !== '/') {
            $basePath = dirname(__DIR__, 2);
            $logPath = $basePath . '/' . ltrim($logPath, '/');
        }
        
        // Normalize path - get real path of parent directory
        $parentDir = dirname($logPath);
        $realParent = realpath($parentDir);
        if ($realParent === false) {
            // If parent doesn't exist, use the original path
            $realParent = $parentDir;
        }
        $logPath = $realParent . '/' . basename($logPath);
        
        return $logPath;
    }

    /**
     * Ensure log directory exists and is writable
     * 
     * @param string $logPath
     * @return bool
     */
    private static function ensureLogDirectory(string $logPath): bool
    {
        // Check if directory already exists and is writable
        if (is_dir($logPath) && is_writable($logPath)) {
            return true;
        }

        // Try to create directory
        if (!is_dir($logPath)) {
            // Suppress errors and check result
            $oldErrorReporting = error_reporting(0);
            $created = @mkdir($logPath, 0755, true);
            error_reporting($oldErrorReporting);
            
            if (!$created) {
                // Log to error_log that we couldn't create directory
                error_log("CRMS: Cannot create log directory: {$logPath}. Check permissions.");
                return false;
            }
        }

        // Check if directory is writable
        if (!is_writable($logPath)) {
            error_log("CRMS: Log directory is not writable: {$logPath}. Check permissions.");
            return false;
        }

        return true;
    }

    /**
     * Parse log level string to Monolog constant
     * 
     * @param string $level
     * @return int
     */
    private static function parseLogLevel(string $level): int
    {
        return match(strtolower($level)) {
            'debug' => MonologLogger::DEBUG,
            'info' => MonologLogger::INFO,
            'notice' => MonologLogger::NOTICE,
            'warning' => MonologLogger::WARNING,
            'error' => MonologLogger::ERROR,
            'critical' => MonologLogger::CRITICAL,
            'alert' => MonologLogger::ALERT,
            'emergency' => MonologLogger::EMERGENCY,
            default => MonologLogger::INFO,
        };
    }

    /**
     * Log debug message
     * 
     * @param string $message
     * @param array $context
     */
    public static function debug(string $message, array $context = []): void
    {
        self::init();
        self::$logger->debug($message, $context);
    }

    /**
     * Log info message
     * 
     * @param string $message
     * @param array $context
     */
    public static function info(string $message, array $context = []): void
    {
        self::init();
        self::$logger->info($message, $context);
    }

    /**
     * Log warning message
     * 
     * @param string $message
     * @param array $context
     */
    public static function warning(string $message, array $context = []): void
    {
        self::init();
        self::$logger->warning($message, $context);
    }

    /**
     * Log error message
     * 
     * @param string $message
     * @param array $context
     */
    public static function error(string $message, array $context = []): void
    {
        self::init();
        self::$logger->error($message, $context);
    }

    /**
     * Log critical message
     * 
     * @param string $message
     * @param array $context
     */
    public static function critical(string $message, array $context = []): void
    {
        self::init();
        self::$logger->critical($message, $context);
    }

    /**
     * Log exception
     * 
     * @param \Throwable $exception
     * @param array $context
     */
    public static function exception(\Throwable $exception, array $context = []): void
    {
        self::init();
        self::$logger->error($exception->getMessage(), array_merge([
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ], $context));
    }
}

