<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Course-Equipment Management Functions
 * ============================================
 */

require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/functions.php';

// ============================================
// COURSE-EQUIPMENT CRUD OPERATIONS
// ============================================

/**
 * Link equipment to a course
 * 
 * @param int $courseId Course ID
 * @param int $equipmentId Equipment ID
 * @param int $quantity Quantity needed
 * @param int|null $userId User ID who made the assignment
 * @return array ['success' => bool, 'message' => string]
 */
function linkEquipmentToCourse(int $courseId, int $equipmentId, int $quantity = 1, ?int $userId = null): array
{
    // Validate course exists
    $course = dbSelectOne("SELECT id, name FROM courses WHERE id = ?", [$courseId], 'i');
    if (!$course) {
        return ['success' => false, 'message' => 'Course not found.'];
    }
    
    // Validate equipment exists
    $equipment = dbSelectOne("SELECT id, name, quantity, available_quantity FROM equipment WHERE id = ?", [$equipmentId], 'i');
    if (!$equipment) {
        return ['success' => false, 'message' => 'Equipment not found.'];
    }
    
    // Check if already linked
    $existing = dbSelectOne(
        "SELECT id FROM course_equipment WHERE course_id = ? AND equipment_id = ?",
        [$courseId, $equipmentId],
        'ii'
    );
    
    if ($existing) {
        // Update existing link
        $result = dbUpdate(
            "UPDATE course_equipment SET quantity_needed = ?, assigned_by = ?, assigned_at = NOW() WHERE id = ?",
            [$quantity, $userId, $existing['id']],
            'iii'
        );
        
        if ($result !== false) {
            return ['success' => true, 'message' => 'Equipment assignment updated successfully.'];
        }
    } else {
        // Create new link
        $result = dbInsert(
            "INSERT INTO course_equipment (course_id, equipment_id, quantity_needed, assigned_by) VALUES (?, ?, ?, ?)",
            [$courseId, $equipmentId, $quantity, $userId],
            'iiii'
        );
        
        if ($result !== false) {
            return ['success' => true, 'message' => 'Equipment linked to course successfully.'];
        }
    }
    
    return ['success' => false, 'message' => 'Failed to link equipment to course.'];
}

/**
 * Unlink equipment from a course
 * 
 * @param int $courseId Course ID
 * @param int $equipmentId Equipment ID
 * @return array ['success' => bool, 'message' => string]
 */
function unlinkEquipmentFromCourse(int $courseId, int $equipmentId): array
{
    $result = dbDelete(
        "DELETE FROM course_equipment WHERE course_id = ? AND equipment_id = ?",
        [$courseId, $equipmentId],
        'ii'
    );
    
    if ($result !== false && $result > 0) {
        return ['success' => true, 'message' => 'Equipment unlinked from course successfully.'];
    }
    
    return ['success' => false, 'message' => 'Assignment not found or already removed.'];
}

/**
 * Check if equipment is linked to a course
 * 
 * @param int $courseId Course ID
 * @param int $equipmentId Equipment ID
 * @return bool True if linked, false otherwise
 */
function isEquipmentLinkedToCourse(int $courseId, int $equipmentId): bool
{
    $result = dbSelectOne(
        "SELECT id FROM course_equipment WHERE course_id = ? AND equipment_id = ?",
        [$courseId, $equipmentId],
        'ii'
    );
    
    return $result !== null;
}

/**
 * Validate assignment form data
 * 
 * @param array $data Form data to validate
 * @return array Array of errors (empty if valid)
 */
function validateAssignment(array $data): array
{
    $errors = [];
    
    // Validate course_id
    if (empty($data['course_id']) || $data['course_id'] <= 0) {
        $errors['course_id'] = 'Please select a course.';
    } else {
        $course = dbSelectOne("SELECT id FROM courses WHERE id = ?", [$data['course_id']], 'i');
        if (!$course) {
            $errors['course_id'] = 'Selected course does not exist.';
        }
    }
    
    // Validate equipment_id
    if (empty($data['equipment_id']) || $data['equipment_id'] <= 0) {
        $errors['equipment_id'] = 'Please select equipment.';
    } else {
        $equipment = dbSelectOne("SELECT id FROM equipment WHERE id = ?", [$data['equipment_id']], 'i');
        if (!$equipment) {
            $errors['equipment_id'] = 'Selected equipment does not exist.';
        }
    }
    
    // Validate quantity_needed
    if (empty($data['quantity_needed']) || $data['quantity_needed'] < 1) {
        $errors['quantity_needed'] = 'Quantity must be at least 1.';
    }
    
    return $errors;
}

/**
 * Get assignment details for display
 * 
 * @param int $courseId Course ID
 * @param int $equipmentId Equipment ID
 * @return array|null Assignment details or null if not found
 */
function getAssignmentDetails(int $courseId, int $equipmentId): ?array
{
    $query = "SELECT ce.*, 
                c.name as course_name, c.category, c.course_date, c.status as course_status,
                e.name as equipment_name, e.type as equipment_type, e.quantity as equipment_quantity
              FROM course_equipment ce
              INNER JOIN courses c ON ce.course_id = c.id
              INNER JOIN equipment e ON ce.equipment_id = e.id
              WHERE ce.course_id = ? AND ce.equipment_id = ?";
    
    return dbSelectOne($query, [$courseId, $equipmentId], 'ii');
}

/**
 * Get all course-equipment assignments with pagination
 * 
 * @param array $filters Filter options
 * @param int $page Current page
 * @param int $perPage Items per page
 * @return array Assignments and pagination data
 */
