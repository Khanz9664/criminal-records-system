<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Log;
use App\Models\User;

/**
 * Authentication Controller
 * 
 * Handles user authentication, login, and logout.
 * Implements secure session management and activity logging.
 * 
 * @package App\Controllers
 */
class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function login(): void
    {
        // If already logged in, redirect to dashboard
        Session::start();
        if (Session::has('user_id')) {
            $this->redirect('/dashboard');
            return;
        }

        $this->view('auth/login');
    }

    /**
     * Authenticate user
     */
    public function authenticate(): void
    {
        Session::start();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Basic validation
        if (empty($username) || empty($password)) {
            Session::flash('error', 'All fields are required');
            $this->view('auth/login', ['error' => 'All fields are required']);
            return;
        }

        // Rate limiting for login attempts
        $this->checkLoginRateLimit($username);

        try {
            $userModel = new User();
            $user = $userModel->findByUsername($username);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Login Success
                $this->createSession($user);
                
                // Regenerate session ID to prevent fixation
                Session::regenerate();

                // Update last login
                $userModel->updateLastLogin($user['id']);

                // Log successful login
                Log::info("User logged in", [
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);

                \App\Services\Logger::log("User Logged In");

                // Redirect to intended URL or dashboard
                $intendedUrl = Session::getFlash('intended_url', '/dashboard');
                $this->redirect($intendedUrl);
            } else {
                // Log failed login attempt
                Log::warning("Failed login attempt", [
                    'username' => $username,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);

                Session::flash('error', 'Invalid username or password');
                $this->view('auth/login', ['error' => 'Invalid username or password']);
            }
        } catch (\Exception $e) {
            Log::exception($e, ['action' => 'authentication']);
            Session::flash('error', 'An error occurred. Please try again.');
            $this->view('auth/login', ['error' => 'An error occurred. Please try again.']);
        }
    }

    /**
     * Create user session
     * 
     * @param array $user User data
     */
    private function createSession(array $user): void
    {
        Session::set('user_id', $user['id']);
        Session::set('username', $user['username']);
        Session::set('role', $user['role']);
        Session::set('user_full_name', $user['full_name']);
        Session::set('last_activity', time());
    }

    /**
     * Check login rate limit
     * 
     * @param string $username
     * @throws \Exception If rate limit exceeded
     */
    private function checkLoginRateLimit(string $username): void
    {
        $key = "login_attempts.{$username}";
        $attempts = Session::get($key, ['count' => 0, 'reset' => time() + 900]); // 15 minutes

        // Reset if window expired
        if (time() > $attempts['reset']) {
            $attempts = ['count' => 0, 'reset' => time() + 900];
        }

        // Increment attempts
        $attempts['count']++;
        Session::set($key, $attempts);

        // Block if too many attempts
        if ($attempts['count'] > 5) {
            Log::warning("Login rate limit exceeded", ['username' => $username]);
            Session::flash('error', 'Too many login attempts. Please try again in 15 minutes.');
            $this->view('auth/login', ['error' => 'Too many login attempts. Please try again in 15 minutes.']);
            exit();
        }
    }

    /**
     * Logout user
     */
    public function logout(): void
    {
        Session::start();
        
        if (Session::has('user_id')) {
            $userId = Session::get('user_id');
            $username = Session::get('username');
            
            Log::info("User logged out", [
                'user_id' => $userId,
                'username' => $username
            ]);
            
            \App\Services\Logger::log("User Logged Out");
        }
        
        Session::destroy();
        $this->redirect('/login');
    }
}

