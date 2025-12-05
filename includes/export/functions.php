<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Export Functions
 * ============================================
 */

require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/functions.php';

// ============================================
// CSV EXPORT FUNCTIONS
// ============================================

/**
 * Export courses to CSV
 * 
 * @param array $filters Filter options
 * @return void Outputs CSV directly
 */
function exportCoursesToCSV(array $filters = []): void
{
    // Get all courses (no pagination limit)
    $courses = getCoursesForExport($filters);
    
    // Set headers for CSV download
    $filename = 'courses_export_' . date('Y-m-d_His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add BOM for Excel UTF-8 compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Add header row
    fputcsv($output, [
        'ID',
        'Name',
        'Category',
        'Date',
        'Start Time',
        'Duration (min)',
        'Max Participants',
        'Current Participants',
        'Instructor',
        'Location',
        'Status',
        'Equipment Count',
        'Created At'
    ]);
    
    // Add data rows
    foreach ($courses as $course) {
        fputcsv($output, [
            $course['id'],
            $course['name'],
            $course['category'],
            $course['course_date'],
            $course['start_time'],
            $course['duration_minutes'],
            $course['max_participants'],
            $course['current_participants'],
            $course['instructor_name'],
            $course['location'],
            $course['status'],
            $course['equipment_count'] ?? 0,
            $course['created_at']
        ]);
    }
    
    fclose($output);
    exit;
}

/**
 * Export equipment to CSV
 * 
 * @param array $filters Filter options
 * @return void Outputs CSV directly
 */
function exportEquipmentToCSV(array $filters = []): void
{
    // Get all equipment (no pagination limit)
    $equipment = getEquipmentForExport($filters);
    
    // Set headers for CSV download
    $filename = 'equipment_export_' . date('Y-m-d_His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add BOM for Excel UTF-8 compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Add header row
    fputcsv($output, [
        'ID',
        'Name',
        'Type',
        'Brand',
        'Model',
        'Quantity',
        'Available',
        'Condition',
        'Location',
        'Purchase Date',
        'Purchase Price',
        'Last Maintenance',
        'Next Maintenance',
        'Courses Assigned',
        'Active',
        'Created At'
    ]);
    
    // Add data rows
    foreach ($equipment as $item) {
        fputcsv($output, [
            $item['id'],
            $item['name'],
            $item['type'],
            $item['brand'] ?? '',
            $item['model'] ?? '',
            $item['quantity'],
            $item['available_quantity'],
            $item['equipment_condition'],
            $item['location'] ?? '',
            $item['purchase_date'] ?? '',
            $item['purchase_price'] ?? '',
            $item['last_maintenance'] ?? '',
            $item['next_maintenance'] ?? '',
            $item['courses_count'] ?? 0,
            $item['is_active'] ? 'Yes' : 'No',
            $item['created_at']
        ]);
    }
    
    fclose($output);
    exit;
}

/**
 * Get courses for export (no pagination)
 * 
 * @param array $filters Filter options
 * @return array Courses data
 */
function getCoursesForExport(array $filters = []): array
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
    
    // Build WHERE clause
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Get courses
    $query = "SELECT c.*, 
                (SELECT COUNT(*) FROM course_equipment WHERE course_id = c.id) as equipment_count
              FROM courses c 
              $whereClause 
              ORDER BY c.course_date DESC, c.start_time ASC";
    
    return dbSelect($query, $params, $types);
}

/**
 * Get equipment for export (no pagination)
 * 
 * @param array $filters Filter options
 * @return array Equipment data
 */
function getEquipmentForExport(array $filters = []): array
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
    
    if (!empty($filters['search'])) {
        $where[] = "(name LIKE ? OR brand LIKE ? OR model LIKE ?)";
        $searchTerm = '%' . $filters['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'sss';
    }
    
    // Build WHERE clause
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Get equipment
    $query = "SELECT e.*, 
                (SELECT COUNT(*) FROM course_equipment WHERE equipment_id = e.id) as courses_count
              FROM equipment e 
              $whereClause 
              ORDER BY e.type, e.name";
    
    return dbSelect($query, $params, $types);
}

/**
 * Export courses to PDF
 * 
 * @param array $filters Filter options
 * @return void Outputs PDF directly
 */
function exportCoursesToPDF(array $filters = []): void
{
    $courses = getCoursesForExport($filters);
    
    // Generate HTML for PDF
    $html = generatePDFHeader('Courses Report');
    $html .= '<table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Date</th>
                <th>Time</th>
                <th>Duration</th>
                <th>Instructor</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($courses as $course) {
        $html .= '<tr>
            <td>' . htmlspecialchars($course['name']) . '</td>
            <td>' . htmlspecialchars($course['category']) . '</td>
            <td>' . $course['course_date'] . '</td>
            <td>' . $course['start_time'] . '</td>
            <td>' . $course['duration_minutes'] . ' min</td>
            <td>' . htmlspecialchars($course['instructor_name']) . '</td>
            <td>' . ucfirst($course['status']) . '</td>
        </tr>';
    }
    
    $html .= '</tbody></table>';
    $html .= generatePDFFooter();
    
    outputPDF($html, 'courses_report_' . date('Y-m-d'));
}

/**
 * Export equipment to PDF
 * 
 * @param array $filters Filter options
 * @return void Outputs PDF directly
 */
function exportEquipmentToPDF(array $filters = []): void
{
    $equipment = getEquipmentForExport($filters);
    
    // Generate HTML for PDF
    $html = generatePDFHeader('Equipment Report');
    $html .= '<table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Brand</th>
                <th>Qty</th>
                <th>Condition</th>
                <th>Location</th>
                <th>Active</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($equipment as $item) {
        $html .= '<tr>
            <td>' . htmlspecialchars($item['name']) . '</td>
            <td>' . htmlspecialchars($item['type']) . '</td>
            <td>' . htmlspecialchars($item['brand'] ?? '-') . '</td>
            <td>' . $item['quantity'] . '</td>
            <td>' . ucfirst($item['equipment_condition']) . '</td>
            <td>' . htmlspecialchars($item['location'] ?? '-') . '</td>
            <td>' . ($item['is_active'] ? 'Yes' : 'No') . '</td>
        </tr>';
    }
    
    $html .= '</tbody></table>';
    $html .= generatePDFFooter();
    
    outputPDF($html, 'equipment_report_' . date('Y-m-d'));
}

/**
 * Generate PDF header HTML
 * 
 * @param string $title Report title
 * @return string HTML content
 */
function generatePDFHeader(string $title): string
{
    return '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>' . htmlspecialchars($title) . '</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
            .header { text-align: center; margin-bottom: 30px; }
            .header h1 { color: #4F46E5; margin: 0; font-size: 24px; }
            .header p { color: #666; margin: 5px 0 0; }
            .data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            .data-table th { background-color: #4F46E5; color: white; font-weight: bold; }
            .data-table tr:nth-child(even) { background-color: #f9f9f9; }
            .footer { margin-top: 30px; text-align: center; color: #666; font-size: 10px; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>' . htmlspecialchars($title) . '</h1>
            <p>Generated on ' . date('F j, Y \a\t g:i A') . '</p>
        </div>';
}

/**
 * Generate PDF footer HTML
 * 
 * @return string HTML content
 */
function generatePDFFooter(): string
{
    return '
        <div class="footer">
            <p>Gym Management Platform &copy; ' . date('Y') . '</p>
        </div>
    </body>
    </html>';
}

/**
 * Output HTML as PDF download
 * Uses browser print functionality as a simple alternative
 * 
 * @param string $html HTML content
 * @param string $filename PDF filename (without extension)
 * @return void
 */
function outputPDF(string $html, string $filename): void
{
    // For a simple solution without external libraries,
    // we output HTML that can be printed/saved as PDF
    header('Content-Type: text/html; charset=utf-8');
    
    // Add print-specific styles and auto-print script
    $printHtml = str_replace('</head>', '
        <style media="print">
            @page { margin: 1cm; }
            body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        </style>
        <script>
            window.onload = function() {
                // Set document title for PDF filename
                document.title = "' . htmlspecialchars($filename) . '";
                // Trigger print dialog
                setTimeout(function() { window.print(); }, 500);
            }
        </script>
    </head>', $html);
    
    echo $printHtml;
    exit;
}
