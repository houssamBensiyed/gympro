<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Helper Functions
 * ============================================
 */

require_once __DIR__ . '/db.php';

// ============================================
// SECURITY FUNCTIONS
// ============================================

/**
 * Sanitize input data
 * 
 * @param mixed $data Input data
 * @return mixed Sanitized data
 */
function sanitize($data)
{
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    
    return $data;
}

/**
 * Escape output for HTML display
 * 
 * @param string $value Value to escape
 * @return string Escaped value
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 * 
 * @return string CSRF token
 */
function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * @return bool Is valid
 */
function verifyCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token input field
 * 
 * @return string HTML input field
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(generateCsrfToken()) . '">';
}

// ============================================
// DASHBOARD STATISTICS FUNCTIONS
// ============================================

/**
 * Get dashboard statistics
 * 
 * @return array Dashboard stats
 */
function getDashboardStats(): array
{
    $stats = [
        'total_courses' => 0,
        'scheduled_courses' => 0,
        'completed_courses' => 0,
        'cancelled_courses' => 0,
        'total_equipment' => 0,
        'active_equipment' => 0,
        'equipment_in_maintenance' => 0,
        'total_assignments' => 0
    ];
    
    // Get courses count
    $stats['total_courses'] = dbCount('courses');
    $stats['scheduled_courses'] = dbCount('courses', "status = 'scheduled'");
    $stats['completed_courses'] = dbCount('courses', "status = 'completed'");
    $stats['cancelled_courses'] = dbCount('courses', "status = 'cancelled'");
    
    // Get equipment count
    $stats['total_equipment'] = dbCount('equipment');
    $stats['active_equipment'] = dbCount('equipment', "is_active = 1");
    $stats['equipment_in_maintenance'] = dbCount('equipment', "equipment_condition = 'needs_repair'");
    
    // Get assignments count
    $stats['total_assignments'] = dbCount('course_equipment');
    
    return $stats;
}

/**
 * Get courses distribution by category
 * 
 * @return array Category distribution
 */
function getCoursesByCategory(): array
{
    $query = "SELECT 
                category,
                COUNT(*) as count,
                ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM courses)), 1) as percentage
              FROM courses 
              GROUP BY category 
              ORDER BY count DESC";
    
    return dbSelect($query);
}

/**
 * Get equipment distribution by type
 * 
 * @return array Type distribution
 */
function getEquipmentByType(): array
{
    $query = "SELECT 
                type,
                COUNT(*) as count,
                SUM(quantity) as total_quantity,
                ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM equipment)), 1) as percentage
              FROM equipment 
              GROUP BY type 
              ORDER BY count DESC";
    
    return dbSelect($query);
}

/**
 * Get equipment distribution by condition
 * 
 * @return array Condition distribution
 */
function getEquipmentByCondition(): array
{
    $query = "SELECT 
                equipment_condition as `condition`,
                COUNT(*) as count
              FROM equipment 
              GROUP BY equipment_condition 
              ORDER BY count DESC";
    
    return dbSelect($query);
}

/**
 * Get upcoming courses
 * 
 * @param int $limit Number of courses to return
 * @return array Upcoming courses
 */
function getUpcomingCourses(int $limit = 5): array
{
    $query = "SELECT c.*, 
                (SELECT COUNT(*) FROM course_equipment WHERE course_id = c.id) as equipment_count
              FROM courses c 
              WHERE c.course_date >= CURDATE() AND c.status = 'scheduled'
              ORDER BY c.course_date ASC, c.start_time ASC 
              LIMIT ?";
    
    return dbSelect($query, [$limit], 'i');
}

/**
 * Get recent equipment
 * 
 * @param int $limit Number of equipment to return
 * @return array Recent equipment
 */
function getRecentEquipment(int $limit = 5): array
{
    $query = "SELECT * FROM equipment 
              ORDER BY created_at DESC 
              LIMIT ?";
    
    return dbSelect($query, [$limit], 'i');
}

/**
 * Get low stock equipment
 * 
 * @param int $threshold Minimum quantity threshold
 * @return array Low stock equipment
 */
function getLowStockEquipment(int $threshold = 5): array
{
    $query = "SELECT * FROM equipment 
              WHERE quantity <= ? AND is_active = 1
              ORDER BY quantity ASC";
    
    return dbSelect($query, [$threshold], 'i');
}

/**
 * Get courses this week
 * 
 * @return array This week's courses
 */
function getCoursesThisWeek(): array
{
    $query = "SELECT * FROM courses 
              WHERE course_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
              AND status = 'scheduled'
              ORDER BY course_date ASC, start_time ASC";
    
    return dbSelect($query);
}

// ============================================
// FORMATTING FUNCTIONS
// ============================================

/**
 * Format date for display
 * 
 * @param string|null $date Date string
 * @param string $format Output format
 * @return string Formatted date
 */
