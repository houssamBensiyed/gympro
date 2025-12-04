<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Create Course Page - Ultra-Sharp Design
 * ============================================
 */

$pageTitle = 'Add Course';
$currentPage = 'courses';

require_once __DIR__ . '/../../includes/courses/functions.php';

$errors = [];
$formData = [
    'name' => '',
    'category' => '',
    'description' => '',
    'course_date' => date('Y-m-d', strtotime('+1 day')),
    'start_time' => '09:00',
    'duration_minutes' => 60,
    'max_participants' => 20,
    'instructor_name' => '',
    'location' => 'Main Hall',
    'status' => 'scheduled'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid request. Please try again.');
        redirect(url('/courses/create.php'));
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
    
    $errors = validateCourseData($formData);
    
    if (empty($errors)) {
        $courseId = createCourse($formData);
        
        if ($courseId) {
            setFlashMessage('success', 'Course created successfully.');
            redirect(url('/courses/view.php?id=' . $courseId));
        } else {
            setFlashMessage('error', 'Failed to create course.');
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- BREADCRUMB -->
<div class="flex items-center gap-2 text-xs text-dark-400 mb-6">
    <a href="<?php echo url('/courses/'); ?>" class="hover:text-dark-200">Courses</a>
    <span>/</span>
    <span class="text-dark-200">New Course</span>
</div>

<!-- FORM CARD -->
<div class="max-w-2xl mx-auto">
    <div class="bg-dark-950 border border-dark-900 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-dark-900">
            <h1 class="text-base font-semibold text-white">Create New Course</h1>
            <p class="text-xs text-dark-400 mt-0.5">Fill in the course details below</p>
        </div>
        
        <form action="" method="POST" class="p-6">
            <?php echo csrfField(); ?>
            
            <div class="space-y-5">
                <!-- Course Name -->
                <div>
                    <label for="name" class="block text-xs font-medium text-dark-300 mb-1.5">
                        Course Name <span class="text-red-400">*</span>
                    </label>
                    <input type="text" id="name" name="name" 
                           class="w-full px-3 py-2 bg-dark-925 border <?php echo isset($errors['name']) ? 'border-red-500' : 'border-dark-800'; ?> rounded text-sm text-white placeholder-dark-500 focus:outline-none focus:border-mint-500"
                           value="<?php echo e($formData['name']); ?>"
                           placeholder="e.g., Morning Yoga Class">
                    <?php if (isset($errors['name'])): ?>
                    <p class="mt-1 text-xs text-red-400"><?php echo e($errors['name']); ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Two columns -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="category" class="block text-xs font-medium text-dark-300 mb-1.5">
                            Category <span class="text-red-400">*</span>
                        </label>
                        <select id="category" name="category" 
                                class="w-full px-3 py-2 bg-dark-925 border <?php echo isset($errors['category']) ? 'border-red-500' : 'border-dark-800'; ?> rounded text-sm text-white focus:outline-none focus:border-mint-500">
                            <option value="">Select</option>
                            <?php foreach (COURSE_CATEGORIES as $cat): ?>
                            <option value="<?php echo e($cat); ?>" <?php echo $formData['category'] === $cat ? 'selected' : ''; ?>><?php echo e($cat); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['category'])): ?>
                        <p class="mt-1 text-xs text-red-400"><?php echo e($errors['category']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="status" class="block text-xs font-medium text-dark-300 mb-1.5">Status</label>
                        <select id="status" name="status" 
                                class="w-full px-3 py-2 bg-dark-925 border border-dark-800 rounded text-sm text-white focus:outline-none focus:border-mint-500">
                            <?php foreach (COURSE_STATUSES as $key => $label): ?>
                            <option value="<?php echo e($key); ?>" <?php echo $formData['status'] === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Date & Time -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="course_date" class="block text-xs font-medium text-dark-300 mb-1.5">
                            Date <span class="text-red-400">*</span>
                        </label>
                        <input type="date" id="course_date" name="course_date" 
                               class="w-full px-3 py-2 bg-dark-925 border <?php echo isset($errors['course_date']) ? 'border-red-500' : 'border-dark-800'; ?> rounded text-sm text-white focus:outline-none focus:border-mint-500"
                               value="<?php echo e($formData['course_date']); ?>">
                        <?php if (isset($errors['course_date'])): ?>
                        <p class="mt-1 text-xs text-red-400"><?php echo e($errors['course_date']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="start_time" class="block text-xs font-medium text-dark-300 mb-1.5">
                            Start Time <span class="text-red-400">*</span>
                        </label>
                        <input type="time" id="start_time" name="start_time" 
                               class="w-full px-3 py-2 bg-dark-925 border <?php echo isset($errors['start_time']) ? 'border-red-500' : 'border-dark-800'; ?> rounded text-sm text-white focus:outline-none focus:border-mint-500"
                               value="<?php echo e($formData['start_time']); ?>">
                        <?php if (isset($errors['start_time'])): ?>
                        <p class="mt-1 text-xs text-red-400"><?php echo e($errors['start_time']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Duration & Participants -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="duration_minutes" class="block text-xs font-medium text-dark-300 mb-1.5">
                            Duration (min) <span class="text-red-400">*</span>
                        </label>
                        <input type="number" id="duration_minutes" name="duration_minutes" 
                               class="w-full px-3 py-2 bg-dark-925 border <?php echo isset($errors['duration_minutes']) ? 'border-red-500' : 'border-dark-800'; ?> rounded text-sm text-white focus:outline-none focus:border-mint-500"
                               value="<?php echo e($formData['duration_minutes']); ?>" min="1" max="480">
                        <?php if (isset($errors['duration_minutes'])): ?>
                        <p class="mt-1 text-xs text-red-400"><?php echo e($errors['duration_minutes']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="max_participants" class="block text-xs font-medium text-dark-300 mb-1.5">
                            Max Participants <span class="text-red-400">*</span>
                        </label>
                        <input type="number" id="max_participants" name="max_participants" 
                               class="w-full px-3 py-2 bg-dark-925 border <?php echo isset($errors['max_participants']) ? 'border-red-500' : 'border-dark-800'; ?> rounded text-sm text-white focus:outline-none focus:border-mint-500"
                               value="<?php echo e($formData['max_participants']); ?>" min="1" max="100">
                        <?php if (isset($errors['max_participants'])): ?>
                        <p class="mt-1 text-xs text-red-400"><?php echo e($errors['max_participants']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Instructor & Location -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="instructor_name" class="block text-xs font-medium text-dark-300 mb-1.5">
                            Instructor <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="instructor_name" name="instructor_name" 
                               class="w-full px-3 py-2 bg-dark-925 border <?php echo isset($errors['instructor_name']) ? 'border-red-500' : 'border-dark-800'; ?> rounded text-sm text-white placeholder-dark-500 focus:outline-none focus:border-mint-500"
                               value="<?php echo e($formData['instructor_name']); ?>" placeholder="Instructor name">
                        <?php if (isset($errors['instructor_name'])): ?>
                        <p class="mt-1 text-xs text-red-400"><?php echo e($errors['instructor_name']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="location" class="block text-xs font-medium text-dark-300 mb-1.5">Location</label>
                        <input type="text" id="location" name="location" 
                               class="w-full px-3 py-2 bg-dark-925 border border-dark-800 rounded text-sm text-white placeholder-dark-500 focus:outline-none focus:border-mint-500"
                               value="<?php echo e($formData['location']); ?>" placeholder="e.g., Main Hall">
                    </div>
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
                <a href="<?php echo url('/courses/'); ?>" class="px-4 py-2 text-sm text-dark-300 hover:text-white">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-mint-600 hover:bg-mint-500 text-white text-sm font-medium rounded">
                    Create Course
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>