<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Link Equipment to Course - Modern Tailwind CSS
 * ============================================
 */

$pageTitle = 'Link Equipment';
$currentPage = 'course-equipment';

require_once __DIR__ . '/../../includes/course_equipment/functions.php';
require_once __DIR__ . '/../../includes/courses/functions.php';
require_once __DIR__ . '/../../includes/equipment/functions.php';

// Get pre-selected IDs if provided
$selectedCourseId = isset($_GET['course_id']) ? (int) $_GET['course_id'] : null;
$selectedEquipmentId = isset($_GET['equipment_id']) ? (int) $_GET['equipment_id'] : null;

// Get available courses and equipment
$courses = getCoursesForDropdown();
$equipment = getEquipmentForDropdownList();

$errors = [];
$formData = [
    'course_id' => $selectedCourseId,
    'equipment_id' => $selectedEquipmentId,
    'quantity_needed' => 1
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid request.');
        redirect(url('/course-equipment/link.php'));
    }
    
    $formData = [
        'course_id' => (int) ($_POST['course_id'] ?? 0),
        'equipment_id' => (int) ($_POST['equipment_id'] ?? 0),
        'quantity_needed' => (int) ($_POST['quantity_needed'] ?? 1)
    ];
    
    $errors = validateAssignment($formData);
    
    if (empty($errors)) {
        $result = linkEquipmentToCourse($formData['course_id'], $formData['equipment_id'], $formData['quantity_needed']);
        if ($result) {
            setFlashMessage('success', 'Equipment linked successfully.');
            if ($selectedCourseId) {
                redirect(url('/courses/view.php?id=' . $formData['course_id']));
            } elseif ($selectedEquipmentId) {
                redirect(url('/equipment/view.php?id=' . $formData['equipment_id']));
            } else {
                redirect(url('/course-equipment/'));
            }
        } else {
            setFlashMessage('error', 'Failed to link equipment or already linked.');
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- PAGE HEADER -->
<div class="mb-8">
    <nav class="flex items-center gap-2 text-sm text-dark-400 mb-3">
        <a href="<?php echo url('/course-equipment/'); ?>" class="hover:text-cyan-400 transition-colors">Assignments</a>
        <i class="fas fa-chevron-right text-xs text-dark-600"></i>
        <span class="text-dark-300">Link Equipment</span>
    </nav>
    <h1 class="text-lg font-semibold tracking-tight text-white">Link Equipment to Course</h1>
</div>

<!-- LINK FORM -->
<div class="max-w-2xl mx-auto">
    <div class="bg-dark-950 backdrop-blur-sm rounded-lg border border-dark-900 overflow-hidden">
        <div class="flex items-center gap-2 p-5 border-b border-dark-900">
            <i class="fas fa-link text-cyan-400"></i>
            <h2 class="text-lg font-semibold text-white">Assignment Details</h2>
        </div>
        <div class="p-6">
            <form action="" method="POST" novalidate>
                <?php echo csrfField(); ?>
                
                <div class="space-y-6">
                    <!-- Course Selection -->
                    <div>
                        <label for="course_id" class="block text-sm font-medium text-dark-300 mb-2">
                            Course <span class="text-rose-400">*</span>
                        </label>
                        <select id="course_id" name="course_id" class="w-full px-4 py-3 bg-dark-975/50 border <?php echo isset($errors['course_id']) ? 'border-rose-500' : 'border-dark-800'; ?> rounded-lg text-white focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-all" <?php echo $selectedCourseId ? 'disabled' : ''; ?> required>
                            <option value="">Select Course</option>
                            <?php foreach ($courses as $course): ?>
                            <option value="<?php echo $course['id']; ?>" <?php echo $formData['course_id'] == $course['id'] ? 'selected' : ''; ?>>
                                <?php echo e($course['name']); ?> (<?php echo formatDate($course['course_date']); ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($selectedCourseId): ?>
                        <input type="hidden" name="course_id" value="<?php echo $selectedCourseId; ?>">
                        <?php endif; ?>
                        <?php if (isset($errors['course_id'])): ?>
                        <p class="mt-2 text-sm text-rose-400"><?php echo e($errors['course_id']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Equipment Selection -->
                    <div>
                        <label for="equipment_id" class="block text-sm font-medium text-dark-300 mb-2">
                            Equipment <span class="text-rose-400">*</span>
                        </label>
                        <select id="equipment_id" name="equipment_id" class="w-full px-4 py-3 bg-dark-975/50 border <?php echo isset($errors['equipment_id']) ? 'border-rose-500' : 'border-dark-800'; ?> rounded-lg text-white focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-all" <?php echo $selectedEquipmentId ? 'disabled' : ''; ?> required>
                            <option value="">Select Equipment</option>
                            <?php foreach ($equipment as $eq): ?>
                            <option value="<?php echo $eq['id']; ?>" <?php echo $formData['equipment_id'] == $eq['id'] ? 'selected' : ''; ?>>
                                <?php echo e($eq['name']); ?> (<?php echo e($eq['type']); ?> - <?php echo $eq['available_quantity']; ?> available)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($selectedEquipmentId): ?>
                        <input type="hidden" name="equipment_id" value="<?php echo $selectedEquipmentId; ?>">
                        <?php endif; ?>
                        <?php if (isset($errors['equipment_id'])): ?>
                        <p class="mt-2 text-sm text-rose-400"><?php echo e($errors['equipment_id']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Quantity -->
                    <div>
                        <label for="quantity_needed" class="block text-sm font-medium text-dark-300 mb-2">
                            Quantity Needed <span class="text-rose-400">*</span>
                        </label>
                        <input type="number" id="quantity_needed" name="quantity_needed" class="w-full px-4 py-3 bg-dark-975/50 border <?php echo isset($errors['quantity_needed']) ? 'border-rose-500' : 'border-dark-800'; ?> rounded-lg text-white focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-all" value="<?php echo e($formData['quantity_needed']); ?>" min="1" required>
                        <?php if (isset($errors['quantity_needed'])): ?>
                        <p class="mt-2 text-sm text-rose-400"><?php echo e($errors['quantity_needed']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-dark-900">
                    <a href="<?php echo url('/course-equipment/'); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-dark-800 hover:bg-dark-600 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-medium rounded-lg shadow-lg shadow-cyan-500/25 transition-all duration-200">
                        <i class="fas fa-link"></i> Link Equipment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
