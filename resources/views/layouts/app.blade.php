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
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">

        <!-- Top Navigation -->
        @include('layouts.navigation')

        <!-- Main Content Wrapper -->
        <div class="flex min-h-0 flex-col bg-gray-100 dark:bg-gray-900">

            <!-- Page Heading -->
            @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow dark:shadow-gray-900/50 transition-colors">
                <div class="mx-auto w-full max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endisset

            <!-- Scrollable Page Content -->
            <main class="min-h-0 flex-1">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
