<?php
$messages = getFlashMessages();
if (!empty($messages)):
?>
<!-- Floating Toast Alerts -->
<div class="fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-sm" id="alertContainer">
    <?php foreach ($messages as $index => $msg): 
        $bgColor = match($msg['type']) {
            'success' => 'bg-mint-600 text-white',
            'error' => 'bg-red-600 text-white',
            'warning' => 'bg-amber-600 text-white',
            default => 'bg-dark-800 text-dark-100'
        };
        $icon = match($msg['type']) {
            'success' => 'fa-check-circle',
            'error' => 'fa-exclamation-circle',
            'warning' => 'fa-exclamation-triangle',
            default => 'fa-info-circle'
        };
    ?>
    <div class="<?php echo $bgColor; ?> px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in" data-alert="<?php echo $index; ?>">
        <i class="fas <?php echo $icon; ?> text-sm"></i>
        <span class="text-sm font-medium flex-1"><?php echo e($msg['message']); ?></span>
        <button onclick="dismissAlert(<?php echo $index; ?>)" class="text-white/70 hover:text-white">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>
    <?php endforeach; ?>
</div>

<style>
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .animate-slide-in { animation: slideIn 0.3s ease-out; }
</style>

<script>
    function dismissAlert(index) {
        const alert = document.querySelector('[data-alert="' + index + '"]');
        if (alert) {
            alert.style.transform = 'translateX(100%)';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 200);
        }
    }
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('#alertContainer > div').forEach((alert, i) => {
            setTimeout(() => dismissAlert(i), i * 100);
        });
    }, 5000);
</script>
<?php endif; ?>
