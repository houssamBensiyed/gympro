<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * View Equipment Page - Modern Tailwind CSS
 * ============================================
 */

require_once __DIR__ . '/../../includes/equipment/functions.php';
require_once __DIR__ . '/../../includes/course_equipment/functions.php';

$equipmentId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$equipment = getEquipmentById($equipmentId);

if (!$equipment) {
    setFlashMessage('error', 'Equipment not found.');
    redirect(url('/equipment/'));
}

$linkedCourses = getEquipmentCourses($equipmentId);

$pageTitle = $equipment['name'];
$currentPage = 'equipment';

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- PAGE HEADER -->
<div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6 mb-8">
    <div>
        <nav class="flex items-center gap-2 text-sm text-dark-400 mb-3">
            <a href="<?php echo url('/equipment/'); ?>" class="hover:text-emerald-400 transition-colors">Equipment</a>
            <i class="fas fa-chevron-right text-xs text-dark-600"></i>
            <span class="text-dark-300"><?php echo e($equipment['name']); ?></span>
        </nav>
        <h1 class="text-lg font-semibold tracking-tight text-white mb-3"><?php echo e($equipment['name']); ?></h1>
        <div class="flex flex-wrap items-center gap-2">
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
            <?php if ($equipment['is_active']): ?>
            <span class="px-3 py-1 text-xs font-medium rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                <i class="fas fa-check mr-1"></i>Active
            </span>
            <?php else: ?>
            <span class="px-3 py-1 text-xs font-medium rounded-lg bg-surface-500/10 text-dark-400 border border-surface-500/30">
                <i class="fas fa-times mr-1"></i>Inactive
            </span>
            <?php endif; ?>
        </div>
    </div>
    <div class="flex gap-3">
        <a href="<?php echo url('/equipment/edit.php?id=' . $equipment['id']); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-medium rounded-lg transition-colors">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="<?php echo url('/equipment/delete.php?id=' . $equipment['id']); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-medium rounded-lg transition-colors" data-confirm="Are you sure you want to delete this equipment?">
            <i class="fas fa-trash"></i> Delete
        </a>
    </div>
</div>

