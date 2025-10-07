<?php
/**
 * CausalMMA Admin Panel Configuration - PRODUCTION
 *
 * Production configuration with security hardening
 */

// API Configuration
define('API_BASE_URL', 'https://api.causalmma.com');  // Production API
define('ADMIN_API_KEY', 'YOUR_ADMIN_API_KEY_HERE');  // Replace with your admin key

// Admin Login Credentials
// Generate password hash: php -r "echo password_hash('YOUR_PASSWORD', PASSWORD_BCRYPT);"
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD_HASH', 'YOUR_PASSWORD_HASH_HERE');

// Session Configuration - Production Security
ini_set('session.cookie_httponly', 1);  // Prevent JavaScript access to session cookies
ini_set('session.cookie_secure', 1);    // HTTPS only (REQUIRED for production)
ini_set('session.cookie_samesite', 'Strict');  // CSRF protection
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', 3600);  // 1 hour session timeout
session_start();

// Timezone
date_default_timezone_set('UTC');

// Error Reporting - PRODUCTION (hide errors from users)
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/causalmma_admin_errors.log');  // Log errors to file

/**
 * Make API request to admin endpoints
 */
function apiRequest($endpoint, $method = 'GET', $data = null) {
    $url = API_BASE_URL . $endpoint;

    $headers = [
        'X-Admin-Key: ' . ADMIN_API_KEY,
        'Content-Type: application/json',
        'User-Agent: CausalMMA-Admin/1.0'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);  // Verify SSL certificates
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'PATCH') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        error_log("API Request Error: $error");
        return ['error' => 'Connection error', 'http_code' => 0];
    }

    $decoded = json_decode($response, true);

    if ($httpCode >= 400) {
        $errorMsg = $decoded['detail'] ?? 'API Error';
        error_log("API Error ($httpCode): $errorMsg");
        return ['error' => $errorMsg, 'http_code' => $httpCode];
    }

    return $decoded;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Format date for display
 */
function formatDate($dateString) {
    if (!$dateString) return 'N/A';
    try {
        $date = new DateTime($dateString);
        return $date->format('Y-m-d H:i');
    } catch (Exception $e) {
        return 'Invalid date';
    }
}

/**
 * Format number with commas
 */
function formatNumber($num) {
    return number_format($num);
}

/**
 * Get page title
 */
function getPageTitle($page) {
    $titles = [
        'dashboard' => 'Dashboard',
        'organizations' => 'Organizations',
        'api_keys' => 'API Keys',
        'users' => 'Users',
        'analytics' => 'Analytics',
        'trial_keys' => 'Trial Keys'
    ];
    return $titles[$page] ?? 'Admin Panel';
}

/**
 * Sanitize output for XSS prevention
 */
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function getCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