function formatDate(?string $date, string $format = 'M d, Y'): string
{
    if ($date === null || $date === '') {
        return '—';
    }
    $datetime = new DateTime($date);
    return $datetime->format($format);
}

/**
 * Format time for display
 * 
 * @param string|null $time Time string
 * @param string $format Output format
 * @return string Formatted time
 */
function formatTime(?string $time, string $format = 'g:i A'): string
{
    if ($time === null || $time === '') {
        return '—';
    }
    $datetime = new DateTime($time);
    return $datetime->format($format);
}

/**
 * Format duration in minutes to human readable
 * 
 * @param int $minutes Duration in minutes
 * @return string Formatted duration
 */
function formatDuration(int $minutes): string
{
    if ($minutes < 60) {
        return $minutes . ' min';
    }
    
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    
    if ($mins === 0) {
        return $hours . ' hr';
    }
    
    return $hours . ' hr ' . $mins . ' min';
}

/**
 * Format number with suffix
 * 
 * @param int $number Number to format
 * @return string Formatted number
 */
function formatNumber(int $number): string
{
    if ($number >= 1000000) {
        return round($number / 1000000, 1) . 'M';
    }
    if ($number >= 1000) {
        return round($number / 1000, 1) . 'K';
    }
    return (string) $number;
}

/**
 * Get status badge class
 * 
 * @param string $status Status value
 * @return string CSS class
 */
function getStatusBadgeClass(string $status): string
{
    $classes = [
        'scheduled' => 'badge-primary',
        'in_progress' => 'badge-warning',
        'ongoing' => 'badge-warning',
        'completed' => 'badge-success',
        'cancelled' => 'badge-danger'
    ];
    
    return $classes[$status] ?? 'badge-secondary';
}

/**
 * Get condition badge class
 * 
 * @param string $condition Condition value
 * @return string CSS class
 */
function getConditionBadgeClass(string $condition): string
{
    $classes = [
        'excellent' => 'badge-success',
        'new' => 'badge-success',
        'good' => 'badge-primary',
        'fair' => 'badge-warning',
        'poor' => 'badge-danger',
        'needs_repair' => 'badge-danger',
        'maintenance' => 'badge-secondary'
    ];
    
    return $classes[$condition] ?? 'badge-secondary';
}

// ============================================
// URL FUNCTIONS
// ============================================

/**
 * Generate URL
 * 
 * @param string $path URL path
 * @param array $params Query parameters
 * @return string Full URL
 */
function url(string $path = '', array $params = []): string
{
    $url = BASE_URL . '/' . ltrim($path, '/');
    
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    
    return $url;
}

/**
 * Get asset URL
 * 
 * @param string $path Asset path
 * @return string Full asset URL
 */
function asset(string $path): string
{
    return ASSETS_URL . '/' . ltrim($path, '/');
}

/**
 * Redirect to URL
 * 
 * @param string $url URL to redirect to
 * @return void
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

// ============================================
// FLASH MESSAGE FUNCTIONS
// ============================================

/**
 * Set flash message
 * 
 * @param string $type Message type (success, error, warning, info)
 * @param string $message Message content
 * @return void
 */
function setFlashMessage(string $type, string $message): void
{
    $_SESSION['flash_messages'][] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get flash messages
 * 
 * @return array Flash messages
 */
function getFlashMessages(): array
{
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

/**
 * Check if there are flash messages
 * 
 * @return bool Has messages
 */
function hasFlashMessages(): bool
{
    return !empty($_SESSION['flash_messages']);
}

// ============================================
// VALIDATION FUNCTIONS
// ============================================

/**
 * Validate required fields
 * 
 * @param array $data Form data
 * @param array $required Required field names
 * @return array Errors
 */
function validateRequired(array $data, array $required): array
{
    $errors = [];
    
    foreach ($required as $field) {
        if (empty($data[$field])) {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }
    
    return $errors;
}

/**
 * Validate email format
 * 
 * @param string $email Email to validate
 * @return bool Is valid
 */
function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate date format
 * 
 * @param string $date Date string
 * @param string $format Expected format
 * @return bool Is valid
 */
function validateDate(string $date, string $format = 'Y-m-d'): bool
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Validate time format
 * 
 * @param string $time Time string
 * @return bool Is valid
 */
function validateTime(string $time): bool
{
    return validateDate($time, 'H:i:s') || validateDate($time, 'H:i');
}

// ============================================
// PAGINATION FUNCTIONS
// ============================================

/**
 * Calculate pagination
 * 
 * @param int $totalItems Total number of items
 * @param int $currentPage Current page number
 * @param int $perPage Items per page
 * @return array Pagination data
 */
function paginate(int $totalItems, int $currentPage = 1, int $perPage = ITEMS_PER_PAGE): array
{
    $totalPages = max(1, ceil($totalItems / $perPage));
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total_items' => $totalItems,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages,
        'prev_page' => $currentPage - 1,
        'next_page' => $currentPage + 1
    ];
}