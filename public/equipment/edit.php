<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Edit Equipment Page - Modern Tailwind CSS
 * ============================================
 */

require_once __DIR__ . '/../../includes/equipment/functions.php';

$equipmentId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$equipment = getEquipmentById($equipmentId);

if (!$equipment) {
    setFlashMessage('error', 'Equipment not found.');
    redirect(url('/equipment/'));
}

$pageTitle = 'Edit Equipment';
$currentPage = 'equipment';

$errors = [];
$formData = [
    'name' => $equipment['name'],
    'type' => $equipment['type'],
    'brand' => $equipment['brand'] ?? '',
    'model' => $equipment['model'] ?? '',
    'quantity' => $equipment['quantity'],
    'equipment_condition' => $equipment['equipment_condition'],
    'location' => $equipment['location'] ?? '',
    'purchase_date' => $equipment['purchase_date'] ?? '',
    'notes' => $equipment['notes'] ?? '',
    'is_active' => $equipment['is_active']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid request.');
        redirect(url('/equipment/edit.php?id=' . $equipmentId));
    }
    
    $formData = [
        'name' => sanitize($_POST['name'] ?? ''),
        'type' => sanitize($_POST['type'] ?? ''),
        'brand' => sanitize($_POST['brand'] ?? ''),
        'model' => sanitize($_POST['model'] ?? ''),
        'quantity' => (int) ($_POST['quantity'] ?? 1),
        'equipment_condition' => sanitize($_POST['equipment_condition'] ?? 'new'),
        'location' => sanitize($_POST['location'] ?? ''),
        'purchase_date' => sanitize($_POST['purchase_date'] ?? ''),
        'notes' => sanitize($_POST['notes'] ?? ''),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    
    $errors = validateEquipmentData($formData, $equipmentId);
    
    if (empty($errors)) {
        $result = updateEquipment($equipmentId, $formData);
        if ($result !== false) {
            setFlashMessage('success', 'Equipment updated successfully.');
            redirect(url('/equipment/view.php?id=' . $equipmentId));
        } else {
            setFlashMessage('error', 'Failed to update equipment.');
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- PAGE HEADER -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <nav class="flex items-center gap-2 text-sm text-dark-400 mb-3">
            <a href="<?php echo url('/equipment/'); ?>" class="hover:text-emerald-400 transition-colors">Equipment</a>
            <i class="fas fa-chevron-right text-xs text-dark-600"></i>
            <a href="<?php echo url('/equipment/view.php?id=' . $equipmentId); ?>" class="hover:text-emerald-400 transition-colors"><?php echo e($equipment['name']); ?></a>
            <i class="fas fa-chevron-right text-xs text-dark-600"></i>
            <span class="text-dark-300">Edit</span>
        </nav>
        <h1 class="text-lg font-semibold tracking-tight text-white">Edit Equipment</h1>
    </div>
    <a href="<?php echo url('/equipment/view.php?id=' . $equipmentId); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-dark-800 hover:bg-dark-600 text-white font-medium rounded-lg transition-colors">
        <i class="fas fa-arrow-left"></i> Back to Equipment
    </a>
</div>

<!-- EQUIPMENT FORM -->
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-dark-950 backdrop-blur-sm rounded-lg border border-dark-900 overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-dark-900">
            <h2 class="flex items-center gap-2 text-lg font-semibold text-white">
                <i class="fas fa-edit text-amber-400"></i>
                Equipment Information
            </h2>
            <?php 
            $conditionColors = [
                'new' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                'good' => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30',
                'fair' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                'needs_repair' => 'bg-rose-500/10 text-rose-400 border-rose-500/30'
            ];
            $conditionClass = $conditionColors[$equipment['equipment_condition']] ?? 'bg-surface-500/10 text-dark-400 border-surface-500/30';
            ?>
            <span class="px-3 py-1 text-xs font-medium rounded-lg border <?php echo $conditionClass; ?>">
                <?php echo e(EQUIPMENT_CONDITIONS[$equipment['equipment_condition']] ?? ucfirst(str_replace('_', ' ', $equipment['equipment_condition']))); ?>
            </span>
        </div>
        <div class="p-6">
            <form action="" method="POST" novalidate>
                <?php echo csrfField(); ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-dark-300 mb-2">Equipment Name <span class="text-rose-400">*</span></label>
                        <input type="text" id="name" name="name" class="w-full px-4 py-3 bg-dark-975/50 border <?php echo isset($errors['name']) ? 'border-rose-500' : 'border-dark-800'; ?> rounded-lg text-white placeholder-dark-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all" value="<?php echo e($formData['name']); ?>" required>
                        <?php if (isset($errors['name'])): ?><p class="mt-2 text-sm text-rose-400"><?php echo e($errors['name']); ?></p><?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="type" class="block text-sm font-medium text-dark-300 mb-2">Type <span class="text-rose-400">*</span></label>
                        <select id="type" name="type" class="w-full px-4 py-3 bg-dark-975/50 border border-dark-800 rounded-lg text-white focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all" required>
                            <option value="">Select Type</option>
                            <?php foreach (EQUIPMENT_TYPES as $type): ?>
                            <option value="<?php echo e($type); ?>" <?php echo $formData['type'] === $type ? 'selected' : ''; ?>><?php echo e($type); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="equipment_condition" class="block text-sm font-medium text-dark-300 mb-2">Condition <span class="text-rose-400">*</span></label>
                        <select id="equipment_condition" name="equipment_condition" class="w-full px-4 py-3 bg-dark-975/50 border border-dark-800 rounded-lg text-white focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all" required>
                            <?php foreach (EQUIPMENT_CONDITIONS as $key => $label): ?>
                            <option value="<?php echo e($key); ?>" <?php echo $formData['equipment_condition'] === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="brand" class="block text-sm font-medium text-dark-300 mb-2">Brand</label>
                        <input type="text" id="brand" name="brand" class="w-full px-4 py-3 bg-dark-975/50 border border-dark-800 rounded-lg text-white placeholder-dark-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all" value="<?php echo e($formData['brand']); ?>">
                    </div>
                    
                    <div>
                        <label for="model" class="block text-sm font-medium text-dark-300 mb-2">Model</label>
                        <input type="text" id="model" name="model" class="w-full px-4 py-3 bg-dark-975/50 border border-dark-800 rounded-lg text-white placeholder-dark-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all" value="<?php echo e($formData['model']); ?>">
                    </div>
                    
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-dark-300 mb-2">Quantity <span class="text-rose-400">*</span></label>
                        <input type="number" id="quantity" name="quantity" class="w-full px-4 py-3 bg-dark-975/50 border border-dark-800 rounded-lg text-white focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all" value="<?php echo e($formData['quantity']); ?>" min="1" required>
                        <p class="mt-1.5 text-xs text-dark-500">Available: <?php echo $equipment['available_quantity']; ?></p>
                    </div>
                    
                    <div>
                        <label for="location" class="block text-sm font-medium text-dark-300 mb-2">Location</label>
                        <input type="text" id="location" name="location" class="w-full px-4 py-3 bg-dark-975/50 border border-dark-800 rounded-lg text-white placeholder-dark-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all" value="<?php echo e($formData['location']); ?>">
                    </div>
                    
                    <div>
                        <label for="purchase_date" class="block text-sm font-medium text-dark-300 mb-2">Purchase Date</label>
                        <input type="date" id="purchase_date" name="purchase_date" class="w-full px-4 py-3 bg-dark-975/50 border border-dark-800 rounded-lg text-white focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all" value="<?php echo e($formData['purchase_date']); ?>">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-dark-300 mb-2">Status</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" <?php echo $formData['is_active'] ? 'checked' : ''; ?>>
                            <div class="w-11 h-6 bg-dark-800 peer-focus:ring-2 peer-focus:ring-emerald-500/20 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            <span class="ml-3 text-sm text-dark-400">Active</span>
                        </label>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-dark-300 mb-2">Notes</label>
                        <textarea id="notes" name="notes" class="w-full px-4 py-3 bg-dark-975/50 border border-dark-800 rounded-lg text-white placeholder-dark-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all resize-none" rows="3"><?php echo e($formData['notes']); ?></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-dark-900">
                    <a href="<?php echo url('/equipment/view.php?id=' . $equipmentId); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-dark-800 hover:bg-dark-600 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-medium rounded-lg shadow-lg shadow-emerald-500/25 transition-all duration-200">
                        <i class="fas fa-save"></i> Update Equipment
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Metadata -->
    <div class="bg-dark-950 backdrop-blur-sm rounded-lg border border-dark-900 overflow-hidden">
        <div class="flex items-center gap-2 p-5 border-b border-dark-900">
            <i class="fas fa-info-circle text-cyan-400"></i>
            <h2 class="text-lg font-semibold text-white">Equipment Metadata</h2>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                <div>
                    <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Equipment ID</p>
                    <p class="text-sm text-white">#<?php echo $equipment['id']; ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Created</p>
                    <p class="text-sm text-white"><?php echo formatDate($equipment['created_at'], 'M d, Y'); ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Last Updated</p>
                    <p class="text-sm text-white"><?php echo formatDate($equipment['updated_at'], 'M d, Y'); ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Courses Linked</p>
                    <p class="text-sm text-white"><?php echo $equipment['courses_count']; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>