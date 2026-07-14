<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OWASP & Security Configuration
    |--------------------------------------------------------------------------
    | Security headers and OWASP Top 10 protections
    */

    'headers' => [
        // Prevent clickjacking
        'X-Frame-Options' => 'DENY',
        
        // Prevent MIME type sniffing
        'X-Content-Type-Options' => 'nosniff',
        
        // Enforce HTTPS
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains; preload',
        
        // Referrer policy
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        
        // Content Security Policy
        'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'",
        
        // Permissions Policy
        'Permissions-Policy' => 'accelerometer=(), camera=(), geolocation=(self), microphone=(), payment=(), usb=()',
    ],

    /*
    |--------------------------------------------------------------------------
    | Encryption Configuration
    |--------------------------------------------------------------------------
    */

    'encryption' => [
        'algorithm' => 'AES-256-GCM',
        'at_rest' => true,
        'in_transit' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Configuration
    |--------------------------------------------------------------------------
    */

    'authentication' => [
        '2fa_enabled' => true,
        'totp_window' => 1, // Number of 30-second windows to check
        'session_timeout' => 24 * 60, // 24 hours in minutes
        'token_lifetime' => 60 * 24, // 24 hours in minutes
        'refresh_token_lifetime' => 7 * 24 * 60, // 7 days in minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */

    'rate_limiting' => [
        'enabled' => true,
        'api_requests_per_minute' => 60,
        'login_attempts_per_minute' => 5,
        'password_reset_per_hour' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Input Validation
    |--------------------------------------------------------------------------
    */

    'input_validation' => [
        'max_file_size' => 10 * 1024 * 1024, // 10MB
        'allowed_file_types' => ['pdf', 'jpg', 'jpeg', 'png', 'xlsx', 'xls', 'docx'],
        'sql_injection_protection' => true,
        'xss_protection' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Logging
    |--------------------------------------------------------------------------
    */

    'audit_logging' => [
        'enabled' => true,
        'log_auth_attempts' => true,
        'log_data_access' => true,
        'log_critical_changes' => true,
        'retention_days' => 365,
    ],

    /*
    |--------------------------------------------------------------------------
    | CORS Configuration
    |--------------------------------------------------------------------------
    */

    'cors' => [
        'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000')),
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
        'exposed_headers' => ['X-Total-Count', 'X-Page-Count'],
        'max_age' => 3600,
    ],
];
