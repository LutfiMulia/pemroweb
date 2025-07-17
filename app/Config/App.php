<?php

namespace App\Config;

class App
{
    const VERSION = '1.0.0';
    const APP_NAME = 'Insidentia';
    const APP_DESCRIPTION = 'Smart Reporting System - Pelaporan Mudah, Pengelolaan Optimal';
    
    // Environment settings
    const ENVIRONMENT = 'development'; // development, testing, production
    const DEBUG = true;
    const TIMEZONE = 'Asia/Jakarta';
    
    // URL settings
    const BASE_URL = 'http://localhost/insidentia';
    const ASSETS_URL = 'http://localhost/insidentia/public/assets';
    
    // Session settings
    const SESSION_LIFETIME = 7200; // 2 hours
    const SESSION_COOKIE_NAME = 'insidentia_session';
    const SESSION_SECURE = false; // Set to true in production with HTTPS
    const SESSION_HTTP_ONLY = true;
    const SESSION_SAME_SITE = 'Strict';
    
    // Security settings
    const CSRF_TOKEN_NAME = 'csrf_token';
    const CSRF_TOKEN_LIFETIME = 3600; // 1 hour
    const MAX_LOGIN_ATTEMPTS = 5;
    const LOGIN_LOCKOUT_TIME = 900; // 15 minutes
    
    // File upload settings
    const MAX_UPLOAD_SIZE = 10485760; // 10MB
    const ALLOWED_FILE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
    const UPLOAD_PATH = 'storage/uploads/';
    
    // Email settings
    const MAIL_FROM = 'noreply@insidentia.local';
    const MAIL_FROM_NAME = 'Insidentia System';
    const MAIL_DRIVER = 'smtp'; // smtp, mail, sendmail
    
    // SMTP settings (if using SMTP)
    const SMTP_HOST = 'smtp.gmail.com';
    const SMTP_PORT = 587;
    const SMTP_USERNAME = '';
    const SMTP_PASSWORD = '';
    const SMTP_ENCRYPTION = 'tls'; // tls, ssl
    
    // Password reset settings
    const PASSWORD_RESET_EXPIRY = 3600; // 1 hour
    const PASSWORD_MIN_LENGTH = 8;
    const PASSWORD_REQUIRE_UPPERCASE = true;
    const PASSWORD_REQUIRE_LOWERCASE = true;
    const PASSWORD_REQUIRE_NUMBERS = true;
    const PASSWORD_REQUIRE_SYMBOLS = false;
    
    // Pagination settings
    const ITEMS_PER_PAGE = 20;
    const MAX_ITEMS_PER_PAGE = 100;
    
    // Cache settings
    const CACHE_ENABLED = true;
    const CACHE_LIFETIME = 3600; // 1 hour
    const CACHE_PATH = 'storage/cache/';
    
    // Log settings
    const LOG_ENABLED = true;
    const LOG_LEVEL = 'debug'; // debug, info, warning, error
    const LOG_PATH = 'storage/logs/';
    const LOG_MAX_SIZE = 10485760; // 10MB
    
    // Rate limiting
    const RATE_LIMIT_ENABLED = true;
    const RATE_LIMIT_REQUESTS = 100;
    const RATE_LIMIT_WINDOW = 3600; // 1 hour
    
    /**
     * Get configuration value
     */
    public static function get($key, $default = null)
    {
        $config = [
            'app.name' => self::APP_NAME,
            'app.description' => self::APP_DESCRIPTION,
            'app.version' => self::VERSION,
            'app.environment' => self::ENVIRONMENT,
            'app.debug' => self::DEBUG,
            'app.timezone' => self::TIMEZONE,
            'app.base_url' => self::BASE_URL,
            'app.assets_url' => self::ASSETS_URL,
            
            'session.lifetime' => self::SESSION_LIFETIME,
            'session.cookie_name' => self::SESSION_COOKIE_NAME,
            'session.secure' => self::SESSION_SECURE,
            'session.http_only' => self::SESSION_HTTP_ONLY,
            'session.same_site' => self::SESSION_SAME_SITE,
            
            'security.csrf_token_name' => self::CSRF_TOKEN_NAME,
            'security.csrf_token_lifetime' => self::CSRF_TOKEN_LIFETIME,
            'security.max_login_attempts' => self::MAX_LOGIN_ATTEMPTS,
            'security.login_lockout_time' => self::LOGIN_LOCKOUT_TIME,
            
            'upload.max_size' => self::MAX_UPLOAD_SIZE,
            'upload.allowed_types' => self::ALLOWED_FILE_TYPES,
            'upload.path' => self::UPLOAD_PATH,
            
            'mail.from' => self::MAIL_FROM,
            'mail.from_name' => self::MAIL_FROM_NAME,
            'mail.driver' => self::MAIL_DRIVER,
            'mail.smtp.host' => self::SMTP_HOST,
            'mail.smtp.port' => self::SMTP_PORT,
            'mail.smtp.username' => self::SMTP_USERNAME,
            'mail.smtp.password' => self::SMTP_PASSWORD,
            'mail.smtp.encryption' => self::SMTP_ENCRYPTION,
            
            'password.reset_expiry' => self::PASSWORD_RESET_EXPIRY,
            'password.min_length' => self::PASSWORD_MIN_LENGTH,
            'password.require_uppercase' => self::PASSWORD_REQUIRE_UPPERCASE,
            'password.require_lowercase' => self::PASSWORD_REQUIRE_LOWERCASE,
            'password.require_numbers' => self::PASSWORD_REQUIRE_NUMBERS,
            'password.require_symbols' => self::PASSWORD_REQUIRE_SYMBOLS,
            
            'pagination.items_per_page' => self::ITEMS_PER_PAGE,
            'pagination.max_items_per_page' => self::MAX_ITEMS_PER_PAGE,
            
            'cache.enabled' => self::CACHE_ENABLED,
            'cache.lifetime' => self::CACHE_LIFETIME,
            'cache.path' => self::CACHE_PATH,
            
            'log.enabled' => self::LOG_ENABLED,
            'log.level' => self::LOG_LEVEL,
            'log.path' => self::LOG_PATH,
            'log.max_size' => self::LOG_MAX_SIZE,
            
            'rate_limit.enabled' => self::RATE_LIMIT_ENABLED,
            'rate_limit.requests' => self::RATE_LIMIT_REQUESTS,
            'rate_limit.window' => self::RATE_LIMIT_WINDOW,
        ];
        
        return $config[$key] ?? $default;
    }
    
    /**
     * Initialize application
     */
    public static function init()
    {
        // Set timezone
        date_default_timezone_set(self::TIMEZONE);
        
        // Set error reporting
        if (self::DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }
        
        // Set memory limit
        ini_set('memory_limit', '256M');
        
        // Set execution time limit
        set_time_limit(300); // 5 minutes
        
        // Session configuration
        ini_set('session.cookie_lifetime', self::SESSION_LIFETIME);
        ini_set('session.cookie_secure', self::SESSION_SECURE);
        ini_set('session.cookie_httponly', self::SESSION_HTTP_ONLY);
        ini_set('session.cookie_samesite', self::SESSION_SAME_SITE);
        ini_set('session.use_strict_mode', 1);
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
