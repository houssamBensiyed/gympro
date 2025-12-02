<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Configuration File
 * ============================================
 */

// Prevent direct access
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// ============================================
// ERROR REPORTING
// ============================================
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// ============================================
// TIMEZONE
// ============================================
date_default_timezone_set('UTC');

// ============================================
// SESSION CONFIGURATION
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Strict');
    session_start();
}

// ============================================
// APPLICATION SETTINGS
// ============================================
define('APP_NAME', 'Gym Management Platform');
define('APP_VERSION', '1.0.0');
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_DEBUG', getenv('APP_DEBUG') === 'true');

// ============================================
// DATABASE CONFIGURATION
// ============================================
define('DB_HOST', getenv('DB_HOST') ?: 'mysql');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_DATABASE', getenv('DB_DATABASE') ?: 'gym_management');
define('DB_USERNAME', getenv('DB_USERNAME') ?: 'gym_user');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'gym_password');
define('DB_CHARSET', 'utf8mb4');

// ============================================
// URL CONFIGURATION
// ============================================
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
define('BASE_URL', $protocol . '://' . $host);
define('ASSETS_URL', BASE_URL . '/assets');

// ============================================
// PATH CONFIGURATION
// ============================================
define('INCLUDES_PATH', APP_ROOT . '/includes');
define('PUBLIC_PATH', APP_ROOT . '/public');
define('ASSETS_PATH', PUBLIC_PATH . '/assets');

// ============================================
// PAGINATION SETTINGS
// ============================================
define('ITEMS_PER_PAGE', 10);

// ============================================
// COURSE CATEGORIES
// ============================================
define('COURSE_CATEGORIES', [
    'Yoga',
    'Cardio',
    'Strength',
    'Pilates',
    'Combat',
    'Aquatic',
    'CrossFit',
    'Dance',
    'Wellness',
    'Other'
]);

// ============================================
// COURSE STATUSES
// ============================================
define('COURSE_STATUSES', [
    'scheduled' => 'Scheduled',
    'in_progress' => 'In Progress',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled'
]);

// ============================================
// EQUIPMENT TYPES
// ============================================
define('EQUIPMENT_TYPES', [
    'Yoga',
    'Weights',
    'Cardio',
    'Combat',
    'Accessories',
    'Strength',
    'Recovery',
    'Functional',
    'Pilates',
    'Other'
]);

// ============================================
// EQUIPMENT CONDITIONS
// ============================================
define('EQUIPMENT_CONDITIONS', [
    'excellent' => 'Excellent',
    'good' => 'Good',
    'fair' => 'Fair',
    'poor' => 'Poor',
    'needs_repair' => 'Needs Repair'
]);

// ============================================
// USER ROLES
// ============================================
define('USER_ROLES', [
    'admin' => 'Administrator',
    'staff' => 'Staff Member'
]);