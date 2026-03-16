<?php require_once 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Account Login - SwiftCapital</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="IupufGIaZ9FIXRhnUkmDWxjN739GCNieMCFWQnFZ">
    <meta name="robots" content="index, follow">
    <meta name="apple-mobile-web-app-title" content="SwiftCapital">
    <meta name="application-name" content="SwiftCapital">
    <meta name="description" content="Swift and Secure Money Transfer to any UK bank account will become a breeze with SwiftCapital.">
    <link rel="shortcut icon" href="assets/images/SWC%20Icon%20Dark.png">


    <!-- Tailwind CSS -->
    <script src="3.4.17"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#e5eaf0',
                            100: '#cdd6e1',
                            200: '#9bacc3',
                            300: '#6983a5',
                            400: '#375987',
                            500: '#002d62',
                            600: '#002858',
                            700: '#00244e',
                            800: '#001f44',
                            900: '#001b3b',
                        },
                        secondary: {
                            50: '#fcedef',
                            100: '#f9dade',
                            200: '#f3b5be',
                            300: '#ed909d',
                            400: '#e76b7e',
                            500: '#E21936',
                            600: '#cb1630',
                            700: '#b4142b',
                            800: '#9e1125',
                            900: '#870f20',
                        }
                    },
                    fontFamily: {
                        'sans': ['Lato', 'sans-serif'],
                        'secondary': ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer="" src="npm/alpinejs%403.x.x/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="lucide%400.564.0/dist/umd/lucide.min.js"></script>
    <script src="npm/lucide%40latest/dist/umd/lucide.min.js"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    <!-- CSS Variables -->
    <script>
        // Set CSS theme variables
        document.documentElement.style.setProperty('--primary-color', '#002d62');
        document.documentElement.style.setProperty('--primary-color-dark', '#001f44');
        document.documentElement.style.setProperty('--primary-color-light', '#375987');
        document.documentElement.style.setProperty('--secondary-color', '#E21936');
        document.documentElement.style.setProperty('--secondary-color-dark', '#9e1125');
        document.documentElement.style.setProperty('--secondary-color-light', '#ed909d');
        document.documentElement.style.setProperty('--text-color', '#101010');
        document.documentElement.style.setProperty('--bg-color', '#f9fafb');
        document.documentElement.style.setProperty('--card-bg-color', '#ffffff');
    </script>

    
<link rel="stylesheet" href="assets/css/auth.css">
    <!-- Web Application Manifest -->
<link rel="manifest" href="manifest.json">
<!-- Chrome for Android theme color -->
<meta name="theme-color" content="#000000">

<!-- Add to homescreen for Chrome on Android -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="application-name" content="PWA">
<link rel="icon" sizes="512x512" href="images/icons/icon-512x512.png">

<!-- Add to homescreen for Safari on iOS -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="PWA">
<link rel="apple-touch-icon" href="images/icons/icon-512x512.png">


<link href="images/icons/splash-640x1136.png" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
<link href="images/icons/splash-750x1334.png" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
<link href="images/icons/splash-1242x2208.png" media="(device-width: 621px) and (device-height: 1104px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image">
<link href="images/icons/splash-1125x2436.png" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image">
<link href="images/icons/splash-828x1792.png" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
<link href="images/icons/splash-1242x2688.png" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image">
<link href="images/icons/splash-1536x2048.png" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
<link href="images/icons/splash-1668x2224.png" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
<link href="images/icons/splash-1668x2388.png" media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
<link href="images/icons/splash-2048x2732.png" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">

<!-- Tile for Win8 -->
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/images/icons/icon-512x512.png">

<script type="text/javascript">
    // Initialize the service worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/serviceworker.js', {
            scope: '.'
        }).then(function (registration) {
            // Registration was successful
            console.log('Laravel PWA: ServiceWorker registration successful with scope: ', registration.scope);
        }, function (err) {
            // registration failed :(
            console.log('Laravel PWA: ServiceWorker registration failed: ', err);
        });
    }
</script><link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body class="font-sans bg-gray-50 text-gray-900 flex min-h-screen">
    <!-- Page Loader -->
    <div class="page-loading active">
        <div class="page-loading-inner">
            <div class="loading-container">
                <div class="loading-animation">
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="core"></div>
                </div>
                <div class="text">SwiftCapital</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="w-full">
        
