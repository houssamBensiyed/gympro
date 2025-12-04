<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Edit Course Page - Modern Tailwind CSS Design
 * ============================================
 */

// Include required files
require_once __DIR__ . '/../../includes/courses/functions.php';

// Get course ID
$courseId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch course data
$course = getCourseById($courseId);

// If course not found, redirect with error
if (!$course) {
    setFlashMessage('error', 'Course not found.');
    redirect(url('/courses/'));
}

// Page configuration
$pageTitle = 'Edit Course';
$currentPage = 'courses';

// Initialize variables
$errors = [];
$formData = [
    'name' => $course['name'],
    'category' => $course['category'],
    'description' => $course['description'] ?? '',
    'course_date' => $course['course_date'],
    'start_time' => substr($course['start_time'], 0, 5),
    'duration_minutes' => $course['duration_minutes'],
    'max_participants' => $course['max_participants'],
    'instructor_name' => $course['instructor_name'],
    'location' => $course['location'] ?? '',
    'status' => $course['status']
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid request. Please try again.');
        redirect(url('/courses/edit.php?id=' . $courseId));
    }
    
    $formData = [
        'name' => sanitize($_POST['name'] ?? ''),
        'category' => sanitize($_POST['category'] ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
        'course_date' => sanitize($_POST['course_date'] ?? ''),
        'start_time' => sanitize($_POST['start_time'] ?? ''),
        'duration_minutes' => (int) ($_POST['duration_minutes'] ?? 60),
        'max_participants' => (int) ($_POST['max_participants'] ?? 20),
        'instructor_name' => sanitize($_POST['instructor_name'] ?? ''),
        'location' => sanitize($_POST['location'] ?? 'Main Hall'),
        'status' => sanitize($_POST['status'] ?? 'scheduled')
    ];
    
    $errors = validateCourseData($formData, $courseId);
    
    if (empty($errors)) {
        $result = updateCourse($courseId, $formData);
        
        if ($result !== false) {
            setFlashMessage('success', 'Course "' . $formData['name'] . '" has been updated successfully.');
            redirect(url('/courses/view.php?id=' . $courseId));
        } else {
            setFlashMessage('error', 'Failed to update course. Please try again.');
        }
    }
}

// Include header
require_once __DIR__ . '/../../includes/header.php';
?>

<!-- ============================================ -->
<!-- PAGE HEADER -->
<!-- ============================================ -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <nav class="flex items-center gap-2 text-sm text-dark-400 mb-3">
            <a href="<?php echo url('/courses/'); ?>" class="hover:text-mint-400 transition-colors">Courses</a>
            <i class="fas fa-chevron-right text-xs text-dark-600"></i>
            <a href="<?php echo url('/courses/view.php?id=' . $courseId); ?>" class="hover:text-mint-400 transition-colors"><?php echo e($course['name']); ?></a>
            <i class="fas fa-chevron-right text-xs text-dark-600"></i>
            <span class="text-dark-300">Edit</span>
        </nav>
        <h1 class="text-lg font-semibold tracking-tight text-white">Edit Course</h1>
    </div>
    <a href="<?php echo url('/courses/view.php?id=' . $courseId); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-dark-800 hover:bg-dark-600 text-white font-medium rounded-lg transition-colors">
        <i class="fas fa-arrow-left"></i> Back to Course
    </a>
</div>

