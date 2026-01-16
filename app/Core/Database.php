<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database Connection Manager
 * 
 * Singleton pattern for database connection management.
 * Uses Config class for environment-based configuration.
 * 
 * @package App\Core
 */
class Database
{
    private static ?self $instance = null;
    private ?PDO $pdo = null;
    private Config $config;

    private function __construct()
    {
        $this->config = Config::getInstance();
        $this->connect();
    }

    /**
     * Get singleton instance
     * 
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Establish database connection
     * 
     * @throws PDOException
     */
    private function connect(): void
    {
        try {
            $host = $this->config->get('DB_HOST', 'localhost');
            $port = $this->config->get('DB_PORT', 3306);
            $dbname = $this->config->get('DB_NAME', 'CriminalRecordsDB');
            $charset = $this->config->get('DB_CHARSET', 'utf8mb4');
            $user = $this->config->get('DB_USER', 'root');
            $pass = $this->config->get('DB_PASS', '');

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::ATTR_PERSISTENT => false, // Don't use persistent connections for security
            ];

            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Log error instead of exposing it
            error_log("Database Connection Failed: " . $e->getMessage());
            
            if ($this->config->isDebug()) {
                die("Database Connection Failed: " . $e->getMessage());
            } else {
                die("Database connection error. Please contact the administrator.");
            }
        }
    }

    /**
     * Get PDO connection
     * 
     * @return PDO
     */
    public function getConnection(): PDO
    {
        // Reconnect if connection is lost
        if ($this->pdo === null) {
            $this->connect();
        }
        
        return $this->pdo;
    }

    /**
     * Test database connection
     * 
     * @return bool
     */
    public function testConnection(): bool
    {
        try {
            $this->getConnection()->query("SELECT 1");
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
