<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Course-Equipment Assignment Index - Ultra-Sharp Design
 * ============================================
 */

$pageTitle = 'Equipment Assignments';
$currentPage = 'course-equipment';

require_once __DIR__ . '/../../includes/course_equipment/functions.php';

// Get all assignments with details
$result = getAllAssignments();
$assignments = $result['assignments'] ?? [];

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- PAGE HEADER -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-lg font-semibold tracking-tight text-white">Equipment Assignments</h1>
        <p class="text-dark-400 mt-1">Manage course-equipment relationships</p>
    </div>
    <a href="<?php echo url('/course-equipment/link.php'); ?>" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-medium rounded-lg shadow-lg shadow-cyan-500/25 hover:shadow-cyan-500/40 transition-all duration-200">
        <i class="fas fa-link"></i>
        New Assignment
    </a>
</div>

<!-- ASSIGNMENTS TABLE -->
<div class="bg-dark-950 backdrop-blur-sm rounded-lg border border-dark-900 overflow-hidden">
    <div class="flex items-center gap-2 p-5 border-b border-dark-900">
        <i class="fas fa-link text-cyan-400"></i>
        <h2 class="text-lg font-semibold text-white">Assignments</h2>
        <span class="px-2.5 py-0.5 text-xs font-medium bg-cyan-500/20 text-cyan-300 rounded-lg"><?php echo count($assignments); ?></span>
    </div>
    
    <div class="p-5">
        <?php if (!empty($assignments)): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-xs font-semibold text-dark-400 uppercase tracking-wider border-b border-dark-900">
                        <th class="text-left py-4 px-4">Course</th>
                        <th class="text-left py-4 px-4">Equipment</th>
                        <th class="text-left py-4 px-4">Quantity</th>
                        <th class="text-left py-4 px-4">Assigned Date</th>
                        <th class="text-center py-4 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-900">
                    <?php foreach ($assignments as $assignment): ?>
                    <tr class="hover:bg-dark-925">
                        <td class="py-3 px-4">
                            <div>
                                <a href="<?php echo url('/courses/view.php?id=' . ($assignment['course_id'] ?? '')); ?>" class="text-sm text-dark-100 hover:text-mint-400">
                                    <?php echo e($assignment['course_name'] ?? 'Unknown Course'); ?>
                                </a>
                                <p class="text-xs text-dark-500 mt-0.5">
                                    <?php echo formatDate($assignment['course_date'] ?? null); ?>
                                </p>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div>
                                <a href="<?php echo url('/equipment/view.php?id=' . ($assignment['equipment_id'] ?? '')); ?>" class="text-sm text-dark-100 hover:text-mint-400">
                                    <?php echo e($assignment['equipment_name'] ?? 'Unknown Equipment'); ?>
                                </a>
                                <p class="text-xs text-dark-500 mt-0.5">
                                    <?php echo e($assignment['equipment_type'] ?? ''); ?>
                                </p>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-sm text-dark-200"><?php echo ($assignment['quantity_needed'] ?? 0); ?></span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-xs text-dark-400"><?php echo formatDate($assignment['assigned_at'] ?? null, 'M d, Y'); ?></span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="<?php echo url('/course-equipment/unlink.php?course_id=' . ($assignment['course_id'] ?? '') . '&equipment_id=' . ($assignment['equipment_id'] ?? '')); ?>" class="px-2 py-1 text-xs text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded" data-confirm="Remove this assignment?">
                                <i class="fas fa-unlink"></i> Remove
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="w-20 h-20 rounded-lg bg-dark-800/50 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-link-slash text-4xl text-dark-500"></i>
            </div>
            <h3 class="text-xl font-semibold text-white mb-2">No Assignments Found</h3>
            <p class="text-dark-400 mb-6 max-w-md mx-auto">
                There are no equipment assignments yet. Link equipment to courses to get started.
            </p>
            <a href="<?php echo url('/course-equipment/link.php'); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-medium rounded-lg transition-all duration-200">
                <i class="fas fa-link"></i> Create First Assignment
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
