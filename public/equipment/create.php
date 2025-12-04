<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Create Equipment Page - Ultra-Sharp Design
 * ============================================
 */

$pageTitle = 'Add Equipment';
$currentPage = 'equipment';

require_once __DIR__ . '/../../includes/equipment/functions.php';

$errors = [];
$formData = [
    'name' => '',
    'type' => '',
    'description' => '',
    'quantity' => 1,
    'equipment_condition' => 'excellent'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid request.');
        redirect(url('/equipment/create.php'));
    }
    
    $formData = [
        'name' => sanitize($_POST['name'] ?? ''),
        'type' => sanitize($_POST['type'] ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
        'quantity' => (int) ($_POST['quantity'] ?? 1),
        'equipment_condition' => sanitize($_POST['equipment_condition'] ?? 'excellent')
    ];
    
    $errors = validateEquipmentData($formData);
    
    if (empty($errors)) {
        $equipmentId = createEquipment($formData);
        
        if ($equipmentId) {
            setFlashMessage('success', 'Equipment created successfully.');
            redirect(url('/equipment/view.php?id=' . $equipmentId));
        } else {
            setFlashMessage('error', 'Failed to create equipment.');
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- BREADCRUMB -->
<div class="flex items-center gap-2 text-xs text-dark-400 mb-6">
    <a href="<?php echo url('/equipment/'); ?>" class="hover:text-dark-200">Equipment</a>
    <span>/</span>
    <span class="text-dark-200">New Equipment</span>
</div>

<!-- FORM CARD -->
<div class="max-w-2xl mx-auto">
    <div class="bg-dark-950 border border-dark-900 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-dark-900">
            <h1 class="text-base font-semibold text-white">Add New Equipment</h1>
            <p class="text-xs text-dark-400 mt-0.5">Fill in the equipment details below</p>
        </div>
        
        <form action="" method="POST" class="p-6">
            <?php echo csrfField(); ?>
            
            <div class="space-y-5">
                <!-- Equipment Name -->
                <div>
                    <label for="name" class="block text-xs font-medium text-dark-300 mb-1.5">
                        Equipment Name <span class="text-red-400">*</span>
                    </label>
                    <input type="text" id="name" name="name" 
                           class="w-full px-3 py-2 bg-dark-925 border <?php echo isset($errors['name']) ? 'border-red-500' : 'border-dark-800'; ?> rounded text-sm text-white placeholder-dark-500 focus:outline-none focus:border-mint-500"
                           value="<?php echo e($formData['name']); ?>"
                           placeholder="e.g., Olympic Barbell">
                    <?php if (isset($errors['name'])): ?>
                    <p class="mt-1 text-xs text-red-400"><?php echo e($errors['name']); ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Type & Condition -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-xs font-medium text-dark-300 mb-1.5">
                            Type <span class="text-red-400">*</span>
                        </label>
                        <select id="type" name="type" 
                                class="w-full px-3 py-2 bg-dark-925 border <?php echo isset($errors['type']) ? 'border-red-500' : 'border-dark-800'; ?> rounded text-sm text-white focus:outline-none focus:border-mint-500">
                            <option value="">Select Type</option>
                            <?php foreach (EQUIPMENT_TYPES as $type): ?>
                            <option value="<?php echo e($type); ?>" <?php echo $formData['type'] === $type ? 'selected' : ''; ?>><?php echo e($type); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['type'])): ?>
                        <p class="mt-1 text-xs text-red-400"><?php echo e($errors['type']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="equipment_condition" class="block text-xs font-medium text-dark-300 mb-1.5">Condition</label>
                        <select id="equipment_condition" name="equipment_condition" 
                                class="w-full px-3 py-2 bg-dark-925 border border-dark-800 rounded text-sm text-white focus:outline-none focus:border-mint-500">
                            <?php foreach (EQUIPMENT_CONDITIONS as $key => $label): ?>
                            <option value="<?php echo e($key); ?>" <?php echo $formData['equipment_condition'] === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Quantity -->
                <div class="w-1/2">
                    <label for="quantity" class="block text-xs font-medium text-dark-300 mb-1.5">
                        Quantity <span class="text-red-400">*</span>
                    </label>
                    <input type="number" id="quantity" name="quantity" 
                           class="w-full px-3 py-2 bg-dark-925 border <?php echo isset($errors['quantity']) ? 'border-red-500' : 'border-dark-800'; ?> rounded text-sm text-white focus:outline-none focus:border-mint-500"
                           value="<?php echo e($formData['quantity']); ?>" min="1" max="9999">
                    <?php if (isset($errors['quantity'])): ?>
                    <p class="mt-1 text-xs text-red-400"><?php echo e($errors['quantity']); ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Description -->
                <div>
                    <label for="description" class="block text-xs font-medium text-dark-300 mb-1.5">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 bg-dark-925 border border-dark-800 rounded text-sm text-white placeholder-dark-500 focus:outline-none focus:border-mint-500 resize-none"
                              placeholder="Optional description..."><?php echo e($formData['description']); ?></textarea>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-dark-900">
                <a href="<?php echo url('/equipment/'); ?>" class="px-4 py-2 text-sm text-dark-300 hover:text-white">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-mint-600 hover:bg-mint-500 text-white text-sm font-medium rounded">
                    Create Equipment
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>