<!-- EQUIPMENT DETAILS -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Equipment Information Card -->
        <div class="bg-dark-950 backdrop-blur-sm rounded-lg border border-dark-900 overflow-hidden">
            <div class="flex items-center gap-2 p-5 border-b border-dark-900">
                <i class="fas fa-info-circle text-emerald-400"></i>
                <h2 class="text-lg font-semibold text-white">Equipment Details</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-lg bg-accent-500/10 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-tag text-mint-400"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Type</p>
                            <p class="text-sm text-white"><?php echo e($equipment['type']); ?></p>
                        </div>
                    </div>
                    
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-boxes text-emerald-400"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Quantity</p>
                            <p class="text-sm text-white"><?php echo $equipment['quantity']; ?> (<?php echo $equipment['available_quantity']; ?> available)</p>
                        </div>
                    </div>
                    
                    <?php if ($equipment['brand']): ?>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-lg bg-amber-500/10 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-building text-amber-400"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Brand</p>
                            <p class="text-sm text-white"><?php echo e($equipment['brand']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($equipment['model']): ?>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-lg bg-cyan-500/10 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-cube text-cyan-400"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Model</p>
                            <p class="text-sm text-white"><?php echo e($equipment['model']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-lg bg-rose-500/10 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-rose-400"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Location</p>
                            <p class="text-sm text-white"><?php echo e($equipment['location'] ?: 'Not specified'); ?></p>
                        </div>
                    </div>
                    
                    <?php if ($equipment['purchase_date']): ?>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-lg bg-indigo-500/10 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calendar-check text-indigo-400"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Purchase Date</p>
                            <p class="text-sm text-white"><?php echo formatDate($equipment['purchase_date']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($equipment['notes'])): ?>
                <div class="mt-6 pt-6 border-t border-dark-900">
                    <h3 class="text-sm font-medium text-dark-400 mb-3">Notes</h3>
                    <p class="text-sm text-dark-300 leading-relaxed"><?php echo nl2br(e($equipment['notes'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Linked Courses Card -->
        <div class="bg-dark-950 backdrop-blur-sm rounded-lg border border-dark-900 overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b border-dark-900">
                <h2 class="flex items-center gap-2 text-lg font-semibold text-white">
                    <i class="fas fa-calendar-alt text-mint-400"></i>
                    Linked Courses
                    <span class="px-2 py-0.5 text-xs font-medium bg-accent-500/20 text-accent-300 rounded-lg"><?php echo count($linkedCourses); ?></span>
                </h2>
                <a href="<?php echo url('/course-equipment/link.php?equipment_id=' . $equipment['id']); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-mint-600 hover:bg-mint-500 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-plus"></i> Link to Course
                </a>
            </div>
            <div class="p-5">
                <?php if (!empty($linkedCourses)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-xs font-semibold text-dark-400 uppercase tracking-wider border-b border-dark-900">
                                <th class="text-left py-3 px-4">Course</th>
                                <th class="text-left py-3 px-4">Date</th>
                                <th class="text-left py-3 px-4">Status</th>
                                <th class="text-left py-3 px-4">Qty Needed</th>
                                <th class="text-center py-3 px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-dark-700/50">
                            <?php foreach ($linkedCourses as $course): ?>
                            <tr class="hover:bg-dark-800/30 transition-colors">
                                <td class="py-3 px-4">
                                    <a href="<?php echo url('/courses/view.php?id=' . $course['id']); ?>" class="font-medium text-white hover:text-mint-400 transition-colors">
                                        <?php echo e($course['name']); ?>
                                    </a>
                                </td>
                                <td class="py-3 px-4 text-sm text-dark-400"><?php echo formatDate($course['course_date']); ?></td>
                                <td class="py-3 px-4">
                                    <?php 
                                    $statusColors = [
                                        'scheduled' => 'bg-accent-500/10 text-mint-400 border-accent-500/30',
                                        'in_progress' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                        'completed' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                        'cancelled' => 'bg-rose-500/10 text-rose-400 border-rose-500/30'
                                    ];
                                    $statusClass = $statusColors[$course['status']] ?? 'bg-surface-500/10 text-dark-400 border-surface-500/30';
                                    ?>
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-lg border <?php echo $statusClass; ?>">
                                        <?php echo e(COURSE_STATUSES[$course['status']] ?? ucfirst($course['status'])); ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-semibold text-white"><?php echo $course['quantity_needed']; ?></td>
                                <td class="py-3 px-4 text-center">
                                    <a href="<?php echo url('/course-equipment/unlink.php?course_id=' . $course['id'] . '&equipment_id=' . $equipment['id']); ?>" class="p-2 text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors inline-block" title="Unlink" data-confirm="Are you sure you want to unlink this course?">
                                        <i class="fas fa-unlink"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-12">
                    <div class="w-16 h-16 rounded-lg bg-dark-800/50 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-times text-2xl text-dark-500"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">No Linked Courses</h3>
                    <p class="text-dark-400 mb-4">This equipment is not linked to any courses yet.</p>
                    <a href="<?php echo url('/course-equipment/link.php?equipment_id=' . $equipment['id']); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-mint-600 hover:bg-mint-500 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-plus"></i> Link to Course
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Quick Actions -->
        <div class="bg-dark-950 backdrop-blur-sm rounded-lg border border-dark-900 overflow-hidden">
            <div class="flex items-center gap-2 p-5 border-b border-dark-900">
                <i class="fas fa-bolt text-amber-400"></i>
                <h2 class="text-lg font-semibold text-white">Quick Actions</h2>
            </div>
            <div class="p-4 space-y-2">
                <a href="<?php echo url('/equipment/edit.php?id=' . $equipment['id']); ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg text-dark-400 hover:text-white hover:bg-dark-800/50 transition-all">
                    <i class="fas fa-edit w-5 text-center"></i><span>Edit Equipment</span>
                </a>
                <a href="<?php echo url('/course-equipment/link.php?equipment_id=' . $equipment['id']); ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg text-dark-400 hover:text-white hover:bg-dark-800/50 transition-all">
                    <i class="fas fa-link w-5 text-center"></i><span>Link to Course</span>
                </a>
                <a href="<?php echo url('/equipment/delete.php?id=' . $equipment['id']); ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg text-dark-400 hover:text-rose-400 hover:bg-rose-500/10 transition-all" data-confirm="Are you sure?">
                    <i class="fas fa-trash w-5 text-center"></i><span>Delete Equipment</span>
                </a>
            </div>
        </div>
        
        <!-- Metadata -->
        <div class="bg-dark-950 backdrop-blur-sm rounded-lg border border-dark-900 overflow-hidden">
            <div class="flex items-center gap-2 p-5 border-b border-dark-900">
                <i class="fas fa-database text-cyan-400"></i>
                <h2 class="text-lg font-semibold text-white">Metadata</h2>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Equipment ID</p>
                    <p class="text-sm text-white">#<?php echo $equipment['id']; ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Created</p>
                    <p class="text-sm text-white"><?php echo formatDate($equipment['created_at'], 'M d, Y g:i A'); ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Last Updated</p>
                    <p class="text-sm text-white"><?php echo formatDate($equipment['updated_at'], 'M d, Y g:i A'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>