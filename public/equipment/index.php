<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Equipment List Page - Ultra-Sharp Design
 * ============================================
 */

$pageTitle = 'Equipment';
$currentPage = 'equipment';

require_once __DIR__ . '/../../includes/equipment/functions.php';

// Get filter parameters
$filters = [
    'search' => sanitize($_GET['search'] ?? ''),
    'type' => sanitize($_GET['type'] ?? ''),
    'condition' => sanitize($_GET['condition'] ?? ''),
    'sort' => sanitize($_GET['sort'] ?? 'name'),
    'order' => sanitize($_GET['order'] ?? 'ASC')
];

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

$result = getAllEquipment($filters, $page);
$equipment = $result['equipment'];
$pagination = $result['pagination'];

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- PAGE HEADER -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-lg font-semibold text-dark-50 tracking-tight">Equipment Management</h1>
        <p class="text-xs text-dark-400 mt-0.5">Manage all gym equipment</p>
    </div>
    <a href="<?php echo url('/equipment/create.php'); ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-mint-600 hover:bg-mint-700 text-white text-sm font-medium rounded">
        <i class="fas fa-plus text-xs"></i>
        Add Equipment
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
                    <label class="block text-xs text-dark-400 mb-1">Type</label>
                    <select name="type" class="w-full px-3 py-1.5 bg-dark-925 border border-dark-800 rounded text-sm text-dark-100">
                        <option value="">All</option>
                        <?php foreach (EQUIPMENT_TYPES as $type): ?>
                        <option value="<?php echo e($type); ?>" <?php echo $filters['type'] === $type ? 'selected' : ''; ?>><?php echo e($type); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-dark-400 mb-1">Condition</label>
                    <select name="condition" class="w-full px-3 py-1.5 bg-dark-925 border border-dark-800 rounded text-sm text-dark-100">
                        <option value="">All</option>
                        <?php foreach (EQUIPMENT_CONDITIONS as $key => $label): ?>
                        <option value="<?php echo e($key); ?>" <?php echo $filters['condition'] === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-3 py-1.5 bg-dark-800 hover:bg-dark-700 text-dark-100 text-xs font-medium rounded">Apply</button>
                    <a href="<?php echo url('/equipment/'); ?>" class="ml-2 px-3 py-1.5 text-dark-400 hover:text-dark-200 text-xs">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- TABLE -->
<div class="bg-dark-950 border border-dark-900 rounded-lg overflow-hidden">
    <div class="px-4 py-3 border-b border-dark-900 flex items-center justify-between">
        <span class="text-xs text-dark-400"><?php echo $pagination['total_items']; ?> items</span>
        <select id="sortSelect" onchange="applySorting()" class="text-xs bg-dark-925 border border-dark-800 rounded px-2 py-1 text-dark-300">
            <option value="name-ASC" <?php echo ($filters['sort'] === 'name' && $filters['order'] === 'ASC') ? 'selected' : ''; ?>>Name A-Z</option>
            <option value="name-DESC" <?php echo ($filters['sort'] === 'name' && $filters['order'] === 'DESC') ? 'selected' : ''; ?>>Name Z-A</option>
            <option value="quantity-DESC" <?php echo ($filters['sort'] === 'quantity' && $filters['order'] === 'DESC') ? 'selected' : ''; ?>>Qty High</option>
            <option value="quantity-ASC" <?php echo ($filters['sort'] === 'quantity' && $filters['order'] === 'ASC') ? 'selected' : ''; ?>>Qty Low</option>
        </select>
    </div>
    
    <?php if (!empty($equipment)): ?>
    <table class="w-full">
        <thead>
            <tr class="text-[10px] font-medium text-dark-500 uppercase tracking-wider border-b border-dark-900">
                <th class="text-left px-4 py-2">Equipment</th>
                <th class="text-left px-4 py-2">Type</th>
                <th class="text-left px-4 py-2">Quantity</th>
                <th class="text-left px-4 py-2">Condition</th>
                <th class="text-center px-4 py-2 w-20">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-dark-900">
            <?php foreach ($equipment as $item): ?>
            <tr class="hover:bg-dark-925">
                <td class="px-4 py-3">
                    <a href="<?php echo url('/equipment/view.php?id=' . $item['id']); ?>" class="text-sm text-dark-100 hover:text-mint-400"><?php echo e($item['name']); ?></a>
                </td>
                <td class="px-4 py-3">
                    <span class="text-xs text-dark-300"><?php echo e($item['type']); ?></span>
                </td>
                <td class="px-4 py-3">
                    <span class="text-sm font-medium text-dark-200"><?php echo $item['quantity']; ?></span>
                </td>
                <td class="px-4 py-3">
                    <?php 
                    $condColor = match($item['equipment_condition'] ?? '') {
                        'excellent' => 'text-mint-500',
                        'good' => 'text-dark-300',
                        'fair' => 'text-amber-500',
                        'needs_repair' => 'text-red-500',
                        default => 'text-dark-400'
                    };
                    ?>
                    <span class="text-xs <?php echo $condColor; ?>"><?php echo e(EQUIPMENT_CONDITIONS[$item['equipment_condition']] ?? 'Unknown'); ?></span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-1">
                        <a href="<?php echo url('/equipment/view.php?id=' . $item['id']); ?>" class="p-1.5 text-dark-400 hover:text-dark-100" title="View"><i class="fas fa-eye text-xs"></i></a>
                        <a href="<?php echo url('/equipment/edit.php?id=' . $item['id']); ?>" class="p-1.5 text-dark-400 hover:text-dark-100" title="Edit"><i class="fas fa-pen text-xs"></i></a>
                        <a href="<?php echo url('/equipment/delete.php?id=' . $item['id']); ?>" class="p-1.5 text-dark-400 hover:text-red-400" title="Delete" data-confirm="Delete this equipment?"><i class="fas fa-trash text-xs"></i></a>
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
            <a href="<?php echo url('/equipment/', array_merge($filters, ['page' => $pagination['prev_page']])); ?>" class="px-2 py-1 text-xs text-dark-400 hover:text-dark-100">← Prev</a>
            <?php endif; ?>
            <?php if ($pagination['has_next']): ?>
            <a href="<?php echo url('/equipment/', array_merge($filters, ['page' => $pagination['next_page']])); ?>" class="px-2 py-1 text-xs text-dark-400 hover:text-dark-100">Next →</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="px-4 py-12 text-center">
        <i class="fas fa-dumbbell text-dark-700 text-2xl mb-2"></i>
        <p class="text-sm text-dark-400">No equipment found</p>
        <a href="<?php echo url('/equipment/create.php'); ?>" class="inline-block mt-3 text-xs text-mint-500 hover:text-mint-400">+ Add first equipment</a>
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

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>