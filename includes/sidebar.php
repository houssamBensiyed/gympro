<aside class="fixed top-0 left-0 w-56 h-screen bg-dark-975 border-r border-dark-900 flex flex-col z-50" id="sidebar">
    
    <!-- Logo -->
    <div class="h-14 flex items-center px-4 border-b border-dark-900">
        <a href="<?php echo url('/'); ?>" class="flex items-center gap-2">
            <div class="w-6 h-6 rounded bg-mint-600 flex items-center justify-center">
                <i class="fas fa-dumbbell text-white text-xs"></i>
            </div>
            <span class="text-sm font-semibold text-dark-50 tracking-tight">GymPro</span>
        </a>
        <button class="lg:hidden ml-auto text-dark-400 hover:text-white" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-3 px-2">
        <!-- Main -->
        <div class="mb-4">
            <span class="px-2 text-[10px] font-medium uppercase tracking-widest text-dark-500">Main</span>
            <ul class="mt-2 space-y-0.5">
                <li>
                    <a href="<?php echo url('/'); ?>" class="flex items-center gap-2 px-2 py-1.5 rounded text-sm <?php echo $currentPage === 'dashboard' ? 'bg-dark-900 text-mint-400' : 'text-dark-300 hover:text-dark-50 hover:bg-dark-925'; ?>">
                        <i class="fas fa-grid-2 w-4 text-center text-xs"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Management -->
        <div class="mb-4">
            <span class="px-2 text-[10px] font-medium uppercase tracking-widest text-dark-500">Management</span>
            <ul class="mt-2 space-y-0.5">
                <li>
                    <a href="<?php echo url('/courses/'); ?>" class="flex items-center gap-2 px-2 py-1.5 rounded text-sm <?php echo $currentPage === 'courses' ? 'bg-dark-900 text-mint-400' : 'text-dark-300 hover:text-dark-50 hover:bg-dark-925'; ?>">
                        <i class="fas fa-calendar w-4 text-center text-xs"></i>
                        <span>Courses</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo url('/equipment/'); ?>" class="flex items-center gap-2 px-2 py-1.5 rounded text-sm <?php echo $currentPage === 'equipment' ? 'bg-dark-900 text-mint-400' : 'text-dark-300 hover:text-dark-50 hover:bg-dark-925'; ?>">
                        <i class="fas fa-dumbbell w-4 text-center text-xs"></i>
                        <span>Equipment</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo url('/course-equipment/'); ?>" class="flex items-center gap-2 px-2 py-1.5 rounded text-sm <?php echo $currentPage === 'course-equipment' ? 'bg-dark-900 text-mint-400' : 'text-dark-300 hover:text-dark-50 hover:bg-dark-925'; ?>">
                        <i class="fas fa-link w-4 text-center text-xs"></i>
                        <span>Assignments</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Reports -->
        <div class="mb-4">
            <span class="px-2 text-[10px] font-medium uppercase tracking-widest text-dark-500">Reports</span>
            <ul class="mt-2 space-y-0.5">
                <li>
                    <a href="<?php echo url('/export/courses.php'); ?>" class="flex items-center gap-2 px-2 py-1.5 rounded text-sm <?php echo $currentPage === 'export-courses' ? 'bg-dark-900 text-mint-400' : 'text-dark-300 hover:text-dark-50 hover:bg-dark-925'; ?>">
                        <i class="fas fa-file-arrow-down w-4 text-center text-xs"></i>
                        <span>Export Courses</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo url('/export/equipment.php'); ?>" class="flex items-center gap-2 px-2 py-1.5 rounded text-sm <?php echo $currentPage === 'export-equipment' ? 'bg-dark-900 text-mint-400' : 'text-dark-300 hover:text-dark-50 hover:bg-dark-925'; ?>">
                        <i class="fas fa-file-export w-4 text-center text-xs"></i>
                        <span>Export Equipment</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    
    <!-- User -->
    <div class="p-3 border-t border-dark-900">
        <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
        <a href="<?php echo url('/logout.php'); ?>" class="flex items-center gap-2 px-2 py-1.5 rounded text-sm text-dark-400 hover:text-red-400 hover:bg-dark-925">
            <i class="fas fa-sign-out-alt w-4 text-center text-xs"></i>
            <span>Logout</span>
        </a>
        <?php endif; ?>
    </div>
</aside>

<!-- Mobile Overlay -->
<div class="fixed inset-0 bg-black/80 z-40 lg:hidden hidden" id="sidebarOverlay"></div>
