<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Unlink Equipment from Course - Modern Tailwind CSS
 * ============================================
 */

require_once __DIR__ . '/../../includes/course_equipment/functions.php';

// Get IDs from query parameters
$courseId = isset($_GET['course_id']) ? (int) $_GET['course_id'] : 0;
$equipmentId = isset($_GET['equipment_id']) ? (int) $_GET['equipment_id'] : 0;

// Validate parameters
if (!$courseId || !$equipmentId) {
    setFlashMessage('error', 'Invalid request parameters.');
    redirect(url('/course-equipment/'));
}

// Check if assignment exists
if (!isEquipmentLinkedToCourse($courseId, $equipmentId)) {
    setFlashMessage('error', 'Assignment not found.');
    redirect(url('/course-equipment/'));
}

// Handle confirmation POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid request.');
        redirect(url('/course-equipment/'));
    }
    
    $result = unlinkEquipmentFromCourse($courseId, $equipmentId);
    
    if ($result) {
        setFlashMessage('success', 'Equipment unlinked from course successfully.');
    } else {
        setFlashMessage('error', 'Failed to unlink equipment.');
    }
    
    // Redirect back to referrer or assignments page
    $returnUrl = $_POST['return_url'] ?? '';
    if ($returnUrl && strpos($returnUrl, url('/')) === 0) {
        redirect($returnUrl);
    }
    redirect(url('/course-equipment/'));
}

// Get assignment details for confirmation
$assignment = getAssignmentDetails($courseId, $equipmentId);

$pageTitle = 'Unlink Equipment';
$currentPage = 'course-equipment';

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- PAGE HEADER -->
<div class="mb-8">
    <nav class="flex items-center gap-2 text-sm text-dark-400 mb-3">
        <a href="<?php echo url('/course-equipment/'); ?>" class="hover:text-cyan-400 transition-colors">Assignments</a>
        <i class="fas fa-chevron-right text-xs text-dark-600"></i>
        <span class="text-dark-300">Unlink Equipment</span>
    </nav>
    <h1 class="text-lg font-semibold tracking-tight text-white">Unlink Equipment</h1>
</div>

<!-- CONFIRMATION CARD -->
<div class="max-w-xl">
    <div class="bg-dark-950 backdrop-blur-sm rounded-lg border border-rose-500/30 overflow-hidden">
        <div class="flex items-center gap-3 p-5 border-b border-dark-900 bg-rose-500/5">
            <div class="w-10 h-10 rounded-lg bg-rose-500/20 flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-rose-400"></i>
            </div>
            <h2 class="text-lg font-semibold text-white">Confirm Unlink</h2>
        </div>
        <div class="p-6">
            <p class="text-dark-300 mb-6">Are you sure you want to remove this equipment assignment?</p>
            
            <div class="bg-dark-975/50 rounded-lg p-4 mb-6 space-y-4">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-accent-500/10 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-mint-400"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Course</p>
                        <p class="text-sm text-white"><?php echo e($assignment['course_name'] ?? 'Unknown Course'); ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                        <i class="fas fa-dumbbell text-emerald-400"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Equipment</p>
                        <p class="text-sm text-white"><?php echo e($assignment['equipment_name'] ?? 'Unknown Equipment'); ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-amber-500/10 flex items-center justify-center">
                        <i class="fas fa-hashtag text-amber-400"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Quantity</p>
                        <p class="text-sm text-white"><?php echo $assignment['quantity_needed'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            
            <form action="" method="POST">
                <?php echo csrfField(); ?>
                <input type="hidden" name="return_url" value="<?php echo e($_SERVER['HTTP_REFERER'] ?? ''); ?>">
                
                <div class="flex gap-4">
                    <a href="<?php echo url('/course-equipment/'); ?>" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-dark-800 hover:bg-dark-600 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-unlink"></i> Unlink
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