function getAllAssignments(array $filters = [], int $page = 1, int $perPage = ITEMS_PER_PAGE): array
{
    $where = [];
    $params = [];
    $types = '';
    
    // Apply filters
    if (!empty($filters['course_id'])) {
        $where[] = "ce.course_id = ?";
        $params[] = (int) $filters['course_id'];
        $types .= 'i';
    }
    
    if (!empty($filters['equipment_id'])) {
        $where[] = "ce.equipment_id = ?";
        $params[] = (int) $filters['equipment_id'];
        $types .= 'i';
    }
    
    if (!empty($filters['search'])) {
        $where[] = "(c.name LIKE ? OR e.name LIKE ?)";
        $searchTerm = '%' . $filters['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'ss';
    }
    
    // Build WHERE clause
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Count total items
    $countQuery = "SELECT COUNT(*) as count 
                   FROM course_equipment ce
                   INNER JOIN courses c ON ce.course_id = c.id
                   INNER JOIN equipment e ON ce.equipment_id = e.id
                   $whereClause";
    $totalItems = dbSelectOne($countQuery, $params, $types)['count'] ?? 0;
    
    // Calculate pagination
    $pagination = paginate($totalItems, $page, $perPage);
    
    // Get assignments
    $query = "SELECT ce.*, 
                c.name as course_name, c.category, c.course_date, c.start_time, c.status as course_status,
                e.name as equipment_name, e.type as equipment_type, e.quantity as equipment_quantity
              FROM course_equipment ce
              INNER JOIN courses c ON ce.course_id = c.id
              INNER JOIN equipment e ON ce.equipment_id = e.id
              $whereClause 
              ORDER BY ce.assigned_at DESC
              LIMIT ? OFFSET ?";
    
    $params[] = $pagination['per_page'];
    $params[] = $pagination['offset'];
    $types .= 'ii';
    
    $assignments = dbSelect($query, $params, $types);
    
    return [
        'assignments' => $assignments,
        'pagination' => $pagination
    ];
}

/**
 * Get courses filtered by equipment
 * 
 * @param int $equipmentId Equipment ID
 * @return array Courses using this equipment
 */
function getCoursesByEquipmentId(int $equipmentId): array
{
    $query = "SELECT c.*, ce.quantity_needed, ce.assigned_at
              FROM courses c
              INNER JOIN course_equipment ce ON c.id = ce.course_id
              WHERE ce.equipment_id = ?
              ORDER BY c.course_date DESC, c.start_time ASC";
    
    return dbSelect($query, [$equipmentId], 'i');
}

/**
 * Get equipment filtered by course
 * 
 * @param int $courseId Course ID
 * @return array Equipment assigned to this course
 */
function getEquipmentByCourseId(int $courseId): array
{
    $query = "SELECT e.*, ce.quantity_needed, ce.assigned_at, ce.id as assignment_id
              FROM equipment e
              INNER JOIN course_equipment ce ON e.id = ce.equipment_id
              WHERE ce.course_id = ?
              ORDER BY e.type, e.name";
    
    return dbSelect($query, [$courseId], 'i');
}

/**
 * Get available equipment for linking to a course
 * 
 * @param int $courseId Course ID to exclude already linked equipment
 * @return array Available equipment
 */
function getAvailableEquipmentForLinking(int $courseId): array
{
    $query = "SELECT e.id, e.name, e.type, e.quantity, e.available_quantity
              FROM equipment e
              WHERE e.is_active = 1
              AND e.id NOT IN (
                  SELECT equipment_id FROM course_equipment WHERE course_id = ?
              )
              ORDER BY e.type, e.name";
    
    return dbSelect($query, [$courseId], 'i');
}

/**
 * Get all courses for dropdown (for filtering/linking)
 * 
 * @param bool $scheduledOnly Only return scheduled courses
 * @return array Courses list
 */
function getCoursesForDropdown(bool $scheduledOnly = false): array
{
    $where = $scheduledOnly ? "WHERE status = 'scheduled' AND course_date >= CURDATE()" : "";
    $query = "SELECT id, name, category, course_date, instructor_name 
              FROM courses 
              $where 
              ORDER BY course_date DESC, name";
    
    return dbSelect($query);
}

/**
 * Get all equipment for dropdown (for filtering/linking)
 * 
 * @param bool $activeOnly Only return active equipment
 * @return array Equipment list
 */
function getEquipmentForDropdownList(bool $activeOnly = true): array
{
    $where = $activeOnly ? "WHERE is_active = 1" : "";
    $query = "SELECT id, name, type, quantity 
              FROM equipment 
              $where 
              ORDER BY type, name";
    
    return dbSelect($query);
}

/**
 * Get assignment statistics
 * 
 * @return array Statistics
 */
function getAssignmentStatistics(): array
{
    $stats = [
        'total_assignments' => 0,
        'courses_with_equipment' => 0,
        'equipment_in_use' => 0,
        'total_quantity_assigned' => 0
    ];
    
    // Total assignments
    $result = dbSelectOne("SELECT COUNT(*) as count FROM course_equipment");
    $stats['total_assignments'] = (int) ($result['count'] ?? 0);
    
    // Courses with equipment
    $result = dbSelectOne("SELECT COUNT(DISTINCT course_id) as count FROM course_equipment");
    $stats['courses_with_equipment'] = (int) ($result['count'] ?? 0);
    
    // Equipment in use
    $result = dbSelectOne("SELECT COUNT(DISTINCT equipment_id) as count FROM course_equipment");
    $stats['equipment_in_use'] = (int) ($result['count'] ?? 0);
    
    // Total quantity assigned
    $result = dbSelectOne("SELECT COALESCE(SUM(quantity_needed), 0) as total FROM course_equipment");
    $stats['total_quantity_assigned'] = (int) ($result['total'] ?? 0);
    
    return $stats;
}
