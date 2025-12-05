<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Export Equipment Page - Ultra-Sharp Design
 * ============================================
 */

$pageTitle = 'Export Equipment';
$currentPage = 'export-equipment';

require_once __DIR__ . '/../../includes/equipment/functions.php';
require_once __DIR__ . '/../../includes/export/functions.php';

// Handle export request
if (isset($_GET['format'])) {
    $format = sanitize($_GET['format']);
    
    $filters = [
        'search' => sanitize($_GET['search'] ?? ''),
        'type' => sanitize($_GET['type'] ?? ''),
        'condition' => sanitize($_GET['condition'] ?? '')
    ];
    
    if ($format === 'csv') {
        exportEquipmentToCSV($filters);
    } elseif ($format === 'pdf') {
        exportEquipmentToPDF($filters);
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- PAGE HEADER -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-lg font-semibold text-dark-50 tracking-tight">Export Equipment</h1>
        <p class="text-xs text-dark-400 mt-0.5">Download equipment data in CSV or PDF format</p>
    </div>
    <a href="<?php echo url('/equipment/'); ?>" class="px-3 py-1.5 text-xs text-dark-300 hover:text-white bg-dark-900 rounded">
        ← Back to Equipment
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
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-dark-300 mb-1.5">Search</label>
                    <input type="text" name="search" class="w-full px-3 py-2 bg-dark-925 border border-dark-800 rounded text-sm text-white placeholder-dark-500 focus:outline-none focus:border-mint-500" placeholder="Search equipment..." value="<?php echo e($_GET['search'] ?? ''); ?>">
                </div>
                <div>
                    <label class="block text-xs font-medium text-dark-300 mb-1.5">Type</label>
                    <select name="type" class="w-full px-3 py-2 bg-dark-925 border border-dark-800 rounded text-sm text-white focus:outline-none focus:border-mint-500">
                        <option value="">All Types</option>
                        <?php foreach (EQUIPMENT_TYPES as $type): ?>
                        <option value="<?php echo e($type); ?>" <?php echo ($_GET['type'] ?? '') === $type ? 'selected' : ''; ?>><?php echo e($type); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-dark-300 mb-1.5">Condition</label>
                    <select name="condition" class="w-full px-3 py-2 bg-dark-925 border border-dark-800 rounded text-sm text-white focus:outline-none focus:border-mint-500">
                        <option value="">All Conditions</option>
                        <?php foreach (EQUIPMENT_CONDITIONS as $key => $label): ?>
                        <option value="<?php echo e($key); ?>" <?php echo ($_GET['condition'] ?? '') === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
                        <?php endforeach; ?>
                    </select>
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
