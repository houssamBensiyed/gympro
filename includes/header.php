<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Header Template - Ultra-Sharp Modern Design
 * ============================================
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth/functions.php';

// Require authentication for protected pages
if (!isset($isAuthPage) || !$isAuthPage) {
    requireAuth();
}

// Set default page title
$pageTitle = $pageTitle ?? 'Dashboard';
$currentPage = $currentPage ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Gym Management Platform - Manage courses and equipment">
    
    <title><?php echo e($pageTitle); ?> | <?php echo e(APP_NAME); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo asset('images/favicon.png'); ?>">
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                    },
                    colors: {
                        // Pure dark (almost OLED black)
                        dark: {
                            50: '#f7f7f8',
                            100: '#ececee',
                            200: '#d5d5d9',
                            300: '#b1b1b8',
                            400: '#868691',
                            500: '#6b6b76',
                            600: '#595962',
                            700: '#4a4a51',
                            800: '#3f3f45',
                            900: '#27272a',
                            925: '#1c1c1f',
                            950: '#111113',
                            975: '#0a0a0b',
                        },
                        // Single accent - Emerald/Teal (modern, professional)
                        mint: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    },
                    borderRadius: {
                        'none': '0',
                        'sm': '0.25rem',
                        'DEFAULT': '0.375rem',
                        'md': '0.5rem',
                        'lg': '0.625rem',
                    },
                    boxShadow: {
                        'glow': '0 0 20px rgba(34, 197, 94, 0.15)',
                        'card': '0 1px 3px rgba(0,0,0,0.5)',
                    }
                }
            }
        }
    </script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    
    <!-- Ultra-Sharp Styles -->
    <style>
        * {
            letter-spacing: -0.01em;
        }
        
        body {
            background: #0a0a0b;
            color: #ececee;
            font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11';
        }
        
        h1, h2, h3, h4, h5, h6 {
            letter-spacing: -0.025em;
            font-weight: 600;
        }
        
        /* Sharp scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #111113;
        }
        ::-webkit-scrollbar-thumb {
            background: #27272a;
            border-radius: 0;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #3f3f45;
        }
        
        /* Clean focus */
        input:focus, select:focus, textarea:focus, button:focus {
            outline: 2px solid #22c55e;
            outline-offset: 1px;
        }
        
        /* Table precision */
        table {
            border-collapse: separate;
            border-spacing: 0;
        }
        
        /* Sharp cards */
        .card {
            background: #111113;
            border: 1px solid #1c1c1f;
        }
        
        .card:hover {
            border-color: #27272a;
        }
        
        /* No transitions for sharp feel - optional, remove if you want smooth */
        /* *, *::before, *::after { transition: none !important; } */
    </style>
</head>
<body class="font-sans antialiased">
    <!-- MAIN WRAPPER -->
    <div class="flex min-h-screen" id="app-wrapper">
        
        <!-- SIDEBAR -->
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <!-- MAIN CONTENT -->
        <main class="flex-1 ml-56" id="main-content">
            
            <!-- TOP BAR -->
            <header class="sticky top-0 z-40 bg-dark-975/95 backdrop-blur-sm border-b border-dark-900">
                <div class="flex items-center justify-between px-6 h-14">
                    <div>
                        <h1 class="text-base font-semibold text-dark-50 tracking-tight"><?php echo e($pageTitle); ?></h1>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-xs text-dark-400"><?php echo date('M j, Y'); ?></span>
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded bg-mint-600 flex items-center justify-center text-white text-xs font-medium">
                                <?php echo strtoupper(substr(getCurrentUsername() ?? 'G', 0, 1)); ?>
                            </div>
                            <span class="text-sm text-dark-200"><?php echo e(getCurrentUsername() ?? 'Guest'); ?></span>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- FLASH MESSAGES -->
            <?php include __DIR__ . '/alerts.php'; ?>
            
            <!-- PAGE CONTENT -->
            <div class="p-6">