@php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SensorsHub') - Learn Sensors. Build Projects. Share Ideas.</title>
    <link rel="icon" type="image/png" href="{{ asset('sensorshub_logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300 overflow-x-clip min-h-screen flex flex-col">
    <div id="loading-bar"></div>
    @php
        $homeRoute = 'home';
        if (auth()->check()) {
            if (auth()->user()->isAdministrator()) {
                $homeRoute = 'administrator.dashboard';
            } elseif (auth()->user()->isInstructor()) {
                $homeRoute = 'instructor.dashboard';
            } else {
                $homeRoute = 'dashboard.index';
            }
        }
        $isAdministrator = auth()->check() && auth()->user()->isAdministrator();
        $isInstructor = auth()->check() && auth()->user()->isInstructor();
        $hideNav = request()->is('email/verify*');
    @endphp

    <!-- Toast Notifications -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show"
            x-init="setTimeout(() => show = false, 4000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-6"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-6"
            class="fixed top-20 right-4 z-[9999] max-w-sm w-full">
            <div class="flex items-center gap-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-800 rounded-lg shadow p-4">
                <i class="fas fa-check-circle text-secondary text-base shrink-0"></i>
                <p class="flex-1 text-sm font-medium text-gray-900 dark:text-white">{{ session('success') }}</p>
                <button @click="show = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show"
            x-init="setTimeout(() => show = false, 4000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-6"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-6"
            class="fixed top-20 right-4 z-[9999] max-w-sm w-full">
            <div class="flex items-center gap-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-800 rounded-lg shadow p-4">
                <i class="fas fa-exclamation-circle text-red-500 text-base shrink-0"></i>
                <p class="flex-1 text-sm font-medium text-gray-900 dark:text-white">{{ session('error') }}</p>
                <button @click="show = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>
    @endif

    <style>
        @keyframes cursorBlink {
            0%, 49% { opacity: 1; }
            50%, 100% { opacity: 0; }
        }
        .cursor-blink {
            display: inline-block;
            width: 0.35ch;
            animation: cursorBlink 1.1s step-end infinite;
        }

    #loading-bar {
        position: fixed;
        top: 0;
        left: 0;
        height: 3px;
        width: 0%;
        background: linear-gradient(to right, #10b981, #059669);
        z-index: 99999;
        transition: width 0.25s ease, opacity 0.4s ease;
        opacity: 0;
        pointer-events: none;
    }
    #loading-bar.active {
        opacity: 1;
    }
    </style>

    <!-- Navigation -->
    @guest
    {{-- Top Nav for Guests --}}
    <nav class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between gap-3 h-16">
                <div class="flex items-center min-w-0">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 min-w-0">
                        <img src="{{ asset('sensorshub_logo.png') }}" alt="SensorsHub" class="h-7 w-7 sm:h-8 sm:w-8 object-contain shrink-0">
                        <span class="block text-xl sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white leading-tight">SensorsHub</span>
                    </a>
                </div>
                <div class="hidden xl:flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-base text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">Home</a>
                    <a href="{{ route('sensors.index') }}" class="text-base text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">Sensors</a>
                    <a href="{{ route('projects.index') }}" class="text-base text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">Projects</a>
                    <a href="{{ route('videos.index') }}" class="text-base text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">Tutorials</a>
                    <a href="{{ route('suggestions.community') }}" class="text-base text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">Community</a>
                    <a href="{{ route('shop.index') }}" class="text-base text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">Shop</a>
                    <a href="https://sensors-hub-simulator.vercel.app/" target="_blank" class="text-base text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">Simulation</a>
                    <div class="w-px h-5 bg-gray-200 dark:bg-gray-800"></div>
                    <a href="{{ route('login') }}" class="text-base text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">Login</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                        Register
                    </a>
                    <button id="darkModeToggle" class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition w-8 h-8 flex items-center justify-center">
                        <i class="fas fa-moon dark-icon-moon w-4 text-center"></i><i class="fas fa-sun dark-icon-sun w-4 text-center" style="display: none;"></i>
                    </button>
                </div>
                <div class="xl:hidden flex items-center">
                    <button id="mobileMenuButton" class="text-gray-700 dark:text-gray-300 p-2 -mr-2"><i class="fas fa-bars text-2xl"></i></button>
                </div>
            </div>
        </div>
        <div id="mobileMenu" class="hidden xl:hidden bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
            <div class="px-4 pt-2 pb-4 space-y-1">
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg"><i class="fas fa-home w-5"></i> Home</a>
                <a href="{{ route('sensors.index') }}" class="flex items-center gap-3 px-3 py-2 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg"><i class="fas fa-microchip w-5"></i> Sensors</a>
                <a href="{{ route('projects.index') }}" class="flex items-center gap-3 px-3 py-2 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg"><i class="fas fa-folder-open w-5"></i> Projects</a>
                <a href="{{ route('videos.index') }}" class="flex items-center gap-3 px-3 py-2 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg"><i class="fas fa-play-circle w-5"></i> Tutorials</a>
                <a href="{{ route('suggestions.community') }}" class="flex items-center gap-3 px-3 py-2 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg"><i class="fas fa-comments w-5"></i> Community</a>
                <a href="{{ route('shop.index') }}" class="flex items-center gap-3 px-3 py-2 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg"><i class="fas fa-store w-5"></i> Shop</a>
                <a href="https://sensors-hub-simulator.vercel.app/" target="_blank" class="flex items-center gap-3 px-3 py-2 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg"><i class="fas fa-flask w-5"></i> Simulation</a>
                <div class="border-t border-gray-200 dark:border-gray-800 my-2"></div>
                <a href="{{ route('login') }}" class="flex items-center gap-3 px-3 py-2 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg"><i class="fas fa-key w-5"></i> Login</a>
                <a href="{{ route('register') }}" class="flex items-center gap-3 px-3 py-2 text-base font-medium text-white dark:text-gray-900 bg-gray-900 dark:bg-white rounded-lg"><i class="fas fa-user-plus w-5 text-center shrink-0"></i> Register</a>
                <button id="mobileDarkModeToggle" class="flex items-center gap-3 w-full px-3 py-2 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-moon dark-icon-moon w-5 text-center shrink-0"></i><i class="fas fa-sun dark-icon-sun w-5 text-center shrink-0" style="display: none;"></i> Dark Mode
                </button>
            </div>
        </div>
    </nav>
    @endguest

    @auth
    @unless($hideNav)
    {{-- Top Nav for Logged-in Users (Mobile) + Sidebar for Desktop --}}
    
    {{-- Mobile Top Bar --}}
    <nav class="lg:hidden bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 fixed top-0 left-0 right-0 z-50">
        <div class="px-4">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route($homeRoute) }}" class="flex items-center gap-2.5">
                    <img src="{{ asset('sensorshub_logo.png') }}" alt="SensorsHub" class="h-8 w-8 object-contain shrink-0">
                    <span class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">SensorsHub</span>
                </a>
                <div class="flex items-center gap-1">
                    <button onclick="this.innerHTML = '<i class=\'fas fa-sync-alt text-lg fa-spin\'></i>'; setTimeout(() => window.location.reload(), 300);" class="text-gray-400 hover:text-gray-900 dark:hover:text-white p-2 transition" title="Refresh">
                        <i class="fas fa-sync-alt text-lg"></i>
                    </button>
                    <button id="mobileSidebarToggle" class="text-gray-700 dark:text-gray-300 p-2 -mr-2">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    {{-- Mobile Sidebar Overlay --}}
    <div id="mobileSidebarOverlay" class="hidden lg:hidden fixed inset-0 bg-black/50 z-40"></div>

    {{-- Sidebar (Desktop always visible, Mobile slides in) --}}
    <aside id="sidebar" class="fixed left-0 top-0 h-full w-60 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 z-40 flex flex-col overflow-y-auto
                -translate-x-full lg:translate-x-0 transition-transform duration-300">
        {{-- Logo (Desktop) --}}
        <div class="h-16 hidden lg:flex items-center gap-2.5 px-5 border-b border-gray-200 dark:border-gray-800 flex-shrink-0">
            <img src="{{ asset('sensorshub_logo.png') }}" alt="SensorsHub" class="h-7 w-7 object-contain shrink-0">
            <span class="text-lg font-semibold tracking-tight text-gray-900 dark:text-white truncate">SensorsHub</span>
        </div>

        {{-- Mobile sidebar header --}}
        <div class="h-16 lg:hidden flex items-center justify-between px-4 border-b border-gray-200 dark:border-gray-800 flex-shrink-0">
            <span class="text-base font-semibold tracking-tight text-gray-900 dark:text-white">Menu</span>
            <button id="mobileSidebarClose" class="text-gray-500 dark:text-gray-400 p-1">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        {{-- Role Badge --}}
        <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-800">
            <span class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400">
                {{ $isAdministrator ? 'Administrator' : ($isInstructor ? 'Instructor' : (auth()->user()->isUser() ? 'User' : 'Student')) }}
            </span>
        </div>

        {{-- Nav Links --}}
        <nav class="flex-1 px-2 py-3 space-y-0.5 overflow-y-auto">
            @if($isAdministrator)
                <a href="{{ route('administrator.dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('administrator.dashboard') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-tachometer-alt w-5 text-center shrink-0"></i><span>Dashboard</span></a>
                <a href="{{ route('administrator.analytics') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('administrator.analytics') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-chart-bar w-5 text-center shrink-0"></i><span>Analytics</span></a>
                <a href="{{ route('administrator.users.index') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('administrator.users.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-users w-5 text-center shrink-0"></i><span>Users</span></a>
                <a href="{{ route('administrator.suggestions.index') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('administrator.suggestions.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-lightbulb w-5 text-center shrink-0"></i><span>Suggestions</span></a>
                <a href="{{ route('administrator.sensors.index') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('administrator.sensors.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-microchip w-5 text-center shrink-0"></i><span>Sensors</span></a>
                <a href="{{ route('administrator.projects.index') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('administrator.projects.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-project-diagram w-5 text-center shrink-0"></i><span>Projects</span></a>
                <a href="{{ route('administrator.products.index') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('administrator.products.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-shopping-cart w-5 text-center shrink-0"></i><span>Products</span></a>
                <a href="{{ route('administrator.videos.index') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('administrator.videos.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-video w-5 text-center shrink-0"></i><span>Videos</span></a>
                <a href="{{ route('administrator.logs') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('administrator.logs') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-history w-5 text-center shrink-0"></i><span>Activity Logs</span></a>
                <a href="{{ route('administrator.backup') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('administrator.backup*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-database w-5 text-center shrink-0"></i><span>Backup</span></a>
                <a href="{{ route('administrator.feedback.index') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('administrator.feedback.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-comment-dots w-5 text-center shrink-0"></i><span>Feedback</span></a>
            @elseif($isInstructor)
                <a href="{{ route('instructor.dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('instructor.dashboard') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-tachometer-alt w-5 text-center shrink-0"></i><span>Dashboard</span></a>
                <a href="{{ route('instructor.classes.index') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('instructor.classes.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-chalkboard w-5 text-center shrink-0"></i><span>Classes</span></a>
                <a href="{{ route('suggestions.community') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('suggestions.community') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-comments w-5 text-center shrink-0"></i><span>Community</span></a>
                <a href="{{ route('shop.index') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('shop.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-store w-5 text-center shrink-0"></i><span>Shop</span></a>
            @else
                <a href="{{ route('dashboard.index') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('dashboard.index') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-home w-5 text-center shrink-0"></i><span>Dashboard</span></a>
                <a href="{{ route('sensors.index') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('sensors.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-microchip w-5 text-center shrink-0"></i><span>Sensors</span></a>
                <a href="{{ route('projects.index') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('projects.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-project-diagram w-5 text-center shrink-0"></i><span>Projects</span></a>
                <a href="{{ route('videos.index') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('videos.index') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-play-circle w-5 text-center shrink-0"></i><span>Tutorials</span></a>
                <a href="{{ route('dashboard.classes.index') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('dashboard.classes.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-chalkboard w-5 text-center shrink-0"></i><span>Classroom</span></a>
                <a href="{{ route('suggestions.community') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('suggestions.community') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-comments w-5 text-center shrink-0"></i><span>Community</span></a>
                <a href="{{ route('shop.index') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('shop.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }}"><i class="fas fa-store w-5 text-center shrink-0"></i><span>Shop</span></a>
            @endif

            <a href="https://sensors-hub-simulator.vercel.app/" target="_blank" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white"><i class="fas fa-flask w-5 text-center shrink-0"></i><span>Simulation</span></a>
        </nav>

        {{-- Bottom section --}}
        <div class="border-t border-gray-200 dark:border-gray-800 px-2 py-2 space-y-0.5 flex-shrink-0">
            @php 
                $unreadCount = cache()->remember('unread_notifications_'.auth()->id(), 15, function() {
                    return auth()->user()->notifications()->where('is_read', false)->count();
                });
            @endphp
            <a href="{{ route('notifications.index') }}" class="flex items-center gap-3 px-3 py-2 text-base rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white relative">
                <i class="fas fa-bell w-5 text-center shrink-0"></i><span>Notifications</span>
                @if($unreadCount > 0)<span class="ml-auto bg-red-500 text-white text-[10px] rounded-full w-5 h-5 flex items-center justify-center font-medium">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>@endif
            </a>

            @if(!$isAdministrator)
                <a href="{{ $isInstructor ? route('instructor.feedback.create') : route('dashboard.feedback.create') }}"
                   class="flex items-center gap-3 px-3 py-2 text-base rounded-lg {{ request()->routeIs('*.feedback.create') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white' }} transition">
                    <i class="fas fa-comment-dots w-5 text-center shrink-0"></i><span>Send feedback</span>
                </a>
            @endif

            <button id="sidebarDarkModeToggle" class="flex items-center gap-3 w-full px-3 py-2 text-base rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white transition">
                <i class="fas fa-moon dark-icon-moon w-5 text-center shrink-0"></i><i class="fas fa-sun dark-icon-sun w-5 text-center shrink-0" style="display: none;"></i><span>Dark Mode</span>
            </button>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 w-full px-3 py-2 text-base rounded-lg text-gray-600 dark:text-gray-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 transition">
                    <i class="fas fa-sign-out-alt w-5 text-center shrink-0"></i><span>Logout</span>
                </button>
            </form>
        </div>
    </aside>
    @endunless
    @endauth

    <!-- Main Content -->
    <main class="flex-1 @auth @unless($hideNav) lg:ml-60 @endunless pt-16 lg:pt-0 @else pt-16 @endauth transition-all duration-300">
    @yield('content')
    </main>

        <!-- Footer -->
    <footer class="bg-gray-950 border-t border-gray-900 @auth @unless($hideNav) lg:ml-60 @endunless @endauth">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

            {{-- Top: brand + columns --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8">

                {{-- Brand --}}
                <div class="lg:col-span-5">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 mb-4">
                        <img src="{{ asset('sensorshub_logo.png') }}" alt="SensorsHub" class="h-7 w-7 object-contain">
                        <span class="text-lg font-semibold tracking-tight text-white">
                            SensorsHub
                        </span>
                    </a>
                    <p class="text-sm text-gray-400 max-w-xs leading-relaxed">
                        Learn sensors. Build real circuits. A hands-on workspace for students and makers.
                    </p>
                </div>

                {{-- Quick links --}}
                <div class="lg:col-span-2">
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 mb-4">
                        Explore
                    </p>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route($homeRoute) }}" class="text-sm text-gray-400 hover:text-white transition">Home</a></li>
                        <li><a href="{{ route('sensors.index') }}" class="text-sm text-gray-400 hover:text-white transition">Sensors</a></li>
                        <li><a href="{{ route('projects.index') }}" class="text-sm text-gray-400 hover:text-white transition">Projects</a></li>
                        <li><a href="{{ route('videos.index') }}" class="text-sm text-gray-400 hover:text-white transition">Tutorials</a></li>
                    </ul>
                </div>

                {{-- Resources --}}
                <div class="lg:col-span-2">
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 mb-4">
                        Resources
                    </p>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('suggestions.community') }}" class="text-sm text-gray-400 hover:text-white transition">Community</a></li>
                        <li><a href="{{ route('shop.index') }}" class="text-sm text-gray-400 hover:text-white transition">Shop</a></li>
                        <li><a href="https://sensors-hub-simulator.vercel.app/" target="_blank" class="text-sm text-gray-400 hover:text-white transition">Simulation</a></li>
                    </ul>
                </div>

                {{-- Connect --}}
                <div class="lg:col-span-3">
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 mb-4">
                        Connect
                    </p>
                    <div class="flex items-center gap-2">
                        <a href="#" class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-800 text-gray-500 hover:text-white hover:border-gray-600 transition" aria-label="YouTube">
                            <i class="fab fa-youtube text-sm"></i>
                        </a>
                        <a href="#" class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-800 text-gray-500 hover:text-white hover:border-gray-600 transition" aria-label="Facebook">
                            <i class="fab fa-facebook-f text-sm"></i>
                        </a>
                        <a href="#" class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-800 text-gray-500 hover:text-white hover:border-gray-600 transition" aria-label="Discord">
                            <i class="fab fa-discord text-sm"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Bottom: copyright --}}
            <div class="mt-12 pt-8 border-t border-gray-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <p class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} SensorsHub. All rights reserved.
                </p>
                <p class="text-xs text-gray-500 font-mono">
                    <span class="text-emerald-500">$</span> your next project starts with<span class="cursor-blink text-emerald-500">▎</span>
                </p>
            </div>
        </div>
    </footer>

    <script>
        // ===== Dark Mode Toggles =====
        const html = document.documentElement;
        if (localStorage.getItem('darkMode') === 'true') html.classList.add('dark');

        function updateDarkIcons() {
            const isDark = html.classList.contains('dark');
            document.querySelectorAll('.dark-icon-moon').forEach(el => {
                el.style.display = isDark ? 'none' : '';
            });
            document.querySelectorAll('.dark-icon-sun').forEach(el => {
                el.style.display = isDark ? '' : 'none';
            });
        }

        function toggleDarkMode() {
            html.classList.toggle('dark');
            localStorage.setItem('darkMode', html.classList.contains('dark'));
            updateDarkIcons();
        }

        // Run once on load to set the correct icon
        updateDarkIcons();

        document.getElementById('darkModeToggle')?.addEventListener('click', toggleDarkMode);
        document.getElementById('mobileDarkModeToggle')?.addEventListener('click', toggleDarkMode);
        document.getElementById('sidebarDarkModeToggle')?.addEventListener('click', toggleDarkMode);

        // ===== Guest Mobile Menu =====
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');

        mobileMenuButton?.addEventListener('click', function() {
            const isOpen = !mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden');
            this.innerHTML = isOpen 
                ? '<i class="fas fa-bars text-2xl"></i>' 
                : '<i class="fas fa-times text-2xl"></i>';
        });

        mobileMenu?.querySelectorAll('a, button').forEach(el => {
            el.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                mobileMenuButton.innerHTML = '<i class="fas fa-bars text-2xl"></i>';
            });
        });

        // ===== Auth Mobile Sidebar =====
        const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
        const mobileSidebarClose = document.getElementById('mobileSidebarClose');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobileSidebarOverlay');

        let sidebarOpen = false;

        function openSidebar() {
            if (!sidebar) return;
            sidebar.classList.add('translate-x-0');
            sidebar.classList.remove('-translate-x-full');
            overlay?.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            sidebarOpen = true;
            updateToggleIcon();
        }

        function closeSidebar() {
            if (!sidebar) return;
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');
            overlay?.classList.add('hidden');
            document.body.style.overflow = '';
            sidebarOpen = false;
            updateToggleIcon();
        }

        function toggleSidebar() {
            if (sidebarOpen) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }

        function updateToggleIcon() {
            if (!mobileSidebarToggle) return;
            if (sidebarOpen) {
                mobileSidebarToggle.innerHTML = '<i class="fas fa-times text-2xl"></i>';
            } else {
                mobileSidebarToggle.innerHTML = '<i class="fas fa-bars text-2xl"></i>';
            }
        }

        mobileSidebarToggle?.addEventListener('click', toggleSidebar);
        mobileSidebarClose?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);

        sidebar?.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    closeSidebar();
                }
            });
        });

        window.addEventListener('resize', () => {
            if (sidebar && window.innerWidth >= 1024) {
                closeSidebar();
            }
        });
    </script>


    <script>
        (function () {
            const bar = document.getElementById('loading-bar');
            if (!bar) return;

            let progress = 0;
            let timer = null;
            let visible = false;

            function start() {
                if (visible) return;
                visible = true;
                progress = 0;
                bar.classList.add('active');
                bar.style.width = '0%';

                timer = setInterval(() => {
                    progress += Math.random() * 10;
                    if (progress >= 95) progress = 95;
                    bar.style.width = progress + '%';
                }, 200);

                setTimeout(() => { if (visible) done(); }, 8000);
            }

            function done() {
                if (!visible) return;
                clearInterval(timer);
                bar.style.width = '100%';

                setTimeout(() => {
                    bar.classList.remove('active');
                    bar.style.width = '0%';
                    progress = 0;
                    visible = false;
                }, 400);
            }

            window.addEventListener('beforeunload', start);

            document.addEventListener('click', function (e) {
                const link = e.target.closest('a');
                if (!link) return;
                const href = link.getAttribute('href');
                if (!href) return;
                if (href.startsWith('#') || href.startsWith('javascript:')) return;
                if (link.target === '_blank') return;
                start();
            });

            document.addEventListener('submit', function () {
                start();
            });

            window.addEventListener('pageshow', done);
            window.addEventListener('load', done);
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form').forEach(function(form) {
                if (form.hasAttribute('x-data')) return;
                form.addEventListener('submit', function(e) {
                    var onsubmit = form.getAttribute('onsubmit');
                    if (onsubmit && onsubmit.includes('fileSizeError')) return;
                    if (onsubmit && onsubmit.includes('confirm')) return;

                    var button = form.querySelector('button[type="submit"]');
                    if (!button || button.disabled) return;

                    // Pick a label based on the button's existing text
                    var text = (button.textContent || '').trim().toLowerCase();
                    // Fallback: use title attribute for icon-only buttons
                    if (!text && button.getAttribute('title')) {
                        text = button.getAttribute('title').toLowerCase();
                    }

                    var label = 'Saving';
                    if (text.includes('search') || text.includes('filter')) label = 'Searching';
                    else if (text.includes('logout') || text.includes('log out')) label = 'Logging out';
                    else if (text.includes('login') || text.includes('log in') || text.includes('sign in')) label = 'Logging in';
                    else if (text.includes('register') || text.includes('sign up') || text.includes('signup')) label = 'Creating account';
                    else if (text.includes('verify')) label = 'Verifying';
                    else if (text.includes('resend')) label = 'Sending';
                    else if (text.includes('submit') || text.includes('post') || text.includes('send')) label = 'Submitting';
                    else if (text.includes('import')) label = 'Importing';
                    else if (text.includes('create') || text.includes('add')) label = 'Creating';
                    else if (text.includes('update') || text.includes('edit')) label = 'Updating';
                    else if (text.includes('delete') || text.includes('remove')) label = 'Deleting';
                    else if (text.includes('clear')) label = 'Clearing';
                    else if (text.includes('unban')) label = 'Unbanning';
                    else if (text.includes('reject')) label = 'Rejecting';
                    else if (text.includes('mark') || text.includes('read')) label = 'Updating';
                    else if (text.includes('join')) label = 'Joining';
                    else if (text.includes('grade')) label = 'Saving';
                    else if (text.includes('approve')) label = 'Approving';
                    else if (text.includes('upload')) label = 'Uploading';

                    button.disabled = true;
                    button.classList.add('opacity-70', 'pointer-events-none');
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>' + label + '...</span>';
                });
            });
        });
    </script>

    <script>
        function markAsRead(id) {
            fetch('/notifications/' + id + '/read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                }
            });
        }
    </script>

    @stack('scripts')

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        @auth
            @if(request()->is('email/verify*'))
                const audioSrc = null;
                const storageKey = null;
            @elseif(auth()->user()->isAdministrator())
                const audioSrc = "{{ asset('audio/welcome-administrator.mp3') }}";
                const storageKey = 'welcome_administrator_played';
            @elseif(auth()->user()->isInstructor())
                const audioSrc = "{{ asset('audio/welcome-instructor.mp3') }}";
                const storageKey = 'welcome_instructor_played';
            @else
                const audioSrc = "{{ asset('audio/welcome-back.mp3') }}";
                const storageKey = 'welcome_back_played';
            @endif
        @else
            const audioSrc = "{{ asset('audio/welcome-guest.mp3') }}";
            const storageKey = 'welcome_guest_played';
        @endauth

        if (!audioSrc) return;
        const audio = new Audio(audioSrc);
        audio.volume = 0.8;

        let hasPlayed = false;

        function cleanup() {
            document.removeEventListener('click', playWelcome);
            document.removeEventListener('keydown', playWelcome);
            document.removeEventListener('touchstart', playWelcome);
        }

        function playWelcome() {
            if (hasPlayed) return;
            hasPlayed = true;
            audio.currentTime = 0;
            audio.play()
                .then(() => {
                    sessionStorage.setItem(storageKey, 'true');
                    cleanup();
                })
                .catch(err => {
                    hasPlayed = false;
                });
        }

        const style = document.createElement('style');
        style.textContent = `
            .audio-btn {
                position:fixed; bottom:16px; right:16px; z-index:9999;
                background:rgba(16,185,129,0.85); backdrop-filter:blur(8px);
                color:white; width:40px; height:40px; border-radius:50%;
                border:1px solid rgba(255,255,255,0.15); cursor:pointer;
                box-shadow:0 4px 12px rgba(16,185,129,0.35);
                display:flex; align-items:center; justify-content:center;
                font-size:14px; transition:all 0.3s ease; opacity:0.75;
            }
            .audio-btn:hover, .audio-btn:focus { opacity:1; transform:scale(1.1); box-shadow:0 8px 20px rgba(16,185,129,0.5); }
            .audio-btn.playing { animation: audioPulse 2s infinite; opacity:1; }
            .audio-btn.muted {
                background:rgba(75,85,99,0.85);
                box-shadow:0 4px 12px rgba(75,85,99,0.35);
                opacity:0.55;
            }
            .audio-btn.muted:hover, .audio-btn.muted:focus {
                opacity:1;
                box-shadow:0 8px 20px rgba(75,85,99,0.5);
            }
            @keyframes audioPulse {
                0%, 100% { box-shadow: 0 4px 12px rgba(16,185,129,0.4); }
                50% { box-shadow: 0 4px 20px rgba(16,185,129,0.7), 0 0 0 6px rgba(16,185,129,0.1); }
            }
            @media (max-width: 640px) {
                .audio-btn { width:34px; height:34px; bottom:12px; right:12px; font-size:12px; opacity:0.7; }
                .audio-btn.muted { opacity:0.5; }
            }
        `;
        document.head.appendChild(style);

        const btn = document.createElement('button');
        btn.className = 'audio-btn';
        btn.innerHTML = '<i class="fas fa-volume-up"></i>';
        btn.title = "Play Welcome Message";

        function startPulse() {
            btn.classList.add('playing');
            btn.classList.remove('muted');
            btn.querySelector('i').className = 'fas fa-volume-up';
            btn.title = "Mute";
        }
        function stopPulse() {
            btn.classList.remove('playing');
            btn.classList.add('muted');
            btn.querySelector('i').className = 'fas fa-volume-mute';
            btn.title = "Play welcome message";
        }

        btn.addEventListener('click', function () {
            if (audio.paused) { audio.currentTime = 0; audio.play(); }
            else { audio.pause(); audio.currentTime = 0; }
        });

        audio.addEventListener('play', startPulse);
        audio.addEventListener('pause', stopPulse);
        audio.addEventListener('ended', stopPulse);
        btn.classList.add('muted');
        btn.querySelector('i').className = 'fas fa-volume-mute';
        document.body.appendChild(btn);

        if (!sessionStorage.getItem(storageKey)) {
            audio.play()
                .then(() => { hasPlayed = true; sessionStorage.setItem(storageKey, 'true'); })
                .catch(() => {
                    document.addEventListener('click', playWelcome, { once: true });
                    document.addEventListener('keydown', playWelcome, { once: true });
                    document.addEventListener('touchstart', playWelcome, { once: true });
                });
        }
    });
    </script>

    {{-- ========================== AI Chatbot ========================== --}}
    @auth
    @if(auth()->user()->role === 'student' || auth()->user()->role === 'instructor')
    @unless(request()->routeIs('dashboard.classes.quizzes.show') || request()->routeIs('dashboard.classes.quizzes.submit') || request()->routeIs('instructor.classes.quizzes.show'))
    <style>
    .chat-bubble {
            position:fixed; bottom:80px; right:16px; z-index:9998;
            width:40px; height:40px; border-radius:50%;
            background:rgba(16,185,129,0.85); backdrop-filter:blur(8px);
            color:white; display:flex; align-items:center; justify-content:center;
            cursor:pointer; font-size:16px;
            box-shadow:0 4px 12px rgba(16,185,129,0.35);
            transition:all 0.3s ease; border:1px solid rgba(255,255,255,0.15);
            opacity:0.85;
        }
        .chat-bubble:hover { opacity:1; transform:scale(1.1); box-shadow:0 8px 20px rgba(16,185,129,0.5); }
        .chat-window {
            position:fixed; bottom:130px; right:16px; z-index:9999;
            width:360px; max-height:480px;
            background:white; border-radius:16px;
            box-shadow:0 12px 50px rgba(0,0,0,0.18);
            border:1px solid #E5E7EB;
            display:flex; flex-direction:column; overflow:hidden;
        }
        .dark .chat-window { background:#1F2937; border-color:#374151; }
        .chat-header {
            padding:14px 18px; font-weight:600; font-size:14px;
            color:#1F2937; border-bottom:1px solid #F3F4F6;
            display:flex; align-items:center; gap:10px;
            background:#FAFAFA;
        }
        .dark .chat-header { color:#F9FAFB; border-color:#374151; background:#111827; }
        .chat-header .ai-avatar {
            width:32px; height:32px; border-radius:50%;
            background:linear-gradient(135deg,#10B981,#059669);
            display:flex; align-items:center; justify-content:center;
            font-size:14px; flex-shrink:0;
        }
        .chat-header .status-dot {
            width:7px; height:7px; background:#10B981; border-radius:50%;
            flex-shrink:0;
        }
        .chat-messages {
            flex:1; overflow-y:auto; padding:16px;
            max-height:320px; font-size:13px;
            display:flex; flex-direction:column; gap:10px;
            background:#F9FAFB;
        }
        .dark .chat-messages { background:#1F2937; }
        .chat-message-wrapper { display:flex; gap:8px; align-items:flex-start; }
        .chat-message-wrapper.user { justify-content:flex-end; }
        .chat-ai-avatar {
            width:28px; height:28px; border-radius:50%;
            background:linear-gradient(135deg,#10B981,#059669);
            display:flex; align-items:center; justify-content:center;
            font-size:12px; flex-shrink:0;
        }
        .chat-user {
            background:linear-gradient(135deg,#10B981,#059669); color:white;
            padding:10px 14px; border-radius:18px 18px 4px 18px;
            max-width:80%; font-size:13px; line-height:1.5;
            box-shadow:0 2px 8px rgba(16,185,129,0.25);
        }
        .chat-ai {
            background:white; color:#1F2937;
            padding:10px 14px; border-radius:18px 18px 18px 4px;
            max-width:80%; font-size:13px; line-height:1.5;
            border:1px solid #E5E7EB;
        }
        .dark .chat-ai { background:#374151; color:#E5E7EB; border-color:#4B5563; }
        .typing-dots { display:flex; gap:4px; padding:12px 16px; }
        .typing-dots span {
            width:7px; height:7px; background:#9CA3AF; border-radius:50%;
            animation:typingBounce 1.4s infinite ease-in-out;
        }
        .typing-dots span:nth-child(2) { animation-delay:0.2s; }
        .typing-dots span:nth-child(3) { animation-delay:0.4s; }
        @keyframes typingBounce {
            0%,60%,100% { transform:translateY(0); }
            30% { transform:translateY(-8px); }
        }
        .chat-input-area {
            padding:12px 16px; border-top:1px solid #F3F4F6;
            display:flex; gap:8px; align-items:center; background:white;
        }
        .dark .chat-input-area { border-color:#374151; background:#1F2937; }
        .chat-input-area input {
            flex:1; padding:10px 16px;
            border:1px solid #E5E7EB; border-radius:24px;
            font-size:13px; outline:none; background:#F9FAFB;
            transition:all 0.2s;
        }
        .chat-input-area input:focus { border-color:#10B981; box-shadow:0 0 0 3px rgba(16,185,129,0.15); }
        .dark .chat-input-area input { background:#374151; border-color:#4B5563; color:#E5E7EB; }
        .chat-input-area button {
            width:38px; height:38px; border-radius:50%;
            background:linear-gradient(135deg,#10B981,#059669); color:white;
            border:none; cursor:pointer; font-size:14px;
            display:flex; align-items:center; justify-content:center;
            transition:all 0.2s; flex-shrink:0;
        }
        .chat-input-area button:hover { transform:scale(1.05); box-shadow:0 4px 12px rgba(16,185,129,0.4); }
        .chat-input-area button:disabled { opacity:0.5; cursor:not-allowed; transform:none; }
        @media (max-width:640px) {
            .chat-window { width:92vw; right:4vw; bottom:120px; max-height:60vh; }
            .chat-bubble { bottom:60px; right:12px; width:34px; height:34px; font-size:14px; }
            .chat-messages { max-height:40vh; }
        }
    </style>

    <div x-data="chatBot()">
        <div class="chat-bubble" @click="open = !open">
            <span x-show="!open"><i class="fas fa-robot"></i></span>
            <span x-show="open"><i class="fas fa-times"></i></span>
        </div>
        <div class="chat-window" x-show="open" x-transition.opacity.scale.origin.bottom.duration.200>
            <div class="chat-header">
                <div class="ai-avatar">🤖</div>
                <div class="flex-1">
                    <p class="text-sm font-semibold">SensorsHub AI</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Learning Assistant</p>
                </div>
                <span class="status-dot"></span>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-lg leading-none">&times;</button>
            </div>

            <div class="chat-messages" x-ref="messages">
                <template x-for="(msg, i) in messages">
                    <div>
                        <div class="chat-message-wrapper user" x-show="msg.role === 'user'">
                            <div class="chat-user" x-text="msg.text"></div>
                        </div>
                        <div class="chat-message-wrapper" x-show="msg.role === 'ai'">
                            <div class="chat-ai-avatar">🤖</div>
                            <div class="chat-ai" x-html="msg.text.replace(/\n/g,'<br>')"></div>
                        </div>
                    </div>
                </template>
                <div x-show="loading" class="chat-message-wrapper">
                    <div class="chat-ai-avatar">🤖</div>
                    <div class="typing-dots">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            </div>

            <div class="chat-input-area">
                <input type="text" x-model="input" @keyup.enter="sendMessage()" placeholder="Ask about sensors, Arduino..." :disabled="loading">
                <button @click="sendMessage()" :disabled="loading">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        function chatBot() {
            return {
                open: false,
                messages: [{ role: 'ai', text: "👋 Hey! Ask me anything about sensors, microcontrollers, or electronics." }],
                input: '',
                loading: false,
                sendMessage() {
                    const content = this.input?.trim();
                    if (!content || this.loading) return;
                    this.messages.push({ role: 'user', text: content });
                    this.input = '';
                    this.loading = true;
                    this.$nextTick(() => this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight);
                    fetch('/api/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ message: content })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.messages.push({ role: 'ai', text: data.reply });
                        this.loading = false;
                        this.$nextTick(() => this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight);
                    })
                    .catch(() => {
                        this.messages.push({ role: 'ai', text: 'Sorry, something went wrong!' });
                        this.loading = false;
                    });
                }
            }
        }
    </script>
    @endunless
        @endif
    @endauth

</body>
</html>