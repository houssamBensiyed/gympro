<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Delete Course Page - Modern Tailwind CSS
 * ============================================
 */

require_once __DIR__ . '/../../includes/courses/functions.php';

$courseId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$course = getCourseById($courseId);

if (!$course) {
    setFlashMessage('error', 'Course not found.');
    redirect(url('/courses/'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid request.');
        redirect(url('/courses/'));
    }
    
    $result = deleteCourse($courseId);
    
    if ($result) {
        setFlashMessage('success', 'Course "' . $course['name'] . '" deleted successfully.');
    } else {
        setFlashMessage('error', 'Failed to delete course.');
    }
    
    redirect(url('/courses/'));
}

$pageTitle = 'Delete Course';
$currentPage = 'courses';

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- PAGE HEADER -->
<div class="mb-8">
    <nav class="flex items-center gap-2 text-sm text-slate-400 mb-3">
        <a href="<?php echo url('/courses/'); ?>" class="hover:text-mint-400 transition-colors">Courses</a>
        <i class="fas fa-chevron-right text-xs text-slate-600"></i>
        <span class="text-slate-300">Delete</span>
    </nav>
    <h1 class="text-lg font-semibold tracking-tight text-white">Delete Course</h1>
</div>

<!-- CONFIRMATION CARD -->
<div class="max-w-xl">
    <div class="bg-slate-800/50 backdrop-blur-sm rounded-lg border border-rose-500/30 overflow-hidden">
        <div class="flex items-center gap-3 p-5 border-b border-dark-800/50 bg-rose-500/5">
            <div class="w-10 h-10 rounded-lg bg-rose-500/20 flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-rose-400"></i>
            </div>
            <h2 class="text-lg font-semibold text-white">Confirm Deletion</h2>
        </div>
        <div class="p-6">
            <p class="text-slate-300 mb-6">Are you sure you want to delete this course? This action cannot be undone.</p>
            
            <div class="bg-slate-900/50 rounded-lg p-4 mb-6 space-y-4">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-accent-500/10 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-mint-400"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Course Name</p>
                        <p class="text-sm text-white font-medium"><?php echo e($course['name']); ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-amber-500/10 flex items-center justify-center">
                        <i class="fas fa-user-tie text-amber-400"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Instructor</p>
                        <p class="text-sm text-white"><?php echo e($course['instructor_name']); ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-cyan-500/10 flex items-center justify-center">
                        <i class="fas fa-clock text-cyan-400"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Date & Time</p>
                        <p class="text-sm text-white"><?php echo formatDate($course['course_date']); ?> at <?php echo formatTime($course['start_time']); ?></p>
                    </div>
                </div>
            </div>
            
            <?php if ($course['equipment_count'] > 0): ?>
            <div class="bg-amber-500/10 border border-amber-500/30 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-amber-400 mt-0.5"></i>
                    <p class="text-sm text-amber-200">
                        This course has <strong><?php echo $course['equipment_count']; ?></strong> equipment items assigned. 
                        Deleting this course will remove all equipment assignments.
                    </p>
                </div>
            </div>
            <?php endif; ?>
            
            <form action="" method="POST">
                <?php echo csrfField(); ?>
                
                <div class="flex gap-4">
                    <a href="<?php echo url('/courses/view.php?id=' . $courseId); ?>" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-dark-800 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
