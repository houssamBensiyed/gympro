<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Dashboard Page - Ultra-Sharp Design
 * ============================================
 */

// Page configuration
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
$pageScripts = ['charts.js'];

// Include header
require_once __DIR__ . '/../includes/header.php';

// Get dashboard data
$stats = getDashboardStats();
$coursesByCategory = getCoursesByCategory();
$equipmentByType = getEquipmentByType();
$equipmentByCondition = getEquipmentByCondition();
$upcomingCourses = getUpcomingCourses(5);
$recentEquipment = getRecentEquipment(5);
$lowStockEquipment = getLowStockEquipment(5);
?>

<!-- STATS GRID -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-dark-950 border border-dark-900 rounded-lg p-4">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-dark-400 uppercase tracking-wide">Courses</span>
            <i class="fas fa-calendar text-dark-600 text-xs"></i>
        </div>
        <div class="text-2xl font-semibold text-dark-50 tracking-tight"><?php echo formatNumber($stats['total_courses']); ?></div>
        <div class="text-xs text-mint-500 mt-1"><?php echo $stats['scheduled_courses']; ?> scheduled</div>
    </div>
    
    <div class="bg-dark-950 border border-dark-900 rounded-lg p-4">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-dark-400 uppercase tracking-wide">Equipment</span>
            <i class="fas fa-dumbbell text-dark-600 text-xs"></i>
        </div>
        <div class="text-2xl font-semibold text-dark-50 tracking-tight"><?php echo formatNumber($stats['total_equipment']); ?></div>
        <div class="text-xs text-mint-500 mt-1"><?php echo $stats['active_equipment']; ?> active</div>
    </div>
    
    <div class="bg-dark-950 border border-dark-900 rounded-lg p-4">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-dark-400 uppercase tracking-wide">Scheduled</span>
            <i class="fas fa-clock text-dark-600 text-xs"></i>
        </div>
        <div class="text-2xl font-semibold text-dark-50 tracking-tight"><?php echo formatNumber($stats['scheduled_courses']); ?></div>
        <div class="text-xs text-dark-500 mt-1">upcoming</div>
    </div>
    
    <div class="bg-dark-950 border border-dark-900 rounded-lg p-4">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-dark-400 uppercase tracking-wide">Assigned</span>
            <i class="fas fa-link text-dark-600 text-xs"></i>
        </div>
        <div class="text-2xl font-semibold text-dark-50 tracking-tight"><?php echo formatNumber($stats['total_assignments']); ?></div>
        <div class="text-xs text-dark-500 mt-1">equipment links</div>
    </div>
</div>

<!-- CHARTS ROW -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <div class="bg-dark-950 border border-dark-900 rounded-lg p-4">
        <h3 class="text-sm font-medium text-dark-200 mb-4">Courses by Category</h3>
        <div class="h-48">
            <canvas id="coursesCategoryChart"></canvas>
        </div>
    </div>
    
    <div class="bg-dark-950 border border-dark-900 rounded-lg p-4">
        <h3 class="text-sm font-medium text-dark-200 mb-4">Equipment by Type</h3>
        <div class="h-48">
            <canvas id="equipmentTypeChart"></canvas>
        </div>
    </div>
</div>

<!-- TABLES ROW -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <!-- Upcoming Courses -->
    <div class="bg-dark-950 border border-dark-900 rounded-lg overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-dark-900">
            <h3 class="text-sm font-medium text-dark-200">Upcoming Courses</h3>
            <a href="<?php echo url('/courses/'); ?>" class="text-xs text-mint-500 hover:text-mint-400">View all →</a>
        </div>
        <div class="divide-y divide-dark-900">
            <?php if (!empty($upcomingCourses)): ?>
                <?php foreach ($upcomingCourses as $course): ?>
                <div class="px-4 py-3 hover:bg-dark-925">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-dark-100"><?php echo e($course['name']); ?></p>
                            <p class="text-xs text-dark-500 mt-0.5"><?php echo e($course['instructor_name']); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-dark-300"><?php echo formatDate($course['course_date'], 'M j'); ?></p>
                            <span class="inline-block mt-1 text-[10px] px-1.5 py-0.5 rounded bg-dark-800 text-dark-300"><?php echo e($course['category']); ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="px-4 py-8 text-center text-dark-500 text-sm">No upcoming courses</div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Recent Equipment -->
    <div class="bg-dark-950 border border-dark-900 rounded-lg overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-dark-900">
            <h3 class="text-sm font-medium text-dark-200">Recent Equipment</h3>
            <a href="<?php echo url('/equipment/'); ?>" class="text-xs text-mint-500 hover:text-mint-400">View all →</a>
        </div>
        <div class="divide-y divide-dark-900">
            <?php if (!empty($recentEquipment)): ?>
                <?php foreach ($recentEquipment as $equipment): ?>
                <div class="px-4 py-3 hover:bg-dark-925">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-dark-100"><?php echo e($equipment['name']); ?></p>
                            <p class="text-xs text-dark-500 mt-0.5"><?php echo e($equipment['type']); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-dark-200"><?php echo $equipment['quantity']; ?></p>
                            <?php 
                            $condColor = match($equipment['equipment_condition']) {
                                'excellent' => 'text-mint-500',
                                'good' => 'text-dark-300',
                                'fair' => 'text-amber-500',
                                'needs_repair' => 'text-red-500',
                                default => 'text-dark-400'
                            };
                            ?>
                            <span class="text-[10px] <?php echo $condColor; ?>"><?php echo e(EQUIPMENT_CONDITIONS[$equipment['equipment_condition']] ?? 'Unknown'); ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="px-4 py-8 text-center text-dark-500 text-sm">No equipment found</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($lowStockEquipment)): ?>
<!-- LOW STOCK ALERT -->
<div class="bg-dark-950 border border-amber-900/50 rounded-lg p-4">
    <div class="flex items-center gap-2 mb-3">
        <i class="fas fa-exclamation-triangle text-amber-500 text-xs"></i>
        <h3 class="text-sm font-medium text-amber-400">Low Stock Alert</h3>
    </div>
    <div class="flex flex-wrap gap-2">
        <?php foreach ($lowStockEquipment as $item): ?>
        <span class="text-xs px-2 py-1 bg-dark-900 rounded text-dark-300">
            <?php echo e($item['name']); ?> <span class="text-amber-500">(<?php echo $item['quantity']; ?>)</span>
        </span>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- CHART DATA -->
<script>
    window.chartData = {
        coursesByCategory: <?php echo json_encode($coursesByCategory); ?>,
        equipmentByType: <?php echo json_encode($equipmentByType); ?>,
        equipmentByCondition: <?php echo json_encode($equipmentByCondition); ?>,
        courseStats: {
            scheduled: <?php echo $stats['scheduled_courses']; ?>,
            completed: <?php echo $stats['completed_courses']; ?>,
            cancelled: <?php echo $stats['cancelled_courses']; ?>
        }
    };
</script>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>