<div class="flex flex-col lg:flex-row min-h-screen">
    <!-- Left Side - Branding & Illustration (Desktop Only) -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-600 to-primary-800 relative overflow-hidden">
        <!-- Animated Shapes -->
        <div class="absolute inset-0 overflow-hidden opacity-10">
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-white rounded-full mix-blend-overlay floating-slow"></div>
            <div class="absolute bottom-1/3 right-1/4 w-96 h-96 bg-white rounded-full mix-blend-overlay floating"></div>
            <div class="absolute top-2/3 left-1/3 w-40 h-40 bg-white rounded-full mix-blend-overlay floating-slower"></div>

            <!-- Grid pattern -->
            <div class="absolute inset-0" style="background-image: radial-gradient(rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 20px 20px;"></div>
        </div>

        <!-- Content -->
        <div class="relative flex flex-col justify-center items-center w-full h-full text-white p-12 z-10">
            <!-- Logo -->
            <a href="index.php" class="mb-6">
                <img src="assets/images/SWC%20Primary%20Logo%20Light.png" alt="Logo" class="h-16">
            </a>

            <!-- Title -->
            <h1 class="text-4xl font-extrabold mb-6 text-center">Institutional Client Access</h1>

            <!-- Description -->
            <p class="text-xl mb-8 max-w-md text-center text-white/80">
                Secure portal for the management of global capital, asset settlements, and strategic private advisory.
            </p>

            <!-- Features -->
            <div class="grid grid-cols-2 gap-6 w-full max-w-md">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="vault" class="h-5 w-5"></i>
                    </div>
                    <span>Capital Sovereignty</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="bar-chart-3" class="h-5 w-5"></i>
                    </div>
                    <span>Strategic Yield</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="globe-2" class="h-5 w-5"></i>
                    </div>
                    <span>Global Settlements</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="shield-check" class="h-5 w-5"></i>
                    </div>
                    <span>Board Governance</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-6 lg:p-12">
        <div class="w-full max-w-md">
            <!-- Mobile Logo -->
            <div class="lg:hidden text-center mb-8">
                <a href="index.php">
                    <img src="assets/images/SWC%20Primary%20Logo%20Dark.png" alt="Logo" class="h-12 mx-auto">
                </a>
            </div>

            <!-- Alerts -->
            
            
            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Card Header -->
                <div class="px-8 pt-8 pb-6 text-center">
                    <h2 class="text-2xl font-bold text-gray-900">Secure Panel Access</h2>
                    <p class="mt-2 text-sm text-gray-600">Enter your institutional credentials to authorize session initialization.</p>
                </div>

                <!-- Login Form -->
                <div class="px-8 pb-8">
                    <form method="POST" action="login-process.php">
                        <?php
                        if (isset($_SESSION['errors'])) {
                            foreach ($_SESSION['errors'] as $error) {
                                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">' . $error . '</div>';
                            }
                            unset($_SESSION['errors']);
                        }
                        if (isset($_SESSION['success_message'])) {
                            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">' . $_SESSION['success_message'] . '</div>';
                            unset($_SESSION['success_message']);
                        }
                        ?>
                        <input type="hidden" name="_token" value="IupufGIaZ9FIXRhnUkmDWxjN739GCNieMCFWQnFZ">
                        <!-- Email -->
                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                            <div class="input-wrapper">
                                <div class="relative">
                                    <div class="input-icon">
                                        <i data-lucide="mail" class="h-5 w-5"></i>
                                    </div>
                                    <input id="email" type="text" name="email" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Enter Username" required="" autocomplete="username">
                                </div>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-2">
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <a href="forgot-password.php" class="text-sm text-primary-600 hover:text-primary-500">
                                    Forgot Password?
                                </a>
                            </div>
                            <div class="input-wrapper">
                                <div class="relative">
                                    <div class="input-icon">
                                        <i data-lucide="lock" class="h-5 w-5"></i>
                                    </div>
                                    <input id="password" type="password" name="password" class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢" required="" autocomplete="current-password">
                                    <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 flex items-center pr-3 input-toggle">
                                        <i data-lucide="eye" id="show-password" class="h-5 w-5 text-gray-400"></i>
                                        <i data-lucide="eye-off" id="hide-password" class="h-5 w-5 text-gray-400 hidden"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="remember_me" class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50" checked="">
                                <span class="ml-2 text-sm text-gray-600">Stay signed in for 30 days</span>
                            </label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col space-y-4">
                            <button type="submit" class="w-full py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white font-black text-uppercase tracking-widest text-xs rounded-lg shadow transition duration-150 ease-in-out flex items-center justify-center">
                                <i data-lucide="key" class="h-4 w-4 mr-2"></i>
                                AUTHORIZE ACCESS
                            </button>

                            <a href="register.php" class="w-full py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-lg transition duration-150 ease-in-out flex items-center justify-center">
                                <i data-lucide="user-plus" class="h-5 w-5 mr-2"></i>
                                Not enrolled? Create Account
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Additional Links -->
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600">
                    By signing in, you agree to our
                    <a href="#" class="text-primary-600 hover:text-primary-500">Terms of Service</a> and
                    <a href="#" class="text-primary-600 hover:text-primary-500">Privacy Policy</a>
                </p>
            </div>
        </div>
    </div>
</div>

    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>

    <!-- Enhanced Page Loading Animation -->
    <script>
        window.onload = function() {
            const preloader = document.querySelector('.page-loading');

            // Add a slight delay to make loading animation more noticeable
            setTimeout(function() {
                preloader.classList.remove('active');
                setTimeout(function() {
                    preloader.remove();
                }, 500);
            }, 800);
        };
    </script>
    <script>

    </script>

  

    <!-- Additional Scripts -->
    <script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const showPasswordIcon = document.getElementById('show-password');
        const hidePasswordIcon = document.getElementById('hide-password');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            showPasswordIcon.classList.add('hidden');
            hidePasswordIcon.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            showPasswordIcon.classList.remove('hidden');
            hidePasswordIcon.classList.add('hidden');
        }
    }
</script>
        <div class="gtranslate_wrapper"></div>
<script>
    window.gtranslateSettings = {
        default_language: "en",
        alt_flags:{"en":"usa"},
        wrapper_selector: ".gtranslate_wrapper",
        flag_style: "3d",
    };
</script>
<script src="widgets/latest/float.js" defer=""></script>
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    </script>
</body>
</html>
