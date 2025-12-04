<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Course List Page - Ultra-Sharp Design
 * ============================================
 */

// Page configuration
$pageTitle = 'Courses';
$currentPage = 'courses';

// Include required files
require_once __DIR__ . '/../../includes/courses/functions.php';
require_once __DIR__ . '/../../includes/course_equipment/functions.php';

// Get equipment for filter dropdown
$equipmentList = getEquipmentForDropdownList();

// Get filter parameters
$filters = [
    'search' => sanitize($_GET['search'] ?? ''),
    'category' => sanitize($_GET['category'] ?? ''),
    'status' => sanitize($_GET['status'] ?? ''),
    'instructor' => sanitize($_GET['instructor'] ?? ''),
    'date_from' => sanitize($_GET['date_from'] ?? ''),
    'date_to' => sanitize($_GET['date_to'] ?? ''),
    'equipment_id' => sanitize($_GET['equipment_id'] ?? ''),
    'sort' => sanitize($_GET['sort'] ?? 'course_date'),
    'order' => sanitize($_GET['order'] ?? 'ASC')
];

// Get current page
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

// Get courses with filters and pagination
$result = getAllCourses($filters, $page);
$courses = $result['courses'];
$pagination = $result['pagination'];

// Include header
require_once __DIR__ . '/../../includes/header.php';
?>

<!-- PAGE HEADER -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-lg font-semibold text-dark-50 tracking-tight">Course Management</h1>
        <p class="text-xs text-dark-400 mt-0.5">Manage all gym courses and classes</p>
    </div>
    <a href="<?php echo url('/courses/create.php'); ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-mint-600 hover:bg-mint-700 text-white text-sm font-medium rounded">
        <i class="fas fa-plus text-xs"></i>
        Add Course
    </a>
</div>

<!-- FILTERS -->
<div class="bg-dark-950 border border-dark-900 rounded-lg mb-4">
    <button type="button" class="flex items-center justify-between w-full px-4 py-2.5 text-left" id="toggleFilters">
        <span class="text-xs font-medium text-dark-300 uppercase tracking-wide">Filters</span>
        <i class="fas fa-chevron-down text-dark-500 text-xs transition-transform" id="filterIcon"></i>
    </button>
    <div class="px-4 pb-4 border-t border-dark-900" id="filtersBody">
        <form action="" method="GET" class="pt-4">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
                <div>
                    <label class="block text-xs text-dark-400 mb-1">Search</label>
                    <input type="text" name="search" value="<?php echo e($filters['search']); ?>" placeholder="Search..." class="w-full px-3 py-1.5 bg-dark-925 border border-dark-800 rounded text-sm text-dark-100 placeholder-dark-500">
                </div>
                <div>
                    <label class="block text-xs text-dark-400 mb-1">Category</label>
                    <select name="category" class="w-full px-3 py-1.5 bg-dark-925 border border-dark-800 rounded text-sm text-dark-100">
                        <option value="">All</option>
                        <?php foreach (COURSE_CATEGORIES as $cat): ?>
                        <option value="<?php echo e($cat); ?>" <?php echo $filters['category'] === $cat ? 'selected' : ''; ?>><?php echo e($cat); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-dark-400 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-1.5 bg-dark-925 border border-dark-800 rounded text-sm text-dark-100">
                        <option value="">All</option>
                        <?php foreach (COURSE_STATUSES as $key => $label): ?>
                        <option value="<?php echo e($key); ?>" <?php echo $filters['status'] === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-dark-400 mb-1">Instructor</label>
                    <input type="text" name="instructor" value="<?php echo e($filters['instructor']); ?>" placeholder="Name..." class="w-full px-3 py-1.5 bg-dark-925 border border-dark-800 rounded text-sm text-dark-100 placeholder-dark-500">
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-3 py-1.5 bg-dark-800 hover:bg-dark-700 text-dark-100 text-xs font-medium rounded">Apply</button>
                <a href="<?php echo url('/courses/'); ?>" class="px-3 py-1.5 text-dark-400 hover:text-dark-200 text-xs">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- TABLE -->
