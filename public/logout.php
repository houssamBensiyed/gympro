<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Logout Handler
 * ============================================
 */

// Include required files
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth/functions.php';

// Log out the user
logout();

// Set flash message
setFlashMessage('success', 'You have been successfully logged out.');

// Redirect to login page
redirect(url('/login.php'));
