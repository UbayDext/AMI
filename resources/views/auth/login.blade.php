<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 dark:text-gray-100 antialiased">
    <div class="min-h-screen flex">

        <!-- Left Side: Branding / Background -->
        <div class="hidden lg:flex w-1/2 bg-gray-900 text-white flex-col justify-between p-12 relative overflow-hidden">
            <div class="relative z-10">
                <x-application-logo class="w-16 h-16 fill-current text-white mb-4" />
                <h1 class="text-4xl font-bold tracking-tight">Assessor &<br>Internal Quality Assurance</h1>
                <p class="mt-4 text-purple-200 text-lg">Platform Evaluasi & Monitoring SPMI</p>
            </div>

            <div class="relative z-10">
                <p class="text-sm text-gray-400 dark:text-gray-500">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>

            <!-- Abstract Background Shapes -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 rounded-full bg-purple-600 blur-3xl opacity-20"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-blue-600 blur-3xl opacity-20"></div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-white dark:bg-gray-800 px-5 py-8 sm:p-8">
            <div class="w-full max-w-md space-y-8">
                <div class="flex items-center justify-center gap-3 lg:hidden">
                    <span class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-white p-2 shadow-md ring-1 ring-gray-200 dark:ring-gray-700">
                        <x-application-logo class="h-full w-full object-contain" />
                    </span>
                    <span class="min-w-0">
                        <span class="block text-xs font-medium text-indigo-600 dark:text-indigo-400">Sistem Penjaminan Mutu Internal</span>
                        <span class="mt-1 block text-lg font-bold leading-tight text-gray-900 dark:text-white">STIT Hidayatunnajah Bekasi</span>
                    </span>
                </div>
                <div class="text-center lg:text-left">
                    <h2 class="mt-6 text-3xl font-bold text-gray-900 dark:text-gray-100">Welcome back</h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Please enter your details to sign in.</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                @if (session('error'))
                <div class="mb-4 p-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg flex items-start gap-2">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full p-3" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@company.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full p-3"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between mt-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember for 30 days') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-indigo-600 hover:text-indigo-500" href="{{ route('password.request') }}">
                            {{ __('Forgot password?') }}
                        </a>
                        @endif
                    </div>

                    <div class="mt-6">
                        <x-primary-button class="w-full justify-center py-3 text-base">
                            {{ __('Sign in') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
