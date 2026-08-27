<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed z-30 inset-y-0 left-0 w-64 transition-all duration-300 transform bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 sm:translate-x-0 sm:static sm:inset-auto flex flex-col">

    <!-- Logo -->
    <div class="flex items-center gap-3 h-16 px-5 border-b border-gray-100 dark:border-gray-800 transition-colors">
        <div class="flex-shrink-0 w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="block text-gray-900 dark:text-white transition-colors">
                <div class="text-sm font-bold">{{ config('app.name', 'SPMI') }}</div>
                <div class="text-xs text-gray-400 dark:text-gray-500">
                    @if(auth()->user()->hasRole('admin')) Admin Panel
                    @elseif(auth()->user()->hasRole('auditor')) Auditor Panel
                    @else Auditee Panel
                    @endif
                </div>
            </a>
        </div>
    </div>

    <!-- Navigation Links -->
    <div class="overflow-y-auto overflow-x-hidden flex-grow px-3 py-4 space-y-0.5">

        <!-- Dashboard -->
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <svg class="flex-shrink-0 w-4.5 h-4.5 w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span>{{ __('Dashboard') }}</span>
        </x-sidebar-link>

        <!-- Admin -->
        @if(auth()->user()->can('manage assessments') || auth()->user()->can('manage preparations') || auth()->user()->can('manage questions') || auth()->user()->can('manage users'))
        <div class="mt-6 mb-2 px-3 text-[10px] font-semibold text-gray-400 uppercase tracking-widest">
            Admin
        </div>

        @can('manage assessments')
        <x-sidebar-link :href="route('admin.assessments.index')" :active="request()->routeIs('admin.assessments.*')">
            <svg class="flex-shrink-0 w-5 h-5 {{ request()->routeIs('admin.assessments.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <span>{{ __('Assessments') }}</span>
        </x-sidebar-link>
        @endcan

        @can('manage preparations')
        <x-sidebar-link :href="route('admin.standard-preparations.landing')" :active="request()->routeIs('admin.standard-preparations.*')">
            <svg class="flex-shrink-0 w-5 h-5 {{ request()->routeIs('admin.standard-preparations.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <span>{{ __('Evidence Indikator Standar') }}</span>
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.fosk.index')" :active="request()->routeIs('admin.fosk.*')">
            <svg class="flex-shrink-0 w-5 h-5 {{ request()->routeIs('admin.fosk.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <span>{{ __('FOSK Akreditasi') }}</span>
        </x-sidebar-link>
        @endcan

        @can('manage questions')
        <x-sidebar-link :href="route('admin.questions.index')" :active="request()->routeIs('admin.questions.*')">
            <svg class="flex-shrink-0 w-5 h-5 {{ request()->routeIs('admin.questions.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span>{{ __('Soal') }}</span>
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.question-categories.index')" :active="request()->routeIs('admin.question-categories.*')">
            <svg class="flex-shrink-0 w-5 h-5 {{ request()->routeIs('admin.question-categories.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            <span>{{ __('Prodi') }}</span>
        </x-sidebar-link>
        @endcan

        @can('manage assessments')
        <x-sidebar-link :href="route('admin.auditor-decrees.index')" :active="request()->routeIs('admin.auditor-decrees.*')">
            <svg class="flex-shrink-0 w-5 h-5 {{ request()->routeIs('admin.auditor-decrees.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>{{ __('SK Auditor') }}</span>
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.accreditation-years.index')" :active="request()->routeIs('admin.accreditation-years.*')">
            <svg class="flex-shrink-0 w-5 h-5 {{ request()->routeIs('admin.accreditation-years.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>{{ __('Tahun Akreditasi') }}</span>
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.ami.cycles.index')" :active="request()->routeIs('admin.ami.*')">
            <svg class="flex-shrink-0 w-5 h-5 {{ request()->routeIs('admin.ami.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span>AMI</span>
        </x-sidebar-link>
        @endcan

        @can('manage users')
        <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index') || request()->routeIs('admin.users.create') || request()->routeIs('admin.users.edit')">
            <svg class="flex-shrink-0 w-5 h-5 {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.users.create') || request()->routeIs('admin.users.edit') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span>{{ __('Users') }}</span>
        </x-sidebar-link>

        @php $pendingRoleRequests = \App\Models\RoleRequest::where('status', 'pending')->count(); @endphp
        <x-sidebar-link :href="route('admin.role-requests.index')" :active="request()->routeIs('admin.role-requests.*')">
            <svg class="flex-shrink-0 w-5 h-5 {{ request()->routeIs('admin.role-requests.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <span>Pengajuan Standar</span>
            @if($pendingRoleRequests > 0)
            <span class="ml-auto inline-flex items-center justify-center min-w-[1.1rem] h-[1.1rem] px-1 text-[10px] font-bold bg-amber-400 text-white rounded-full">
                {{ $pendingRoleRequests > 9 ? '9+' : $pendingRoleRequests }}
            </span>
            @endif
        </x-sidebar-link>
        @endcan
        @endif

        <!-- Auditor & Auditee -->
        @if(auth()->user()->hasRole('auditor') || auth()->user()->hasRole('auditee'))
        <div class="mt-6 mb-2 px-3 text-[10px] font-semibold text-gray-400 uppercase tracking-widest">
            {{ auth()->user()->hasRole('auditor') ? 'Auditor' : 'Auditee' }}
        </div>

        @if(auth()->user()->hasRole('auditor'))
        <x-sidebar-link :href="route('assessor.assessments.index')" :active="request()->routeIs('assessor.assessments.*')">
            <svg class="flex-shrink-0 w-5 h-5 {{ request()->routeIs('assessor.assessments.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span>{{ __('My Assessments') }}</span>
        </x-sidebar-link>

        <x-sidebar-link :href="route('assessor.ami.index')" :active="request()->routeIs('assessor.ami.*')">
            <svg class="flex-shrink-0 w-5 h-5 {{ request()->routeIs('assessor.ami.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span>AMI Review</span>
        </x-sidebar-link>
        @endif

        <x-sidebar-link :href="route('internal.standard-preparations.index')" :active="request()->routeIs('internal.standard-preparations.*')">
            <svg class="flex-shrink-0 w-5 h-5 {{ request()->routeIs('internal.standard-preparations.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <span>{{ __('Evidence Indikator Standar') }}</span>
        </x-sidebar-link>
        @endif

        @if(auth()->user()->hasRole('auditee'))
        <x-sidebar-link :href="route('internal.ami.index')" :active="request()->routeIs('internal.ami.*')">
            <svg class="flex-shrink-0 w-5 h-5 {{ request()->routeIs('internal.ami.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span>AMI</span>
        </x-sidebar-link>

        <x-sidebar-link :href="route('role-requests.create')" :active="request()->routeIs('role-requests.create')">
            <svg class="flex-shrink-0 w-5 h-5 {{ request()->routeIs('role-requests.create') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <span>Ajukan Standar</span>
            @if(auth()->user()->pendingRoleRequest)
            <span class="ml-auto inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold bg-amber-400 text-white rounded-full">1</span>
            @endif
        </x-sidebar-link>
        @endif

        <!-- Bottom helper & Theme Toggle -->
        <div class="mt-8 space-y-4">

            <!-- Dark Mode Toggle -->
            <div class="mx-1 px-3 py-2 flex items-center justify-between bg-gray-50 dark:bg-gray-800/50 rounded-xl transition-colors">
                <div class="flex items-center gap-2">
                    <svg x-show="!darkMode" class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                    </svg>
                    <svg x-show="darkMode" x-cloak class="w-4 h-4 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                    </svg>
                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-300" x-text="darkMode ? 'Dark Mode' : 'Light Mode'"></span>
                </div>
                <button @click="toggleTheme($event)" type="button" class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition-colors duration-200 ease-in-out" :class="darkMode ? 'bg-indigo-600' : 'bg-gray-200'" role="switch" :aria-checked="darkMode.toString()">
                    <span class="sr-only">Toggle dark mode</span>
                    <span aria-hidden="true" class="pointer-events-none inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="darkMode ? 'translate-x-2' : '-translate-x-2'"></span>
                </button>
            </div>

            <!-- Need Help -->
            <div class="mx-1 rounded-xl bg-indigo-50 dark:bg-gray-800 border border-indigo-100 dark:border-gray-700 p-3 transition-colors">
                <div class="flex items-start gap-2.5">
                    <div class="flex-shrink-0 w-6 h-6 bg-indigo-600 dark:bg-indigo-500 rounded-full flex items-center justify-center mt-0.5">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-indigo-800 dark:text-indigo-400">Butuh Bantuan?</p>
                        <p class="text-xs text-indigo-500 dark:text-gray-400 mt-0.5">Hubungi tim support jika mengalami kendala teknis.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Profile (Bottom) -->
    <div class="border-t border-gray-100 dark:border-gray-800 px-4 py-3 bg-white dark:bg-gray-900 transition-colors">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-100 dark:bg-gray-800 flex items-center justify-center">
                    <span class="text-xs font-semibold text-indigo-700 dark:text-indigo-400">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ Auth::user()->name }}</p>
                    <a href="{{ route('profile.edit') }}" class="text-xs text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">View Profile</a>
                </div>
            </div>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex-shrink-0 p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Logout">
                    <svg class="h-4.5 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

</aside>
