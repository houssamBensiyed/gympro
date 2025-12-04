<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Equipment Management Functions
 * ============================================
 */

require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/functions.php';

// ============================================
// EQUIPMENT CRUD OPERATIONS
// ============================================

/**
 * Get all equipment with optional filtering and pagination
 * 
 * @param array $filters Filter options
 * @param int $page Current page
 * @param int $perPage Items per page
 * @return array Equipment and pagination data
 */
function getAllEquipment(array $filters = [], int $page = 1, int $perPage = ITEMS_PER_PAGE): array
{
    $where = [];
    $params = [];
    $types = '';
    
    // Apply filters
    if (!empty($filters['type'])) {
        $where[] = "type = ?";
        $params[] = $filters['type'];
        $types .= 's';
    }
    
    if (!empty($filters['condition'])) {
        $where[] = "equipment_condition = ?";
        $params[] = $filters['condition'];
        $types .= 's';
    }
    
    if (isset($filters['is_active']) && $filters['is_active'] !== '') {
        $where[] = "is_active = ?";
        $params[] = (int) $filters['is_active'];
        $types .= 'i';
    }
    
    if (!empty($filters['brand'])) {
        $where[] = "brand LIKE ?";
        $params[] = '%' . $filters['brand'] . '%';
        $types .= 's';
    }
    
    if (!empty($filters['location'])) {
        $where[] = "location LIKE ?";
        $params[] = '%' . $filters['location'] . '%';
        $types .= 's';
    }
    
    if (!empty($filters['min_quantity'])) {
        $where[] = "quantity >= ?";
        $params[] = (int) $filters['min_quantity'];
        $types .= 'i';
    }
    
    if (!empty($filters['max_quantity'])) {
        $where[] = "quantity <= ?";
        $params[] = (int) $filters['max_quantity'];
        $types .= 'i';
    }
    
    if (!empty($filters['search'])) {
        $where[] = "(name LIKE ? OR brand LIKE ? OR model LIKE ? OR notes LIKE ?)";
        $searchTerm = '%' . $filters['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'ssss';
    }
    
    if (!empty($filters['course_id'])) {
        $where[] = "id IN (SELECT equipment_id FROM course_equipment WHERE course_id = ?)";
        $params[] = (int) $filters['course_id'];
        $types .= 'i';
    }
    
    // Build WHERE clause
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Count total items
    $countQuery = "SELECT COUNT(*) as count FROM equipment $whereClause";
    $totalItems = dbSelectOne($countQuery, $params, $types)['count'] ?? 0;
    
    // Calculate pagination
    $pagination = paginate($totalItems, $page, $perPage);
    
    // Get sorting
    $sortColumn = $filters['sort'] ?? 'name';
    $sortOrder = $filters['order'] ?? 'ASC';
    
    // Validate sort column
    $allowedColumns = ['name', 'type', 'brand', 'quantity', 'available_quantity', 'equipment_condition', 'location', 'purchase_date', 'created_at'];
    if (!in_array($sortColumn, $allowedColumns)) {
        $sortColumn = 'name';
    }
    
    $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';
    
    // Get equipment
    $query = "SELECT e.*, 
                (SELECT COUNT(*) FROM course_equipment WHERE equipment_id = e.id) as courses_count,
                (SELECT COALESCE(SUM(quantity_needed), 0) FROM course_equipment WHERE equipment_id = e.id) as total_assigned
              FROM equipment e 
              $whereClause 
              ORDER BY $sortColumn $sortOrder
              LIMIT ? OFFSET ?";
    
    $params[] = $pagination['per_page'];
    $params[] = $pagination['offset'];
    $types .= 'ii';
    
    $equipment = dbSelect($query, $params, $types);
    
    return [
        'equipment' => $equipment,
        'pagination' => $pagination
    ];
}

/**
 * Get a single equipment by ID
 * 
 * @param int $id Equipment ID
 * @return array|null Equipment data or null
 */
function getEquipmentById(int $id): ?array
{
    $query = "SELECT e.*, 
                (SELECT COUNT(*) FROM course_equipment WHERE equipment_id = e.id) as courses_count,
                (SELECT COALESCE(SUM(quantity_needed), 0) FROM course_equipment WHERE equipment_id = e.id) as total_assigned
              FROM equipment e 
              WHERE e.id = ?";
    
    return dbSelectOne($query, [$id], 'i');
}

/**
 * Create new equipment
 * 
 * @param array $data Equipment data
 * @return int|false Insert ID or false on failure
 */
function createEquipment(array $data): int|false
{
    $query = "INSERT INTO equipment 
              (name, type, brand, model, quantity, available_quantity, equipment_condition, 
               purchase_date, purchase_price, last_maintenance, next_maintenance, location, notes, is_active) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $availableQty = $data['available_quantity'] ?? $data['quantity'];
    
    $params = [
        $data['name'],
        $data['type'],
        $data['brand'] ?? null,
        $data['model'] ?? null,
        (int) $data['quantity'],
        (int) $availableQty,
        $data['equipment_condition'] ?? 'good',
        !empty($data['purchase_date']) ? $data['purchase_date'] : null,
        !empty($data['purchase_price']) ? (float) $data['purchase_price'] : null,
        !empty($data['last_maintenance']) ? $data['last_maintenance'] : null,
        !empty($data['next_maintenance']) ? $data['next_maintenance'] : null,
        $data['location'] ?? 'Equipment Room',
        $data['notes'] ?? null,
        isset($data['is_active']) ? (int) $data['is_active'] : 1
    ];
    
    return dbInsert($query, $params, 'ssssiissdsssi');
}

/**
 * Update existing equipment
 * 
 * @param int $id Equipment ID
 * @param array $data Equipment data
 * @return int|false Affected rows or false on failure
 */
function updateEquipment(int $id, array $data): int|false
{
    $query = "UPDATE equipment SET 
              name = ?, 
              type = ?, 
              brand = ?, 
              model = ?, 
              quantity = ?, 
              available_quantity = ?, 
              equipment_condition = ?, 
              purchase_date = ?, 
              purchase_price = ?, 
              last_maintenance = ?, 
              next_maintenance = ?, 
              location = ?, 
              notes = ?, 
              is_active = ?
              WHERE id = ?";
    
    $availableQty = $data['available_quantity'] ?? $data['quantity'];
    
    $params = [
        $data['name'],
        $data['type'],
        $data['brand'] ?? null,
        $data['model'] ?? null,
        (int) $data['quantity'],
        (int) $availableQty,
        $data['equipment_condition'] ?? 'good',
        !empty($data['purchase_date']) ? $data['purchase_date'] : null,
        !empty($data['purchase_price']) ? (float) $data['purchase_price'] : null,
        !empty($data['last_maintenance']) ? $data['last_maintenance'] : null,
        !empty($data['next_maintenance']) ? $data['next_maintenance'] : null,
        $data['location'] ?? 'Equipment Room',
        $data['notes'] ?? null,
        isset($data['is_active']) ? (int) $data['is_active'] : 1,
        $id
    ];
    
    return dbUpdate($query, $params, 'ssssiissdsssii');
}

/**
 * Delete equipment
 * 
 * @param int $id Equipment ID
 * @return int|false Affected rows or false on failure
 */
function deleteEquipment(int $id): int|false
{
    // First delete course equipment assignments
    dbDelete("DELETE FROM course_equipment WHERE equipment_id = ?", [$id], 'i');
    
    // Then delete the equipment
    return dbDelete("DELETE FROM equipment WHERE id = ?", [$id], 'i');
}

/**
 * Check if equipment name exists (for validation)
 * 
 * @param string $name Equipment name
 * @param int|null $excludeId Exclude this ID from check
 * @return bool Exists
 */
function equipmentNameExists(string $name, ?int $excludeId = null): bool
{
    $query = "SELECT COUNT(*) as count FROM equipment WHERE name = ?";
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
// EQUIPMENT VALIDATION
// ============================================

/**
 * Validate equipment data
 * 
 * @param array $data Equipment data
 * @param int|null $equipmentId Equipment ID (for updates)
 * @return array Errors array
 */
function validateEquipmentData(array $data, ?int $equipmentId = null): array
{
    $errors = [];
    
    // Name validation
    if (empty($data['name'])) {
        $errors['name'] = 'Equipment name is required.';
    } elseif (strlen($data['name']) > 100) {
        $errors['name'] = 'Equipment name must not exceed 100 characters.';
    } elseif (equipmentNameExists($data['name'], $equipmentId)) {
        $errors['name'] = 'Equipment with this name already exists.';
    }
    
    // Type validation
    if (empty($data['type'])) {
        $errors['type'] = 'Equipment type is required.';
    } elseif (!in_array($data['type'], EQUIPMENT_TYPES)) {
        $errors['type'] = 'Invalid equipment type selected.';
    }
    
    // Quantity validation
    if (!isset($data['quantity']) || $data['quantity'] === '') {
        $errors['quantity'] = 'Quantity is required.';
    } elseif (!is_numeric($data['quantity']) || $data['quantity'] < 0) {
        $errors['quantity'] = 'Quantity must be a non-negative number.';
    } elseif ($data['quantity'] > 9999) {
        $errors['quantity'] = 'Quantity cannot exceed 9999.';
    }
    
    // Available quantity validation
    if (isset($data['available_quantity'])) {
        if (!is_numeric($data['available_quantity']) || $data['available_quantity'] < 0) {
            $errors['available_quantity'] = 'Available quantity must be a non-negative number.';
        } elseif ($data['available_quantity'] > $data['quantity']) {
            $errors['available_quantity'] = 'Available quantity cannot exceed total quantity.';
        }
    }
    
    // Condition validation
    if (!empty($data['equipment_condition']) && !array_key_exists($data['equipment_condition'], EQUIPMENT_CONDITIONS)) {
        $errors['equipment_condition'] = 'Invalid condition selected.';
    }
    
    // Brand validation
    if (!empty($data['brand']) && strlen($data['brand']) > 100) {
        $errors['brand'] = 'Brand name must not exceed 100 characters.';
    }
    
    // Model validation
    if (!empty($data['model']) && strlen($data['model']) > 100) {
        $errors['model'] = 'Model name must not exceed 100 characters.';
    }
    
    // Location validation
    if (!empty($data['location']) && strlen($data['location']) > 100) {
        $errors['location'] = 'Location must not exceed 100 characters.';
    }
    
    // Purchase date validation
    if (!empty($data['purchase_date']) && !validateDate($data['purchase_date'])) {
        $errors['purchase_date'] = 'Invalid purchase date format.';
    }
    
    // Purchase price validation
    if (!empty($data['purchase_price'])) {
        if (!is_numeric($data['purchase_price']) || $data['purchase_price'] < 0) {
            $errors['purchase_price'] = 'Purchase price must be a non-negative number.';
        } elseif ($data['purchase_price'] > 9999999.99) {
            $errors['purchase_price'] = 'Purchase price is too high.';
        }
    }
    
    // Maintenance dates validation
    if (!empty($data['last_maintenance']) && !validateDate($data['last_maintenance'])) {
        $errors['last_maintenance'] = 'Invalid last maintenance date format.';
    }
    
    if (!empty($data['next_maintenance']) && !validateDate($data['next_maintenance'])) {
        $errors['next_maintenance'] = 'Invalid next maintenance date format.';
    }
    
    // Notes validation
    if (!empty($data['notes']) && strlen($data['notes']) > 5000) {
        $errors['notes'] = 'Notes must not exceed 5000 characters.';
    }
    
    return $errors;
}

// ============================================
// EQUIPMENT COURSE FUNCTIONS
// ============================================

/**
 * Get courses using this equipment
 * 
 * @param int $equipmentId Equipment ID
 * @return array Courses list
 */
function getEquipmentCourses(int $equipmentId): array
{
    $query = "SELECT c.*, ce.quantity_needed, ce.assigned_at, ce.id as assignment_id
              FROM courses c
              INNER JOIN course_equipment ce ON c.id = ce.course_id
              WHERE ce.equipment_id = ?
              ORDER BY c.course_date DESC, c.start_time DESC";
    
    return dbSelect($query, [$equipmentId], 'i');
}

/**
 * Get available courses for equipment (not yet assigned)
 * 
 * @param int $equipmentId Equipment ID
 * @return array Available courses
 */
function getAvailableCoursesForEquipment(int $equipmentId): array
{
    $query = "SELECT c.* FROM courses c
              WHERE c.status = 'scheduled'
              AND c.course_date >= CURDATE()
              AND c.id NOT IN (
                  SELECT course_id FROM course_equipment WHERE equipment_id = ?
              )
              ORDER BY c.course_date ASC, c.start_time ASC";
    
    return dbSelect($query, [$equipmentId], 'i');
}

// ============================================
// EQUIPMENT STATISTICS
// ============================================

/**
 * Get equipment statistics
 * 
 * @return array Statistics
 */
function getEquipmentStatistics(): array
{
    $stats = [
        'total' => 0,
        'total_quantity' => 0,
        'active' => 0,
        'inactive' => 0,
        'by_condition' => [],
        'by_type' => [],
        'maintenance_due' => 0,
        'low_stock' => 0
    ];
    
    // Total equipment count
    $stats['total'] = dbCount('equipment');
    $stats['active'] = dbCount('equipment', 'is_active = 1');
    $stats['inactive'] = dbCount('equipment', 'is_active = 0');
    
    // Total quantity
    $query = "SELECT COALESCE(SUM(quantity), 0) as total FROM equipment";
    $result = dbSelectOne($query);
    $stats['total_quantity'] = (int) ($result['total'] ?? 0);
    
    // By condition
    $query = "SELECT equipment_condition, COUNT(*) as count FROM equipment GROUP BY equipment_condition";
    $results = dbSelect($query);
    foreach ($results as $row) {
        $stats['by_condition'][$row['equipment_condition']] = $row['count'];
    }
    
    // By type
    $query = "SELECT type, COUNT(*) as count, SUM(quantity) as total_qty FROM equipment GROUP BY type ORDER BY count DESC";
    $stats['by_type'] = dbSelect($query);
    
    // Maintenance due
    $stats['maintenance_due'] = dbCount('equipment', "next_maintenance IS NOT NULL AND next_maintenance <= CURDATE() AND is_active = 1");
    
    // Low stock (quantity <= 5)
    $stats['low_stock'] = dbCount('equipment', 'quantity <= 5 AND is_active = 1');
    
    return $stats;
}

/**
 * Get equipment needing maintenance
 * 
 * @param int $limit Number of items to return
 * @return array Equipment needing maintenance
 */
function getEquipmentNeedingMaintenance(int $limit = 10): array
{
    $query = "SELECT * FROM equipment 
              WHERE next_maintenance IS NOT NULL 
              AND next_maintenance <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
              AND is_active = 1
              ORDER BY next_maintenance ASC
              LIMIT ?";
    
    return dbSelect($query, [$limit], 'i');
}

/**
 * Get low stock equipment
 * 
 * @param int $threshold Minimum quantity threshold
 * @param int $limit Number of items to return
 * @return array Low stock equipment
 */
function getLowStockEquipmentList(int $threshold = 5, int $limit = 10): array
{
    $query = "SELECT * FROM equipment 
              WHERE quantity <= ? AND is_active = 1
              ORDER BY quantity ASC
              LIMIT ?";
    
    return dbSelect($query, [$threshold, $limit], 'ii');
}

/**
 * Update equipment available quantity
 * 
 * @param int $id Equipment ID
 * @param int $quantity New available quantity
 * @return int|false Affected rows or false
 */
function updateEquipmentAvailableQuantity(int $id, int $quantity): int|false
{
    $query = "UPDATE equipment SET available_quantity = ? WHERE id = ?";
    return dbUpdate($query, [$quantity, $id], 'ii');
}

/**
 * Get all equipment for dropdown
 * 
 * @param bool $activeOnly Only return active equipment
 * @return array Equipment list
 */
function getEquipmentForDropdown(bool $activeOnly = true): array
{
    $where = $activeOnly ? "WHERE is_active = 1" : "";
    $query = "SELECT id, name, type, quantity, available_quantity 
              FROM equipment 
              $where 
              ORDER BY type, name";
    
    return dbSelect($query);
}