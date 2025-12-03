/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Main JavaScript File
 * ============================================
 */

(function() {
    'use strict';
    
    // ============================================
    // DOM READY
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        initSidebarToggle();
        initCurrentTime();
        initAlertDismiss();
        initFormValidation();
        initConfirmDialogs();
    });
    
    // ============================================
    // SIDEBAR TOGGLE
    // ============================================
    function initSidebarToggle() {
        const toggleBtn = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const appWrapper = document.querySelector('.app-wrapper');
        
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('open');
                appWrapper.classList.toggle('sidebar-collapsed');
                
                // Save state to localStorage
                const isCollapsed = appWrapper.classList.contains('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            });
            
            // Restore state from localStorage
            const savedState = localStorage.getItem('sidebarCollapsed');
            if (savedState === 'true') {
                appWrapper.classList.add('sidebar-collapsed');
            }
        }
    }
    
    // ============================================
    // CURRENT TIME DISPLAY
    // ============================================
    function initCurrentTime() {
        const timeElement = document.getElementById('currentTime');
        
        if (timeElement) {
            function updateTime() {
                const now = new Date();
                const options = { 
                    hour: 'numeric', 
                    minute: '2-digit',
                    hour12: true 
                };
                timeElement.textContent = now.toLocaleTimeString('en-US', options);
            }
            
            updateTime();
            setInterval(updateTime, 60000); // Update every minute
        }
    }
    
    // ============================================
    // ALERT AUTO-DISMISS
    // ============================================
    function initAlertDismiss() {
        const alerts = document.querySelectorAll('.alert');
        
        alerts.forEach(function(alert) {
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            }, 5000);
        });
    }
    
    // ============================================
    // FORM VALIDATION
    // ============================================
    function initFormValidation() {
        const forms = document.querySelectorAll('form[data-validate]');
        
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Clear previous errors
                form.querySelectorAll('.form-error').forEach(el => el.remove());
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                
                // Validate required fields
                form.querySelectorAll('[required]').forEach(function(field) {
                    if (!field.value.trim()) {
                        isValid = false;
                        showFieldError(field, 'This field is required.');
                    }
                });
                
                // Validate email fields
                form.querySelectorAll('[type="email"]').forEach(function(field) {
                    if (field.value && !isValidEmail(field.value)) {
                        isValid = false;
                        showFieldError(field, 'Please enter a valid email address.');
                    }
                });
                
                // Validate number fields
                form.querySelectorAll('[type="number"]').forEach(function(field) {
                    const min = field.getAttribute('min');
                    const max = field.getAttribute('max');
                    const value = parseFloat(field.value);
                    
                    if (min !== null && value < parseFloat(min)) {
                        isValid = false;
                        showFieldError(field, `Value must be at least ${min}.`);
                    }
                    
                    if (max !== null && value > parseFloat(max)) {
                        isValid = false;
                        showFieldError(field, `Value must not exceed ${max}.`);
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first error
                    const firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstError.focus();
                    }
                }
            });
        });
    }
    
    function showFieldError(field, message) {
        field.classList.add('is-invalid');
        
        const errorElement = document.createElement('span');
        errorElement.className = 'form-error';
        errorElement.textContent = message;
        
        field.parentNode.appendChild(errorElement);
    }
    
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    // ============================================
    // CONFIRM DIALOGS
    // ============================================
    function initConfirmDialogs() {
        const confirmButtons = document.querySelectorAll('[data-confirm]');
        
        confirmButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                const message = this.getAttribute('data-confirm') || 'Are you sure?';
                
                if (!confirm(message)) {
                    e.preventDefault();
                }
            });
        });
    }
    
    // ============================================
    // UTILITY FUNCTIONS
    // ============================================
    
    // Format number with commas
    window.formatNumber = function(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    };
    
    // Debounce function
    window.debounce = function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };
    
    // Show loading state
    window.showLoading = function(element) {
        element.classList.add('loading');
        element.disabled = true;
        element.dataset.originalText = element.innerHTML;
        element.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    };
    
    // Hide loading state
    window.hideLoading = function(element) {
        element.classList.remove('loading');
        element.disabled = false;
        element.innerHTML = element.dataset.originalText;
    };
    
})();