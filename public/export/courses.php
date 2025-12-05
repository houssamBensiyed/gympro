<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Export Courses Page - Ultra-Sharp Design
 * ============================================
 */

$pageTitle = 'Export Courses';
$currentPage = 'export-courses';

require_once __DIR__ . '/../../includes/courses/functions.php';
require_once __DIR__ . '/../../includes/export/functions.php';

// Handle export request
if (isset($_GET['format'])) {
    $format = sanitize($_GET['format']);
    
    $filters = [
        'search' => sanitize($_GET['search'] ?? ''),
        'category' => sanitize($_GET['category'] ?? ''),
        'status' => sanitize($_GET['status'] ?? ''),
        'instructor' => sanitize($_GET['instructor'] ?? ''),
        'date_from' => sanitize($_GET['date_from'] ?? ''),
        'date_to' => sanitize($_GET['date_to'] ?? '')
    ];
    
    if ($format === 'csv') {
        exportCoursesToCSV($filters);
    } elseif ($format === 'pdf') {
        exportCoursesToPDF($filters);
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- PAGE HEADER -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-lg font-semibold text-dark-50 tracking-tight">Export Courses</h1>
        <p class="text-xs text-dark-400 mt-0.5">Download course data in CSV or PDF format</p>
    </div>
    <a href="<?php echo url('/courses/'); ?>" class="px-3 py-1.5 text-xs text-dark-300 hover:text-white bg-dark-900 rounded">
        ← Back to Courses
    </a>
</div>

<!-- EXPORT FORM -->
<div class="max-w-2xl mx-auto">
    <div class="bg-dark-950 border border-dark-900 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-dark-900">
            <h2 class="text-base font-semibold text-white">Export Filters</h2>
            <p class="text-xs text-dark-400 mt-0.5">Filter data before exporting</p>
        </div>
        
        <form method="GET" action="" class="p-6">
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-medium text-dark-300 mb-1.5">Search</label>
                    <input type="text" name="search" class="w-full px-3 py-2 bg-dark-925 border border-dark-800 rounded text-sm text-white placeholder-dark-500 focus:outline-none focus:border-mint-500" placeholder="Search..." value="<?php echo e($_GET['search'] ?? ''); ?>">
                </div>
                <div>
                    <label class="block text-xs font-medium text-dark-300 mb-1.5">Category</label>
                    <select name="category" class="w-full px-3 py-2 bg-dark-925 border border-dark-800 rounded text-sm text-white focus:outline-none focus:border-mint-500">
                        <option value="">All</option>
                        <?php foreach (COURSE_CATEGORIES as $cat): ?>
                        <option value="<?php echo e($cat); ?>" <?php echo ($_GET['category'] ?? '') === $cat ? 'selected' : ''; ?>><?php echo e($cat); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-dark-300 mb-1.5">Status</label>
                    <select name="status" class="w-full px-3 py-2 bg-dark-925 border border-dark-800 rounded text-sm text-white focus:outline-none focus:border-mint-500">
                        <option value="">All</option>
                        <?php foreach (COURSE_STATUSES as $key => $label): ?>
                        <option value="<?php echo e($key); ?>" <?php echo ($_GET['status'] ?? '') === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-dark-300 mb-1.5">Instructor</label>
                    <input type="text" name="instructor" class="w-full px-3 py-2 bg-dark-925 border border-dark-800 rounded text-sm text-white placeholder-dark-500 focus:outline-none focus:border-mint-500" placeholder="Name..." value="<?php echo e($_GET['instructor'] ?? ''); ?>">
                </div>
                <div>
                    <label class="block text-xs font-medium text-dark-300 mb-1.5">Date From</label>
                    <input type="date" name="date_from" class="w-full px-3 py-2 bg-dark-925 border border-dark-800 rounded text-sm text-white focus:outline-none focus:border-mint-500" value="<?php echo e($_GET['date_from'] ?? ''); ?>">
                </div>
                <div>
                    <label class="block text-xs font-medium text-dark-300 mb-1.5">Date To</label>
                    <input type="date" name="date_to" class="w-full px-3 py-2 bg-dark-925 border border-dark-800 rounded text-sm text-white focus:outline-none focus:border-mint-500" value="<?php echo e($_GET['date_to'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="flex gap-3 pt-4 border-t border-dark-900">
                <button type="submit" name="format" value="csv" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded">
                    <i class="fas fa-file-csv"></i> Export CSV
                </button>
                <button type="submit" name="format" value="pdf" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-500 text-white text-sm font-medium rounded">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
            </div>
        </form>
    </div>
    
    <!-- Info Cards -->
    <div class="grid grid-cols-2 gap-4 mt-4">
        <div class="bg-dark-950 border border-dark-900 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                    <i class="fas fa-file-csv text-emerald-400"></i>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-white">CSV Format</h3>
                    <p class="text-xs text-dark-400">For Excel and analysis</p>
                </div>
            </div>
        </div>
        <div class="bg-dark-950 border border-dark-900 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center">
                    <i class="fas fa-file-pdf text-red-400"></i>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-white">PDF Format</h3>
                    <p class="text-xs text-dark-400">Printable report</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
