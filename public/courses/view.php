<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * View Course Page - Ultra-Sharp Design
 * ============================================
 */

require_once __DIR__ . '/../../includes/courses/functions.php';
require_once __DIR__ . '/../../includes/course_equipment/functions.php';

// Get course ID
$courseId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$course = getCourseById($courseId);

if (!$course) {
    setFlashMessage('error', 'Course not found.');
    redirect(url('/courses/'));
}

$assignedEquipment = getCourseEquipment($courseId);
$pageTitle = $course['name'];
$currentPage = 'courses';

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- BREADCRUMB & ACTIONS -->
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-2 text-xs text-dark-400">
        <a href="<?php echo url('/courses/'); ?>" class="hover:text-dark-200">Courses</a>
        <span>/</span>
        <span class="text-dark-200"><?php echo e($course['name']); ?></span>
    </div>
    <div class="flex items-center gap-2">
        <a href="<?php echo url('/courses/edit.php?id=' . $course['id']); ?>" class="px-2.5 py-1 text-xs text-dark-300 hover:text-dark-100 bg-dark-900 rounded">Edit</a>
        <a href="<?php echo url('/courses/delete.php?id=' . $course['id']); ?>" class="px-2.5 py-1 text-xs text-red-400 hover:text-red-300 bg-dark-900 rounded" data-confirm="Delete this course?">Delete</a>
    </div>
</div>

