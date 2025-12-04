<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Course Management Functions
 * ============================================
 */

require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/functions.php';

// ============================================
// COURSE CRUD OPERATIONS
// ============================================

/**
 * Get all courses with optional filtering and pagination
 * 
 * @param array $filters Filter options
 * @param int $page Current page
 * @param int $perPage Items per page
 * @return array Courses and pagination data
 */
function getAllCourses(array $filters = [], int $page = 1, int $perPage = ITEMS_PER_PAGE): array
{
    $where = [];
    $params = [];
    $types = '';
    
    // Apply filters
    if (!empty($filters['category'])) {
        $where[] = "category = ?";
        $params[] = $filters['category'];
        $types .= 's';
    }
    
    if (!empty($filters['status'])) {
        $where[] = "status = ?";
        $params[] = $filters['status'];
        $types .= 's';
    }
    
    if (!empty($filters['instructor'])) {
        $where[] = "instructor_name LIKE ?";
        $params[] = '%' . $filters['instructor'] . '%';
        $types .= 's';
    }
    
    if (!empty($filters['date_from'])) {
        $where[] = "course_date >= ?";
        $params[] = $filters['date_from'];
        $types .= 's';
    }
    
    if (!empty($filters['date_to'])) {
        $where[] = "course_date <= ?";
        $params[] = $filters['date_to'];
        $types .= 's';
    }
    
    if (!empty($filters['search'])) {
        $where[] = "(name LIKE ? OR description LIKE ? OR instructor_name LIKE ?)";
        $searchTerm = '%' . $filters['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'sss';
    }
    
    if (!empty($filters['equipment_id'])) {
        $where[] = "id IN (SELECT course_id FROM course_equipment WHERE equipment_id = ?)";
        $params[] = (int) $filters['equipment_id'];
        $types .= 'i';
    }
    
    // Build WHERE clause
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Count total items
    $countQuery = "SELECT COUNT(*) as count FROM courses $whereClause";
    $totalItems = dbSelectOne($countQuery, $params, $types)['count'] ?? 0;
    
    // Calculate pagination
    $pagination = paginate($totalItems, $page, $perPage);
    
    // Get sorting
    $sortColumn = $filters['sort'] ?? 'course_date';
    $sortOrder = $filters['order'] ?? 'ASC';
    
    // Validate sort column
    $allowedColumns = ['name', 'category', 'course_date', 'start_time', 'duration_minutes', 'max_participants', 'instructor_name', 'status', 'created_at'];
    if (!in_array($sortColumn, $allowedColumns)) {
        $sortColumn = 'course_date';
    }
    
    $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';
    
    // Get courses
    $query = "SELECT c.*, 
                (SELECT COUNT(*) FROM course_equipment WHERE course_id = c.id) as equipment_count
              FROM courses c 
              $whereClause 
              ORDER BY $sortColumn $sortOrder, c.start_time ASC
              LIMIT ? OFFSET ?";
    
    $params[] = $pagination['per_page'];
    $params[] = $pagination['offset'];
    $types .= 'ii';
    
    $courses = dbSelect($query, $params, $types);
    
    return [
        'courses' => $courses,
        'pagination' => $pagination
    ];
}

/**
 * Get a single course by ID
 * 
 * @param int $id Course ID
 * @return array|null Course data or null
 */
function getCourseById(int $id): ?array
{
    $query = "SELECT c.*, 
                (SELECT COUNT(*) FROM course_equipment WHERE course_id = c.id) as equipment_count
              FROM courses c 
              WHERE c.id = ?";
    
    return dbSelectOne($query, [$id], 'i');
}

/**
 * Create a new course
 * 
 * @param array $data Course data
 * @return int|false Insert ID or false on failure
 */
function createCourse(array $data): int|false
{
    $query = "INSERT INTO courses 
              (name, category, description, course_date, start_time, duration_minutes, 
               max_participants, instructor_name, location, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $params = [
        $data['name'],
        $data['category'],
        $data['description'] ?? null,
        $data['course_date'],
        $data['start_time'],
        (int) $data['duration_minutes'],
        (int) $data['max_participants'],
        $data['instructor_name'],
        $data['location'] ?? 'Main Hall',
        $data['status'] ?? 'scheduled'
    ];
    
    return dbInsert($query, $params, 'sssssiisss');
}

/**
 * Update an existing course
 * 
 * @param int $id Course ID
 * @param array $data Course data
 * @return int|false Affected rows or false on failure
 */
function updateCourse(int $id, array $data): int|false
{
    $query = "UPDATE courses SET 
              name = ?, 
              category = ?, 
              description = ?, 
              course_date = ?, 
              start_time = ?, 
              duration_minutes = ?, 
              max_participants = ?, 
              instructor_name = ?, 
              location = ?, 
              status = ?
              WHERE id = ?";
    
    $params = [
        $data['name'],
        $data['category'],
        $data['description'] ?? null,
        $data['course_date'],
        $data['start_time'],
        (int) $data['duration_minutes'],
        (int) $data['max_participants'],
        $data['instructor_name'],
        $data['location'] ?? 'Main Hall',
        $data['status'] ?? 'scheduled',
        $id
    ];
    
    return dbUpdate($query, $params, 'sssssiisssi');
}

/**
 * Delete a course
 * 
 * @param int $id Course ID
 * @return int|false Affected rows or false on failure
 */
function deleteCourse(int $id): int|false
{
    // First delete course equipment assignments
    dbDelete("DELETE FROM course_equipment WHERE course_id = ?", [$id], 'i');
    
    // Then delete the course
    return dbDelete("DELETE FROM courses WHERE id = ?", [$id], 'i');
}

/**
 * Check if course name exists (for validation)
 * 
 * @param string $name Course name
 * @param int|null $excludeId Exclude this ID from check
 * @return bool Exists
 */
function courseNameExists(string $name, ?int $excludeId = null): bool
{
    $query = "SELECT COUNT(*) as count FROM courses WHERE name = ?";
    $params = [$name];
    $types = 's';
    
    if ($excludeId !== null) {
        $query .= " AND id != ?";
        $params[] = $excludeId;
        $types .= 'i';
    }
    
    $result = dbSelectOne($query, $params, $types);
    return ($result['count'] ?? 0) > 0;
}

// ============================================
// COURSE VALIDATION
// ============================================

/**
 * Validate course data
 * 
 * @param array $data Course data
 * @param int|null $courseId Course ID (for updates)
 * @return array Errors array
 */
function validateCourseData(array $data, ?int $courseId = null): array
{
    $errors = [];
    
    // Name validation
    if (empty($data['name'])) {
        $errors['name'] = 'Course name is required.';
    } elseif (strlen($data['name']) > 100) {
        $errors['name'] = 'Course name must not exceed 100 characters.';
    } elseif (courseNameExists($data['name'], $courseId)) {
        $errors['name'] = 'A course with this name already exists.';
    }
    
    // Category validation
    if (empty($data['category'])) {
        $errors['category'] = 'Category is required.';
    } elseif (!in_array($data['category'], COURSE_CATEGORIES)) {
        $errors['category'] = 'Invalid category selected.';
    }
    
    // Date validation
    if (empty($data['course_date'])) {
        $errors['course_date'] = 'Course date is required.';
    } elseif (!validateDate($data['course_date'])) {
        $errors['course_date'] = 'Invalid date format.';
    }
    
    // Time validation
    if (empty($data['start_time'])) {
        $errors['start_time'] = 'Start time is required.';
    } elseif (!validateTime($data['start_time'])) {
        $errors['start_time'] = 'Invalid time format.';
    }
    
    // Duration validation
    if (empty($data['duration_minutes'])) {
        $errors['duration_minutes'] = 'Duration is required.';
    } elseif (!is_numeric($data['duration_minutes']) || $data['duration_minutes'] < 1) {
        $errors['duration_minutes'] = 'Duration must be a positive number.';
    } elseif ($data['duration_minutes'] > 480) {
        $errors['duration_minutes'] = 'Duration cannot exceed 8 hours (480 minutes).';
    }
    
    // Max participants validation
    if (empty($data['max_participants'])) {
        $errors['max_participants'] = 'Maximum participants is required.';
    } elseif (!is_numeric($data['max_participants']) || $data['max_participants'] < 1) {
        $errors['max_participants'] = 'Maximum participants must be a positive number.';
    } elseif ($data['max_participants'] > 100) {
        $errors['max_participants'] = 'Maximum participants cannot exceed 100.';
    }
    
    // Instructor validation
    if (empty($data['instructor_name'])) {
        $errors['instructor_name'] = 'Instructor name is required.';
    } elseif (strlen($data['instructor_name']) > 100) {
        $errors['instructor_name'] = 'Instructor name must not exceed 100 characters.';
    }
    
    // Status validation
    if (!empty($data['status']) && !array_key_exists($data['status'], COURSE_STATUSES)) {
        $errors['status'] = 'Invalid status selected.';
    }
    
    // Location validation
    if (!empty($data['location']) && strlen($data['location']) > 100) {
        $errors['location'] = 'Location must not exceed 100 characters.';
    }
    
    // Description validation
    if (!empty($data['description']) && strlen($data['description']) > 5000) {
        $errors['description'] = 'Description must not exceed 5000 characters.';
    }
    
    return $errors;
}

// ============================================
// COURSE EQUIPMENT FUNCTIONS
// ============================================

/**
 * Get equipment assigned to a course
 * 
 * @param int $courseId Course ID
 * @return array Assigned equipment
 */
function getCourseEquipment(int $courseId): array
{
    $query = "SELECT e.*, ce.quantity_needed, ce.assigned_at, ce.id as assignment_id
              FROM equipment e
              INNER JOIN course_equipment ce ON e.id = ce.equipment_id
              WHERE ce.course_id = ?
              ORDER BY e.type, e.name";
    
    return dbSelect($query, [$courseId], 'i');
}

/**
 * Get available equipment for a course (not yet assigned)
 * 
 * @param int $courseId Course ID
 * @return array Available equipment
 */
function getAvailableEquipmentForCourse(int $courseId): array
{
    $query = "SELECT e.* FROM equipment e
              WHERE e.is_active = 1
              AND e.id NOT IN (
                  SELECT equipment_id FROM course_equipment WHERE course_id = ?
              )
              ORDER BY e.type, e.name";
    
    return dbSelect($query, [$courseId], 'i');
}

// ============================================
// COURSE STATISTICS
// ============================================

/**
 * Get course statistics
 * 
 * @return array Statistics
 */
function getCourseStatistics(): array
{
    $stats = [
        'total' => 0,
        'by_status' => [],
        'by_category' => [],
        'upcoming_count' => 0,
        'today_count' => 0
    ];
    
    // Total courses
    $stats['total'] = dbCount('courses');
    
    // By status
    $query = "SELECT status, COUNT(*) as count FROM courses GROUP BY status";
    $results = dbSelect($query);
    foreach ($results as $row) {
        $stats['by_status'][$row['status']] = $row['count'];
    }
    
    // By category
    $query = "SELECT category, COUNT(*) as count FROM courses GROUP BY category ORDER BY count DESC";
    $stats['by_category'] = dbSelect($query);
    
    // Upcoming courses
    $stats['upcoming_count'] = dbCount('courses', "course_date > CURDATE() AND status = 'scheduled'");
    
    // Today's courses
    $stats['today_count'] = dbCount('courses', "course_date = CURDATE()");
    
    return $stats;
}