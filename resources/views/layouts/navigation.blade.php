<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed z-30 inset-y-0 left-0 w-64 transition-transform duration-300 transform bg-white border-r border-gray-200 sm:translate-x-0 sm:static sm:inset-auto">

    <!-- Logo -->
    <div class="flex items-center justify-center h-16 bg-white border-b border-gray-200">
        <a href="{{ route('dashboard') }}" class="flex items-center">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
            <span class="ml-2 text-xl font-bold text-gray-800">{{ config('app.name') }}</span>
        </a>
    </div>

    <!-- Navigation Links -->
    <div class="overflow-y-auto overflow-x-hidden flex-grow px-4 py-4 space-y-1">

        <!-- Dashboard -->
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <svg class="mr-3 h-6 w-6 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            {{ __('Dashboard') }}
            </x-nav-link>

            <!-- Admin -->
            @if(auth()->user()->can('manage assessments') || auth()->user()->can('manage preparations') || auth()->user()->can('manage questions') || auth()->user()->can('manage users'))
            <div class="mt-8 mb-2 px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Admin
            </div>

            @can('manage assessments')
            <x-sidebar-link :href="route('admin.assessments.index')" :active="request()->routeIs('admin.assessments.*')">
                <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.assessments.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                {{ __('Assessments') }}
            </x-sidebar-link>
            @endcan

            @can('manage preparations')
            <x-sidebar-link :href="route('admin.preparations.index')" :active="request()->routeIs('admin.preparations.*')">
                <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.preparations.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                {{ __('Preparations') }}
            </x-sidebar-link>
            @endcan

            @can('manage questions')
            <x-sidebar-link :href="route('admin.questions.index')" :active="request()->routeIs('admin.questions.*')">
                <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.questions.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ __('Soal') }}
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.question-categories.index')" :active="request()->routeIs('admin.question-categories.*')">
                <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.question-categories.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                {{ __('Kategori Soal') }}
            </x-sidebar-link>
            @endcan

            @can('manage assessments')
            <x-sidebar-link :href="route('admin.auditor-decrees.index')" :active="request()->routeIs('admin.auditor-decrees.*')">
                <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.auditor-decrees.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ __('SK Auditor') }}
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.accreditation-years.index')" :active="request()->routeIs('admin.accreditation-years.*')">
                <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.accreditation-years.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ __('Tahun Akreditasi') }}
            </x-sidebar-link>
            @endcan

            @can('manage users')
            <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.users.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                {{ __('Users') }}
            </x-sidebar-link>
            @endcan
            @endif

            <!-- Assessor -->
            @if(auth()->user()->hasRole('asesor'))
            <div class="mt-8 mb-2 px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Asesor
            </div>
            <x-sidebar-link :href="route('assessor.assessments.index')" :active="request()->routeIs('assessor.*')">
                <svg class="mr-3 h-6 w-6 {{ request()->routeIs('assessor.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                {{ __('My Assessments') }}
            </x-sidebar-link>
            @endif

    </div>

    <!-- User Profile Dropdown (Bottom) -->
    <div class="border-t border-gray-200 p-4 bg-gray-50">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-700 group-hover:text-gray-900">
                        {{ Auth::user()->name }}
                    </p>
                    <a href="{{ route('profile.edit') }}" class="text-xs font-medium text-gray-500 group-hover:text-gray-700">
                        View Profile
                    </a>
                </div>
            </div>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

</aside>