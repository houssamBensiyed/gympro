<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Register Page - Ultra-Sharp Design
 * ============================================
 */

$pageTitle = 'Register';
$isAuthPage = true;

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth/functions.php';

if (isLoggedIn()) {
    redirect(url('/'));
}

$errors = [];
$oldInput = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors['general'] = 'Invalid request. Please try again.';
    } else {
        $data = [
            'username' => sanitize($_POST['username'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? ''
        ];
        
        $oldInput = [
            'username' => $data['username'],
            'email' => $data['email']
        ];
        
        $result = registerUser($data);
        
        if ($result['success']) {
            setFlashMessage('success', $result['message']);
            redirect(url('/login.php'));
        } else {
            if (isset($result['errors'])) {
                $errors = $result['errors'];
            } else {
                $errors['general'] = $result['message'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?> | <?php echo e(APP_NAME); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        dark: { 900: '#18181b', 925: '#141416', 950: '#0e0e10', 975: '#0a0a0b' },
                        mint: { 500: '#22c55e', 600: '#16a34a' }
                    }
                }
            }
        }
    </script>
    <style>
        body { background: #0a0a0b; letter-spacing: -0.01em; }
        h1, h2 { letter-spacing: -0.025em; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 font-sans antialiased text-zinc-100">
    
    <div class="w-full max-w-sm">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-mint-600 mb-4">
                <i class="fas fa-dumbbell text-white text-lg"></i>
            </div>
            <h1 class="text-xl font-semibold text-white">Create account</h1>
            <p class="text-sm text-zinc-500 mt-1">Join the gym management platform</p>
        </div>
        
        <!-- Card -->
        <div class="bg-dark-950 border border-zinc-800 rounded-lg p-6">
            <?php if (!empty($errors['general'])): ?>
            <div class="mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded text-sm text-red-400">
                <?php echo e($errors['general']); ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <?php echo csrfField(); ?>
                
                <div class="mb-4">
                    <label for="username" class="block text-xs font-medium text-zinc-400 mb-1.5">Username</label>
                    <input type="text" id="username" name="username" 
                           class="w-full px-3 py-2 bg-dark-925 border <?php echo isset($errors['username']) ? 'border-red-500' : 'border-zinc-800'; ?> rounded text-sm text-white placeholder-zinc-600 focus:outline-none focus:border-mint-500"
                           placeholder="Choose a username"
                           value="<?php echo e($oldInput['username'] ?? ''); ?>"
                           autocomplete="username">
                    <?php if (isset($errors['username'])): ?>
                    <p class="mt-1 text-xs text-red-400"><?php echo e($errors['username']); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <label for="email" class="block text-xs font-medium text-zinc-400 mb-1.5">Email</label>
                    <input type="email" id="email" name="email" 
                           class="w-full px-3 py-2 bg-dark-925 border <?php echo isset($errors['email']) ? 'border-red-500' : 'border-zinc-800'; ?> rounded text-sm text-white placeholder-zinc-600 focus:outline-none focus:border-mint-500"
                           placeholder="Enter your email"
                           value="<?php echo e($oldInput['email'] ?? ''); ?>"
                           autocomplete="email">
                    <?php if (isset($errors['email'])): ?>
                    <p class="mt-1 text-xs text-red-400"><?php echo e($errors['email']); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="block text-xs font-medium text-zinc-400 mb-1.5">Password</label>
                    <input type="password" id="password" name="password" 
                           class="w-full px-3 py-2 bg-dark-925 border <?php echo isset($errors['password']) ? 'border-red-500' : 'border-zinc-800'; ?> rounded text-sm text-white placeholder-zinc-600 focus:outline-none focus:border-mint-500"
                           placeholder="Create a password"
                           autocomplete="new-password">
                    <?php if (isset($errors['password'])): ?>
                    <p class="mt-1 text-xs text-red-400"><?php echo e($errors['password']); ?></p>
                    <?php else: ?>
                    <p class="mt-1 text-xs text-zinc-600">At least 6 characters</p>
                    <?php endif; ?>
                </div>
                
                <div class="mb-5">
                    <label for="confirm_password" class="block text-xs font-medium text-zinc-400 mb-1.5">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           class="w-full px-3 py-2 bg-dark-925 border <?php echo isset($errors['confirm_password']) ? 'border-red-500' : 'border-zinc-800'; ?> rounded text-sm text-white placeholder-zinc-600 focus:outline-none focus:border-mint-500"
                           placeholder="Confirm your password"
                           autocomplete="new-password">
                    <?php if (isset($errors['confirm_password'])): ?>
                    <p class="mt-1 text-xs text-red-400"><?php echo e($errors['confirm_password']); ?></p>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="w-full py-2 px-4 bg-mint-600 hover:bg-mint-500 text-white text-sm font-medium rounded transition-colors">
                    Create Account
                </button>
            </form>
        </div>
        
        <!-- Footer -->
        <p class="text-center text-sm text-zinc-500 mt-4">
            Already have an account? <a href="<?php echo url('/login.php'); ?>" class="text-mint-500 hover:text-mint-400">Sign in</a>
        </p>
    </div>
</body>
</html>
