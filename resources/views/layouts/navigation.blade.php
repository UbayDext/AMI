@php
    $user = auth()->user();
    $navLink = fn (bool $active) => $active
        ? 'relative flex h-14 items-center px-3 text-sm font-bold text-white after:absolute after:inset-x-2 after:bottom-0 after:h-0.5 after:rounded-full after:bg-white'
        : 'flex h-14 items-center px-3 text-sm font-medium text-blue-100 transition hover:text-white';
    $dropdownLink = 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-blue-50 hover:text-blue-700 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white';
    $isAudit = request()->routeIs('admin.assessments.*', 'admin.ami.*', 'assessor.assessments.*', 'assessor.ami.*', 'internal.ami.*');
    $isDocuments = request()->routeIs('admin.standard-preparations.*', 'admin.fosk.*', 'internal.standard-preparations.*');
    $isReferences = request()->routeIs('admin.questions.*', 'admin.question-categories.*', 'admin.accreditation-years.*', 'admin.auditor-decrees.*');
    $isUsers = request()->routeIs('admin.users.*', 'admin.role-requests.*', 'role-requests.*');
@endphp

<header x-data="{ mobileOpen: false }" class="relative z-40 bg-gradient-to-r from-sky-700 via-blue-700 to-indigo-800 text-white shadow-lg shadow-blue-950/10 dark:from-slate-900 dark:via-slate-900 dark:to-indigo-950">
    <div class="pointer-events-none absolute inset-0 overflow-hidden opacity-10" aria-hidden="true">
        <div class="absolute -right-16 -top-24 h-72 w-72 rounded-full border-[36px] border-white"></div>
        <div class="absolute right-40 top-4 h-32 w-32 rounded-full border-[20px] border-white"></div>
    </div>

    <div class="relative mx-auto flex w-full max-w-[1600px] items-center justify-between gap-4 px-4 py-5 sm:px-6 lg:px-8">
        <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3 sm:gap-4">
            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white p-1.5 shadow-md sm:h-16 sm:w-16">
                <img src="{{ asset('assets/logo.svg') }}" alt="Logo STIT Hidayatunnajah" class="h-full w-full object-contain">
            </span>
            <span class="min-w-0">
                <span class="block truncate text-xs font-medium text-blue-100 sm:text-sm">Sistem Penjaminan Mutu Internal</span>
                <span class="mt-1 block truncate text-base font-bold tracking-tight sm:text-xl">STIT Hidayatunnajah Bekasi</span>
            </span>
        </a>

        <div class="hidden items-center gap-2 sm:flex">
            <button @click="toggleTheme($event)" type="button" class="flex h-10 w-10 items-center justify-center rounded-xl text-blue-100 transition hover:bg-white/10 hover:text-white" :title="darkMode ? 'Gunakan mode terang' : 'Gunakan mode gelap'">
                <svg x-show="!darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <svg x-show="darkMode" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </button>

            <x-dropdown align="right" width="80" contentClasses="p-0 bg-white dark:bg-slate-800 overflow-hidden">
                <x-slot name="trigger">
                    <button class="flex items-center gap-3 rounded-xl px-2 py-1.5 text-left transition hover:bg-white/10">
                        <span class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full border-2 border-white/80 bg-white/15 text-sm font-bold">
                            @if($user->avatar_path)<img src="{{ Storage::url($user->avatar_path) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">@else{{ strtoupper(substr($user->name, 0, 2)) }}@endif
                        </span>
                        <span class="hidden max-w-36 lg:block"><span class="block truncate text-sm font-semibold">{{ $user->name }}</span><span class="block truncate text-xs text-blue-200">{{ $user->roles->pluck('name')->first() ?? 'User' }}</span></span>
                        <svg class="h-4 w-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <div class="flex items-center gap-3 border-b border-slate-100 p-4 dark:border-slate-700">
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-full border-2 border-blue-600 bg-blue-50 text-sm font-bold text-blue-700 dark:bg-slate-700 dark:text-blue-300">
                            @if($user->avatar_path)<img src="{{ Storage::url($user->avatar_path) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">@else{{ strtoupper(substr($user->name, 0, 2)) }}@endif
                        </span>
                        <div class="min-w-0"><p class="truncate text-sm font-bold text-slate-900 dark:text-white">{{ $user->name }}</p><a href="{{ route('profile.edit') }}" class="mt-1 inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:underline dark:text-blue-400">Lihat Profil <span>→</span></a></div>
                    </div>
                    <div class="space-y-1 p-3">
                        <div class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm text-slate-600 dark:text-slate-300"><span class="flex items-center gap-3"><svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10m-1.5-3h-7"/></svg>Bahasa Aplikasi</span><span class="text-xs font-bold">ID 🇮🇩</span></div>
                        <button @click="toggleTheme($event)" type="button" class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-sm text-slate-600 transition hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700"><span class="flex items-center gap-3"><svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>Tampilan</span><span class="text-xs" x-text="darkMode ? 'Gelap' : 'Terang'"></span></button>
                        <a href="mailto:support@stit-hidayatunnajah.ac.id" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-slate-600 transition hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700"><svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Bantuan</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-rose-600 transition hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-900/20"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>Keluar</button></form>
                    </div>
                </x-slot>
            </x-dropdown>
        </div>

        <button @click="mobileOpen = !mobileOpen" type="button" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 sm:hidden" aria-label="Buka navigasi">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/><path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <nav class="relative border-t border-white/10">
        <div class="mx-auto hidden w-full max-w-[1600px] items-center gap-1 px-4 sm:flex sm:px-6 lg:px-8">
            <a href="{{ route('dashboard') }}" class="{{ $navLink(request()->routeIs('dashboard')) }}">Dashboard</a>

            <x-nav-menu-dropdown label="Audit & AMI" :active="$isAudit">
                @can('manage assessments')<a href="{{ route('admin.assessments.index') }}" class="{{ $dropdownLink }}">Kelola Assessment</a><a href="{{ route('admin.ami.cycles.index') }}" class="{{ $dropdownLink }}">Siklus AMI</a>@endcan
                @if($user->hasRole('auditor'))<a href="{{ route('assessor.assessments.index') }}" class="{{ $dropdownLink }}">Assessment Saya</a><a href="{{ route('assessor.ami.index') }}" class="{{ $dropdownLink }}">Review AMI</a>@endif
                @if($user->hasRole('auditee'))<a href="{{ route('internal.ami.index') }}" class="{{ $dropdownLink }}">Pengisian AMI</a>@endif
            </x-nav-menu-dropdown>

            <x-nav-menu-dropdown label="Dokumen" :active="$isDocuments">
                @can('manage preparations')<a href="{{ route('admin.standard-preparations.landing') }}" class="{{ $dropdownLink }}">Evidence Standar</a><a href="{{ route('admin.fosk.index') }}" class="{{ $dropdownLink }}">FOSK Akreditasi</a>@endcan
                @if($user->hasAnyRole(['admin', 'auditor', 'auditee']))<a href="{{ route('internal.standard-preparations.index') }}" class="{{ $dropdownLink }}">Persiapan Standar</a>@endif
            </x-nav-menu-dropdown>

            @if($user->can('manage questions') || $user->can('manage assessments'))
            <x-nav-menu-dropdown label="Referensi" :active="$isReferences">
                @can('manage questions')<a href="{{ route('admin.questions.index') }}" class="{{ $dropdownLink }}">Bank Soal</a><a href="{{ route('admin.question-categories.index') }}" class="{{ $dropdownLink }}">Program Studi</a>@endcan
                @can('manage assessments')<a href="{{ route('admin.accreditation-years.index') }}" class="{{ $dropdownLink }}">Tahun Akreditasi</a><a href="{{ route('admin.auditor-decrees.index') }}" class="{{ $dropdownLink }}">SK Auditor</a>@endcan
            </x-nav-menu-dropdown>
            @endif

            @if($user->can('manage users') || $user->hasRole('auditee'))
            <x-nav-menu-dropdown label="Pengguna" :active="$isUsers">
                @can('manage users')<a href="{{ route('admin.users.index') }}" class="{{ $dropdownLink }}">Kelola Pengguna</a><a href="{{ route('admin.role-requests.index') }}" class="{{ $dropdownLink }}">Pengajuan Standar</a>@endcan
                @if($user->hasRole('auditee'))<a href="{{ route('role-requests.create') }}" class="{{ $dropdownLink }}">Ajukan Standar</a>@endif
            </x-nav-menu-dropdown>
            @endif
        </div>

        <div x-show="mobileOpen" x-collapse class="border-t border-white/10 px-4 py-3 sm:hidden">
            <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2.5 text-sm font-semibold hover:bg-white/10">Dashboard</a>
            @can('manage assessments')<a href="{{ route('admin.assessments.index') }}" class="block rounded-lg px-3 py-2.5 text-sm hover:bg-white/10">Kelola Assessment</a><a href="{{ route('admin.ami.cycles.index') }}" class="block rounded-lg px-3 py-2.5 text-sm hover:bg-white/10">Siklus AMI</a>@endcan
            @if($user->hasRole('auditor'))<a href="{{ route('assessor.assessments.index') }}" class="block rounded-lg px-3 py-2.5 text-sm hover:bg-white/10">Assessment Saya</a><a href="{{ route('assessor.ami.index') }}" class="block rounded-lg px-3 py-2.5 text-sm hover:bg-white/10">Review AMI</a>@endif
            @if($user->hasRole('auditee'))<a href="{{ route('internal.ami.index') }}" class="block rounded-lg px-3 py-2.5 text-sm hover:bg-white/10">Pengisian AMI</a>@endif
            <a href="{{ route('internal.standard-preparations.index') }}" class="block rounded-lg px-3 py-2.5 text-sm hover:bg-white/10">Persiapan Standar</a>
            <div class="mt-2 flex items-center gap-2 border-t border-white/10 pt-3"><button @click="toggleTheme($event)" class="rounded-lg px-3 py-2 text-sm hover:bg-white/10">Ganti Tema</button><a href="{{ route('profile.edit') }}" class="rounded-lg px-3 py-2 text-sm hover:bg-white/10">Profil</a><form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-lg px-3 py-2 text-sm text-rose-200 hover:bg-white/10">Keluar</button></form></div>
        </div>
    </nav>
</header>