<!-- ============================================ -->
<!-- COURSE FORM -->
<!-- ============================================ -->
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-dark-950 backdrop-blur-sm rounded-lg border border-dark-900 overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-dark-900">
            <h2 class="flex items-center gap-2 text-lg font-semibold text-white">
                <i class="fas fa-edit text-amber-400"></i>
                Course Information
            </h2>
            <?php 
            $statusColors = [
                'scheduled' => 'bg-accent-500/10 text-mint-400 border-accent-500/30',
                'in_progress' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                'completed' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                'cancelled' => 'bg-rose-500/10 text-rose-400 border-rose-500/30'
            ];
            $statusClass = $statusColors[$course['status']] ?? 'bg-surface-500/10 text-dark-400 border-surface-500/30';
            ?>
            <span class="px-3 py-1 text-xs font-medium rounded-lg border <?php echo $statusClass; ?>">
                <?php echo e(COURSE_STATUSES[$course['status']] ?? ucfirst(str_replace('_', ' ', $course['status']))); ?>
            </span>
        </div>
        <div class="p-6">
            <form action="" method="POST" novalidate>
                <?php echo csrfField(); ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Course Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-dark-300 mb-2">
                            Course Name <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="w-full px-4 py-3 bg-dark-975/50 border <?php echo isset($errors['name']) ? 'border-rose-500' : 'border-dark-800'; ?> rounded-lg text-white placeholder-dark-500 focus:outline-none focus:border-mint-500 focus:ring-2 focus:ring-mint-500/20 transition-all"
                               value="<?php echo e($formData['name']); ?>"
                               placeholder="Enter course name"
                               maxlength="100"
                               required>
                        <?php if (isset($errors['name'])): ?>
                        <p class="mt-2 text-sm text-rose-400"><?php echo e($errors['name']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-dark-300 mb-2">
                            Category <span class="text-rose-400">*</span>
                        </label>
                        <select id="category" 
                                name="category" 
                                class="w-full px-4 py-3 bg-dark-975/50 border <?php echo isset($errors['category']) ? 'border-rose-500' : 'border-dark-800'; ?> rounded-lg text-white focus:outline-none focus:border-mint-500 focus:ring-2 focus:ring-mint-500/20 transition-all"
                                required>
                            <option value="">Select Category</option>
                            <?php foreach (COURSE_CATEGORIES as $cat): ?>
                            <option value="<?php echo e($cat); ?>" <?php echo $formData['category'] === $cat ? 'selected' : ''; ?>>
                                <?php echo e($cat); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['category'])): ?>
                        <p class="mt-2 text-sm text-rose-400"><?php echo e($errors['category']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-dark-300 mb-2">Status</label>
                        <select id="status" 
                                name="status" 
                                class="w-full px-4 py-3 bg-dark-975/50 border border-dark-800 rounded-lg text-white focus:outline-none focus:border-mint-500 focus:ring-2 focus:ring-mint-500/20 transition-all">
                            <?php foreach (COURSE_STATUSES as $key => $label): ?>
                            <option value="<?php echo e($key); ?>" <?php echo $formData['status'] === $key ? 'selected' : ''; ?>>
                                <?php echo e($label); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Course Date -->
                    <div>
                        <label for="course_date" class="block text-sm font-medium text-dark-300 mb-2">
                            Date <span class="text-rose-400">*</span>
                        </label>
                        <input type="date" 
                               id="course_date" 
                               name="course_date" 
                               class="w-full px-4 py-3 bg-dark-975/50 border <?php echo isset($errors['course_date']) ? 'border-rose-500' : 'border-dark-800'; ?> rounded-lg text-white focus:outline-none focus:border-mint-500 focus:ring-2 focus:ring-mint-500/20 transition-all"
                               value="<?php echo e($formData['course_date']); ?>"
                               required>
                        <?php if (isset($errors['course_date'])): ?>
                        <p class="mt-2 text-sm text-rose-400"><?php echo e($errors['course_date']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Start Time -->
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-dark-300 mb-2">
                            Start Time <span class="text-rose-400">*</span>
                        </label>
                        <input type="time" 
                               id="start_time" 
                               name="start_time" 
                               class="w-full px-4 py-3 bg-dark-975/50 border <?php echo isset($errors['start_time']) ? 'border-rose-500' : 'border-dark-800'; ?> rounded-lg text-white focus:outline-none focus:border-mint-500 focus:ring-2 focus:ring-mint-500/20 transition-all"
                               value="<?php echo e($formData['start_time']); ?>"
                               required>
                        <?php if (isset($errors['start_time'])): ?>
                        <p class="mt-2 text-sm text-rose-400"><?php echo e($errors['start_time']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Duration -->
                    <div>
                        <label for="duration_minutes" class="block text-sm font-medium text-dark-300 mb-2">
                            Duration (minutes) <span class="text-rose-400">*</span>
                        </label>
                        <input type="number" 
                               id="duration_minutes" 
                               name="duration_minutes" 
                               class="w-full px-4 py-3 bg-dark-975/50 border <?php echo isset($errors['duration_minutes']) ? 'border-rose-500' : 'border-dark-800'; ?> rounded-lg text-white focus:outline-none focus:border-mint-500 focus:ring-2 focus:ring-mint-500/20 transition-all"
                               value="<?php echo e($formData['duration_minutes']); ?>"
                               min="1"
                               max="480"
                               required>
                        <p class="mt-1.5 text-xs text-dark-500">Maximum 480 minutes (8 hours)</p>
                        <?php if (isset($errors['duration_minutes'])): ?>
                        <p class="mt-2 text-sm text-rose-400"><?php echo e($errors['duration_minutes']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Max Participants -->
                    <div>
                        <label for="max_participants" class="block text-sm font-medium text-dark-300 mb-2">
                            Max Participants <span class="text-rose-400">*</span>
                        </label>
                        <input type="number" 
                               id="max_participants" 
                               name="max_participants" 
                               class="w-full px-4 py-3 bg-dark-975/50 border <?php echo isset($errors['max_participants']) ? 'border-rose-500' : 'border-dark-800'; ?> rounded-lg text-white focus:outline-none focus:border-mint-500 focus:ring-2 focus:ring-mint-500/20 transition-all"
                               value="<?php echo e($formData['max_participants']); ?>"
                               min="1"
                               max="100"
                               required>
                        <p class="mt-1.5 text-xs text-dark-500">Max 100. Current: <?php echo $course['current_participants']; ?> registered.</p>
                        <?php if (isset($errors['max_participants'])): ?>
                        <p class="mt-2 text-sm text-rose-400"><?php echo e($errors['max_participants']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Instructor Name -->
                    <div>
                        <label for="instructor_name" class="block text-sm font-medium text-dark-300 mb-2">
                            Instructor <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" 
                               id="instructor_name" 
                               name="instructor_name" 
                               class="w-full px-4 py-3 bg-dark-975/50 border <?php echo isset($errors['instructor_name']) ? 'border-rose-500' : 'border-dark-800'; ?> rounded-lg text-white placeholder-dark-500 focus:outline-none focus:border-mint-500 focus:ring-2 focus:ring-mint-500/20 transition-all"
                               value="<?php echo e($formData['instructor_name']); ?>"
                               placeholder="Instructor name"
                               maxlength="100"
                               required>
                        <?php if (isset($errors['instructor_name'])): ?>
                        <p class="mt-2 text-sm text-rose-400"><?php echo e($errors['instructor_name']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-dark-300 mb-2">Location</label>
                        <input type="text" 
                               id="location" 
                               name="location" 
                               class="w-full px-4 py-3 bg-dark-975/50 border border-dark-800 rounded-lg text-white placeholder-dark-500 focus:outline-none focus:border-mint-500 focus:ring-2 focus:ring-mint-500/20 transition-all"
                               value="<?php echo e($formData['location']); ?>"
                               placeholder="e.g., Main Hall, Yoga Studio"
                               maxlength="100">
                    </div>
                    
                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-dark-300 mb-2">Description</label>
                        <textarea id="description" 
                                  name="description" 
                                  class="w-full px-4 py-3 bg-dark-975/50 border border-dark-800 rounded-lg text-white placeholder-dark-500 focus:outline-none focus:border-mint-500 focus:ring-2 focus:ring-mint-500/20 transition-all resize-none"
                                  rows="4"
                                  placeholder="Enter course description (optional)"
                                  maxlength="5000"><?php echo e($formData['description']); ?></textarea>
                        <p class="mt-1.5 text-xs text-dark-500">Optional. Maximum 5000 characters.</p>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-dark-900">
                    <a href="<?php echo url('/courses/view.php?id=' . $courseId); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-dark-800 hover:bg-dark-600 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-mint-600 to-mint-700 hover:from-mint-500 hover:to-mint-600 text-white font-medium rounded-lg shadow-lg shadow-mint-500/25 transition-all duration-200">
                        <i class="fas fa-save"></i> Update Course
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Course Metadata -->
    <div class="bg-dark-950 backdrop-blur-sm rounded-lg border border-dark-900 overflow-hidden">
        <div class="flex items-center gap-2 p-5 border-b border-dark-900">
            <i class="fas fa-info-circle text-cyan-400"></i>
            <h2 class="text-lg font-semibold text-white">Course Metadata</h2>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                <div>
                    <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Course ID</p>
                    <p class="text-sm text-white">#<?php echo $course['id']; ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Created</p>
                    <p class="text-sm text-white"><?php echo formatDate($course['created_at'], 'M d, Y'); ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Last Updated</p>
                    <p class="text-sm text-white"><?php echo formatDate($course['updated_at'], 'M d, Y'); ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mb-1">Equipment</p>
                    <p class="text-sm text-white"><?php echo $course['equipment_count']; ?> items</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once __DIR__ . '/../../includes/footer.php';
?>