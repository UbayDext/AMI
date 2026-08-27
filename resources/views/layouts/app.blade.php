<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
        toggleTheme(event) {
            const button = event.currentTarget;
            const rect = button.getBoundingClientRect();
            const x = rect.left + rect.width / 2;
            const y = rect.top + rect.height / 2;
            const radius = Math.hypot(
                Math.max(x, window.innerWidth - x),
                Math.max(y, window.innerHeight - y)
            );

            document.documentElement.style.setProperty('--theme-wave-x', `${x}px`);
            document.documentElement.style.setProperty('--theme-wave-y', `${y}px`);
            document.documentElement.style.setProperty('--theme-wave-radius', `${radius}px`);

            const applyTheme = () => {
                this.darkMode = !this.darkMode;
                return this.$nextTick();
            };

            if (!document.startViewTransition || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                applyTheme();
                return;
            }

            document.startViewTransition(applyTheme);
        }
    }"
    x-init="$watch('darkMode', val => { localStorage.setItem('darkMode', val); window.dispatchEvent(new CustomEvent('theme-changed', { detail: val })); })"
    :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Prevent FOUC for Dark Mode -->
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

        <!-- Sidebar (Navigation) -->
        @include('layouts.navigation')

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-gray-100 dark:bg-gray-900">

            <!-- Mobile Header (Hamburger) -->
            <div class="sm:hidden bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between transition-colors">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 dark:text-gray-400 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="font-semibold text-lg text-gray-800 dark:text-gray-100">{{ config('app.name') }}</div>
                <div class="w-6"></div> <!-- Spacer for centering if needed -->
            </div>

            <!-- Page Heading -->
            @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow dark:shadow-gray-900/50 transition-colors">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endisset

            <!-- Scrollable Page Content -->
            <main class="flex-1 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
