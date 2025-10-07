<?php
/**
 * CausalMMA Admin Panel Configuration - EXAMPLE
 *
 * Copy this file to config.php and update with your values
 */

// API Configuration
define('API_BASE_URL', 'https://your-api.onrender.com');  // Your Render API URL
define('ADMIN_API_KEY', 'admin_YOUR_KEY_HERE');  // Generate with: python scripts/generate_admin_key.py

// Admin Login Credentials
// IMPORTANT: Change these immediately after deployment!
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD_HASH', password_hash('CHANGE_THIS_PASSWORD', PASSWORD_BCRYPT));

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);  // HTTPS only
session_start();

// Timezone
date_default_timezone_set('UTC');

// Error Reporting
// Set to 0 in production!
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Make API request to admin endpoints
 */
function apiRequest($endpoint, $method = 'GET', $data = null) {
    $url = API_BASE_URL . $endpoint;

    $headers = [
        'X-Admin-Key: ' . ADMIN_API_KEY,
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

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
        return ['error' => $error, 'http_code' => $httpCode];
    }

    $decoded = json_decode($response, true);

    if ($httpCode >= 400) {
        return ['error' => $decoded['detail'] ?? 'API Error', 'http_code' => $httpCode];
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
    $date = new DateTime($dateString);
    return $date->format('Y-m-d H:i');
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
?>