<!-- HEADER -->
<div class="mb-6">
    <div class="flex items-center gap-3 mb-2">
        <h1 class="text-xl font-semibold text-dark-50 tracking-tight"><?php echo e($course['name']); ?></h1>
        <?php 
        $statusColor = match($course['status']) {
            'scheduled' => 'text-mint-500 bg-mint-500/10',
            'in_progress' => 'text-amber-500 bg-amber-500/10',
            'completed' => 'text-dark-400 bg-dark-800',
            'cancelled' => 'text-red-500 bg-red-500/10',
            default => 'text-dark-400 bg-dark-800'
        };
        ?>
        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded <?php echo $statusColor; ?>">
            <?php echo e(COURSE_STATUSES[$course['status']] ?? $course['status']); ?>
        </span>
    </div>
    <p class="text-sm text-dark-400"><?php echo e($course['category']); ?> • <?php echo e($course['instructor_name']); ?></p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <!-- DETAILS -->
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-dark-950 border border-dark-900 rounded-lg p-4">
            <h2 class="text-xs font-medium text-dark-400 uppercase tracking-wide mb-4">Details</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-[10px] text-dark-500 uppercase tracking-wide">Date</span>
                    <p class="text-sm text-dark-200 mt-0.5"><?php echo formatDate($course['course_date'], 'l, M j, Y'); ?></p>
                </div>
                <div>
                    <span class="text-[10px] text-dark-500 uppercase tracking-wide">Time</span>
                    <p class="text-sm text-dark-200 mt-0.5"><?php echo formatTime($course['start_time']); ?></p>
                </div>
                <div>
                    <span class="text-[10px] text-dark-500 uppercase tracking-wide">Duration</span>
                    <p class="text-sm text-dark-200 mt-0.5"><?php echo formatDuration($course['duration_minutes']); ?></p>
                </div>
                <div>
                    <span class="text-[10px] text-dark-500 uppercase tracking-wide">Location</span>
                    <p class="text-sm text-dark-200 mt-0.5"><?php echo e($course['location'] ?: '—'); ?></p>
                </div>
                <div>
                    <span class="text-[10px] text-dark-500 uppercase tracking-wide">Participants</span>
                    <p class="text-sm text-dark-200 mt-0.5">
                        <?php echo $course['current_participants']; ?> / <?php echo $course['max_participants']; ?>
                        <?php $fill = ($course['current_participants'] / $course['max_participants']) * 100; ?>
                        <span class="inline-block w-16 h-1 bg-dark-800 rounded ml-2 align-middle">
                            <span class="block h-1 bg-mint-600 rounded" style="width: <?php echo $fill; ?>%"></span>
                        </span>
                    </p>
                </div>
            </div>
            <?php if (!empty($course['description'])): ?>
            <div class="mt-4 pt-4 border-t border-dark-900">
                <span class="text-[10px] text-dark-500 uppercase tracking-wide">Description</span>
                <p class="text-sm text-dark-300 mt-1"><?php echo nl2br(e($course['description'])); ?></p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- EQUIPMENT -->
        <div class="bg-dark-950 border border-dark-900 rounded-lg">
            <div class="px-4 py-3 border-b border-dark-900 flex items-center justify-between">
                <h2 class="text-xs font-medium text-dark-400 uppercase tracking-wide">Equipment (<?php echo count($assignedEquipment); ?>)</h2>
                <a href="<?php echo url('/course-equipment/link.php?course_id=' . $course['id']); ?>" class="text-xs text-mint-500 hover:text-mint-400">+ Add</a>
            </div>
            <?php if (!empty($assignedEquipment)): ?>
            <div class="divide-y divide-dark-900">
                <?php foreach ($assignedEquipment as $eq): ?>
                <div class="px-4 py-2.5 flex items-center justify-between hover:bg-dark-925">
                    <div>
                        <p class="text-sm text-dark-200"><?php echo e($eq['name']); ?></p>
                        <p class="text-[10px] text-dark-500"><?php echo e($eq['type']); ?> • Qty: <?php echo $eq['quantity_needed']; ?></p>
                    </div>
                    <a href="<?php echo url('/course-equipment/unlink.php?course_id=' . $course['id'] . '&equipment_id=' . $eq['id']); ?>" class="text-dark-500 hover:text-red-400" data-confirm="Remove?"><i class="fas fa-times text-xs"></i></a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="p-6 text-center text-dark-500 text-sm">No equipment assigned</div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- SIDEBAR -->
    <div class="space-y-4">
        <div class="bg-dark-950 border border-dark-900 rounded-lg p-4">
            <h2 class="text-xs font-medium text-dark-400 uppercase tracking-wide mb-4">Actions</h2>
            <div class="space-y-1">
                <a href="<?php echo url('/courses/edit.php?id=' . $course['id']); ?>" class="flex items-center gap-2 px-2 py-1.5 text-sm text-dark-300 hover:text-dark-100 hover:bg-dark-900 rounded">
                    <i class="fas fa-pen w-4 text-xs"></i>Edit Course
                </a>
                <a href="<?php echo url('/course-equipment/link.php?course_id=' . $course['id']); ?>" class="flex items-center gap-2 px-2 py-1.5 text-sm text-dark-300 hover:text-dark-100 hover:bg-dark-900 rounded">
                    <i class="fas fa-link w-4 text-xs"></i>Assign Equipment
                </a>
                <a href="<?php echo url('/courses/delete.php?id=' . $course['id']); ?>" class="flex items-center gap-2 px-2 py-1.5 text-sm text-dark-300 hover:text-red-400 hover:bg-dark-900 rounded" data-confirm="Delete?">
                    <i class="fas fa-trash w-4 text-xs"></i>Delete Course
                </a>
            </div>
        </div>
        
        <div class="bg-dark-950 border border-dark-900 rounded-lg p-4">
            <h2 class="text-xs font-medium text-dark-400 uppercase tracking-wide mb-4">Meta</h2>
            <div class="space-y-3 text-xs">
                <div><span class="text-dark-500">ID:</span> <span class="text-dark-300">#<?php echo $course['id']; ?></span></div>
                <div><span class="text-dark-500">Created:</span> <span class="text-dark-300"><?php echo formatDate($course['created_at'], 'M j, Y'); ?></span></div>
                <div><span class="text-dark-500">Updated:</span> <span class="text-dark-300"><?php echo formatDate($course['updated_at'], 'M j, Y'); ?></span></div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>