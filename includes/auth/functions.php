<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Authentication Functions
 * ============================================
 */

require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/functions.php';

// ============================================
// AUTHENTICATION FUNCTIONS
// ============================================

/**
 * Attempt to log in a user
 * 
 * @param string $usernameOrEmail Username or email
 * @param string $password Password
 * @return array ['success' => bool, 'message' => string, 'user' => array|null]
 */
function attemptLogin(string $usernameOrEmail, string $password): array
{
    // Find user by username or email
    $query = "SELECT * FROM users WHERE (username = ? OR email = ?) AND is_active = 1";
    $user = dbSelectOne($query, [$usernameOrEmail, $usernameOrEmail], 'ss');
    
    if (!$user) {
        return [
            'success' => false,
            'message' => 'Invalid username/email or password.',
            'user' => null
        ];
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        return [
            'success' => false,
            'message' => 'Invalid username/email or password.',
            'user' => null
        ];
    }
    
    // Update last login
    dbUpdate("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']], 'i');
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['logged_in'] = true;
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    return [
        'success' => true,
        'message' => 'Login successful.',
        'user' => $user
    ];
}

/**
 * Register a new user
 * 
 * @param array $data User data
 * @return array ['success' => bool, 'message' => string, 'user_id' => int|null]
 */
function registerUser(array $data): array
{
    // Validate data
    $errors = validateRegistrationData($data);
    if (!empty($errors)) {
        return [
            'success' => false,
            'message' => implode(' ', $errors),
            'errors' => $errors,
            'user_id' => null
        ];
    }
    
    // Hash password
    $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
    
    // Insert user
    $query = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'staff')";
    $userId = dbInsert($query, [
        $data['username'],
        $data['email'],
        $hashedPassword
    ], 'sss');
    
    if ($userId === false) {
        return [
            'success' => false,
            'message' => 'Failed to register user. Please try again.',
            'user_id' => null
        ];
    }
    
    return [
        'success' => true,
        'message' => 'Registration successful. You can now log in.',
        'user_id' => $userId
    ];
}

/**
 * Validate registration data
 * 
 * @param array $data Registration data
 * @return array Validation errors
 */
function validateRegistrationData(array $data): array
{
    $errors = [];
    
    // Username validation
    if (empty($data['username'])) {
        $errors['username'] = 'Username is required.';
    } elseif (strlen($data['username']) < 3 || strlen($data['username']) > 50) {
        $errors['username'] = 'Username must be between 3 and 50 characters.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
        $errors['username'] = 'Username can only contain letters, numbers, and underscores.';
    } elseif (usernameExists($data['username'])) {
        $errors['username'] = 'Username is already taken.';
    }
    
    // Email validation
    if (empty($data['email'])) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    } elseif (emailExists($data['email'])) {
        $errors['email'] = 'Email is already registered.';
    }
    
    // Password validation
    if (empty($data['password'])) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($data['password']) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }
    
    // Confirm password
    if (empty($data['confirm_password'])) {
        $errors['confirm_password'] = 'Please confirm your password.';
    } elseif ($data['password'] !== $data['confirm_password']) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }
    
    return $errors;
}

/**
 * Check if username exists
 * 
 * @param string $username Username to check
 * @param int|null $excludeId Exclude this user ID
 * @return bool
 */
function usernameExists(string $username, ?int $excludeId = null): bool
{
    $query = "SELECT COUNT(*) as count FROM users WHERE username = ?";
    $params = [$username];
    $types = 's';
    
    if ($excludeId !== null) {
        $query .= " AND id != ?";
        $params[] = $excludeId;
        $types .= 'i';
    }
    
    $result = dbSelectOne($query, $params, $types);
    return ($result['count'] ?? 0) > 0;
}

/**
 * Check if email exists
 * 
 * @param string $email Email to check
 * @param int|null $excludeId Exclude this user ID
 * @return bool
 */
function emailExists(string $email, ?int $excludeId = null): bool
{
    $query = "SELECT COUNT(*) as count FROM users WHERE email = ?";
    $params = [$email];
    $types = 's';
    
    if ($excludeId !== null) {
        $query .= " AND id != ?";
        $params[] = $excludeId;
        $types .= 'i';
    }
    
    $result = dbSelectOne($query, $params, $types);
    return ($result['count'] ?? 0) > 0;
}

/**
 * Log out the current user
 * 
 * @return void
 */
function logout(): void
{
    // Unset all session variables
    $_SESSION = [];
    
    // Destroy the session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    
    // Destroy the session
    session_destroy();
}

/**
 * Check if user is logged in
 * 
 * @return bool
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Require authentication to access a page
 * Redirects to login if not authenticated
 * 
 * @return void
 */
function requireAuth(): void
{
    if (!isLoggedIn()) {
        setFlashMessage('error', 'Please log in to access this page.');
        redirect(url('/login.php'));
    }
}

/**
 * Get the current logged-in user
 * 
 * @return array|null User data or null
 */
function getCurrentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }
    
    $query = "SELECT id, username, email, role, last_login, created_at FROM users WHERE id = ?";
    return dbSelectOne($query, [$_SESSION['user_id']], 'i');
}

/**
 * Get current user ID
 * 
 * @return int|null User ID or null
 */
function getCurrentUserId(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current username
 * 
 * @return string|null Username or null
 */
function getCurrentUsername(): ?string
{
    return $_SESSION['username'] ?? null;
}

/**
 * Check if current user has a specific role
 * 
 * @param string $role Role to check
 * @return bool
 */
function hasRole(string $role): bool
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

/**
 * Check if current user is an admin
 * 
 * @return bool
 */
function isAdmin(): bool
{
    return hasRole('admin');
}