<div class="bg-dark-950 border border-dark-900 rounded-lg overflow-hidden">
    <div class="px-4 py-3 border-b border-dark-900 flex items-center justify-between">
        <span class="text-xs text-dark-400"><?php echo $pagination['total_items']; ?> courses</span>
        <select id="sortSelect" onchange="applySorting()" class="text-xs bg-dark-925 border border-dark-800 rounded px-2 py-1 text-dark-300">
            <option value="course_date-ASC" <?php echo ($filters['sort'] === 'course_date' && $filters['order'] === 'ASC') ? 'selected' : ''; ?>>Date ↑</option>
            <option value="course_date-DESC" <?php echo ($filters['sort'] === 'course_date' && $filters['order'] === 'DESC') ? 'selected' : ''; ?>>Date ↓</option>
            <option value="name-ASC" <?php echo ($filters['sort'] === 'name' && $filters['order'] === 'ASC') ? 'selected' : ''; ?>>Name A-Z</option>
            <option value="name-DESC" <?php echo ($filters['sort'] === 'name' && $filters['order'] === 'DESC') ? 'selected' : ''; ?>>Name Z-A</option>
        </select>
    </div>
    
    <?php if (!empty($courses)): ?>
    <table class="w-full">
        <thead>
            <tr class="text-[10px] font-medium text-dark-500 uppercase tracking-wider border-b border-dark-900">
                <th class="text-left px-4 py-2">Course</th>
                <th class="text-left px-4 py-2">Category</th>
                <th class="text-left px-4 py-2">Date</th>
                <th class="text-left px-4 py-2">Duration</th>
                <th class="text-left px-4 py-2">Status</th>
                <th class="text-center px-4 py-2 w-20">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-dark-900">
            <?php foreach ($courses as $course): ?>
            <tr class="hover:bg-dark-925">
                <td class="px-4 py-3">
                    <a href="<?php echo url('/courses/view.php?id=' . $course['id']); ?>" class="text-sm text-dark-100 hover:text-mint-400"><?php echo e($course['name']); ?></a>
                    <p class="text-xs text-dark-500 mt-0.5"><?php echo e($course['instructor_name']); ?></p>
                </td>
                <td class="px-4 py-3">
                    <span class="text-xs text-dark-300"><?php echo e($course['category']); ?></span>
                </td>
                <td class="px-4 py-3">
                    <span class="text-xs text-dark-200"><?php echo formatDate($course['course_date'], 'M j, Y'); ?></span>
                    <span class="text-xs text-dark-500 block"><?php echo formatTime($course['start_time']); ?></span>
                </td>
                <td class="px-4 py-3">
                    <span class="text-xs text-dark-300"><?php echo formatDuration($course['duration_minutes']); ?></span>
                </td>
                <td class="px-4 py-3">
                    <?php 
                    $statusColor = match($course['status']) {
                        'scheduled' => 'text-mint-500',
                        'in_progress' => 'text-amber-500',
                        'completed' => 'text-dark-400',
                        'cancelled' => 'text-red-500',
                        default => 'text-dark-400'
                    };
                    ?>
                    <span class="text-xs <?php echo $statusColor; ?>"><?php echo e(COURSE_STATUSES[$course['status']] ?? $course['status']); ?></span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-1">
                        <a href="<?php echo url('/courses/view.php?id=' . $course['id']); ?>" class="p-1.5 text-dark-400 hover:text-dark-100" title="View"><i class="fas fa-eye text-xs"></i></a>
                        <a href="<?php echo url('/courses/edit.php?id=' . $course['id']); ?>" class="p-1.5 text-dark-400 hover:text-dark-100" title="Edit"><i class="fas fa-pen text-xs"></i></a>
                        <a href="<?php echo url('/courses/delete.php?id=' . $course['id']); ?>" class="p-1.5 text-dark-400 hover:text-red-400" title="Delete" data-confirm="Delete this course?"><i class="fas fa-trash text-xs"></i></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
    <div class="px-4 py-3 border-t border-dark-900 flex items-center justify-between">
        <span class="text-xs text-dark-500"><?php echo $pagination['offset'] + 1; ?>-<?php echo min($pagination['offset'] + $pagination['per_page'], $pagination['total_items']); ?> of <?php echo $pagination['total_items']; ?></span>
        <div class="flex items-center gap-1">
            <?php if ($pagination['has_prev']): ?>
            <a href="<?php echo url('/courses/', array_merge($filters, ['page' => $pagination['prev_page']])); ?>" class="px-2 py-1 text-xs text-dark-400 hover:text-dark-100">← Prev</a>
            <?php endif; ?>
            <?php if ($pagination['has_next']): ?>
            <a href="<?php echo url('/courses/', array_merge($filters, ['page' => $pagination['next_page']])); ?>" class="px-2 py-1 text-xs text-dark-400 hover:text-dark-100">Next →</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="px-4 py-12 text-center">
        <i class="fas fa-calendar-times text-dark-700 text-2xl mb-2"></i>
        <p class="text-sm text-dark-400">No courses found</p>
        <a href="<?php echo url('/courses/create.php'); ?>" class="inline-block mt-3 text-xs text-mint-500 hover:text-mint-400">+ Add first course</a>
    </div>
    <?php endif; ?>
</div>

<script>
    document.getElementById('toggleFilters').addEventListener('click', function() {
        const body = document.getElementById('filtersBody');
        const icon = document.getElementById('filterIcon');
        body.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    });
    
    function applySorting() {
        const [sort, order] = document.getElementById('sortSelect').value.split('-');
        const url = new URL(window.location.href);
        url.searchParams.set('sort', sort);
        url.searchParams.set('order', order);
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    }
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>