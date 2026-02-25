<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Admin — Users</h1>
            <a href="{{ route('admin.users.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-indigo-700 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Create User
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ showDeleteModal: false, deleteTargetName: '', deleteFormAction: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Flash messages --}}
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3500)"
                class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm">
                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                {{ session('error') }}
            </div>
            @endif

            {{-- ─── Stats Card ────────────────── --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm px-6 py-5">
                <div class="grid grid-cols-2 lg:grid-cols-5 gap-6">

                    {{-- Total Users --}}
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900/60 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Total Users</div>
                        </div>
                    </div>

                    {{-- Active Users --}}
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['active'] }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Active Users</div>
                        </div>
                    </div>

                    {{-- Pending Approval --}}
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-900/60 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['pending'] }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Pending Approval</div>
                        </div>
                    </div>

                    {{-- Blocked Users --}}
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-900/60 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['blocked'] }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Blocked Users</div>
                        </div>
                    </div>

                    {{-- Admin Users --}}
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-900/60 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['admin'] }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Admin Users</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── Main Table Card ────────────────── --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">

                {{-- Filter Bar --}}
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <form method="GET" action="{{ route('admin.users.index') }}"
                        class="flex flex-wrap items-center gap-3">

                        {{-- Role filter --}}
                        <div class="relative">
                            <select name="role" onchange="this.form.submit()"
                                class="appearance-none pl-4 pr-8 py-2 text-sm font-medium border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300 cursor-pointer">
                                <option value="">All Roles</option>
                                @foreach($roles as $r)
                                <option value="{{ $r }}" @selected(request('role')===$r)>{{ ucfirst($r) }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        {{-- Status filter --}}
                        <div class="relative">
                            <select name="status" onchange="this.form.submit()"
                                class="appearance-none pl-4 pr-8 py-2 text-sm font-medium border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300 cursor-pointer">
                                <option value="">All Statuses</option>
                                <option value="active" @selected(request('status')==='active' )>Aktif</option>
                                <option value="pending" @selected(request('status')==='pending' )>Menunggu Persetujuan</option>
                                <option value="blocked" @selected(request('status')==='blocked' )>Diblokir</option>
                            </select>
                            <div class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        {{-- Search --}}
                        <div class="relative flex-1 min-w-[200px]">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari pengguna..."
                                class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 dark:focus:ring-indigo-500/50" />
                        </div>

                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter
                        </button>
                        @if(request('role') || request('status') || request('search'))
                        <a href="{{ route('admin.users.index') }}"
                            class="text-sm text-gray-400 dark:text-gray-500 hover:text-gray-600 transition-colors">Reset</a>
                        @endif

                        <span class="ml-auto text-xs text-gray-400 dark:text-gray-500">
                            <strong class="text-gray-600 dark:text-gray-400">{{ $users->total() }}</strong> pengguna
                        </span>
                    </form>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <th class="pl-5 pr-3 py-3 w-10">
                                    <input type="checkbox" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Login Time</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Since</th>
                                <th class="px-4 py-3 w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            @forelse($users as $u)
                            @php
                            $colors = [
                            'bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-400',
                            'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-400',
                            'bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-400',
                            'bg-rose-100 dark:bg-rose-900/60 text-rose-700 dark:text-rose-400',
                            'bg-purple-100 dark:bg-purple-900/60 text-purple-700 dark:text-purple-400',
                            'bg-sky-100 dark:bg-sky-900/60 text-sky-700 dark:text-sky-400'
                            ];
                            $avatarColor = $colors[crc32($u->name) % count($colors)];
                            @endphp
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/50 transition-colors group">
                                {{-- Checkbox --}}
                                <td class="pl-5 pr-3 py-3.5">
                                    <input type="checkbox" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                                </td>

                                {{-- Name + Email --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-9 h-9 rounded-full {{ $avatarColor }} flex items-center justify-center text-sm font-bold">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $u->name }}</div>
                                            <div class="text-xs text-gray-400 dark:text-gray-500">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Role badges --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($u->getRoleNames() as $role)
                                        @php
                                        $roleCls = match($role) {
                                        'admin' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300',
                                        'asesor' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
                                        'standar' => 'bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-400',
                                        default => 'bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300',
                                        };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $roleCls }}">
                                            {{ ucfirst($role) }}
                                        </span>
                                        @endforeach
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3.5">
                                    @if($u->hasRole('admin'))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-800/80 text-gray-600 dark:text-gray-400">
                                        Selalu Aktif
                                    </span>
                                    @elseif($u->is_blocked)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/40 dark:text-red-300 dark:border-red-800">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        Diblokir
                                    </span>
                                    @elseif($u->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-800">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Aktif
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-800">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Menunggu Persetujuan
                                    </span>
                                    @endif
                                </td>

                                {{-- Login Time --}}
                                <td class="px-4 py-3.5 text-sm">
                                    @if($u->login_start_time && $u->login_end_time)
                                    <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-indigo-50/50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 border border-indigo-100/50 dark:border-indigo-800/30">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="font-medium tracking-wide">{{ $u->login_start_time->format('H:i') }} &mdash; {{ $u->login_end_time->format('H:i') }} WIB</span>
                                    </div>
                                    @else
                                    <span class="text-gray-400 dark:text-gray-500 italic text-xs">Bebas Akses</span>
                                    @endif
                                </td>

                                {{-- Since --}}
                                <td class="px-4 py-3.5 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ $u->created_at->format('d M Y') }}
                                </td>

                                {{-- 3-Dot Actions Menu --}}
                                <td class="px-4 py-3.5 text-right">
                                    <div class="relative flex justify-end" x-data="{ open: false }" @click.outside="open = false">
                                        <button @click="open = !open"
                                            class="w-8 h-8 flex items-center justify-center text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                            </svg>
                                        </button>

                                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            class="absolute right-0 top-9 z-20 w-44 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 py-1.5 origin-top-right"
                                            style="display:none">

                                            {{-- Edit --}}
                                            <a href="{{ route('admin.users.edit', $u) }}"
                                                class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </a>

                                            @unless($u->hasRole('admin'))

                                            @if($u->is_blocked)
                                            <form method="POST" action="{{ route('admin.users.unblock', $u) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                                    </svg>
                                                    Unblock Account
                                                </button>
                                            </form>
                                            @else

                                            {{-- Activate --}}
                                            @if(!$u->is_active)
                                            <form method="POST" action="{{ route('admin.users.toggle-active', $u) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors">
                                                    <span class="w-4 h-4 flex items-center justify-center">
                                                        <span class="w-4 h-4 rounded-full bg-emerald-500 flex items-center justify-center">
                                                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        </span>
                                                    </span>
                                                    Activate
                                                </button>
                                            </form>
                                            @endif

                                            {{-- Deactivate --}}
                                            @if($u->is_active)
                                            <form method="POST" action="{{ route('admin.users.toggle-active', $u) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                                    <span class="w-4 h-4 flex items-center justify-center">
                                                        <span class="w-4 h-4 rounded-full bg-red-500 flex items-center justify-center">
                                                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </span>
                                                    </span>
                                                    Deactivate
                                                </button>
                                            </form>
                                            @endif
                                            @endif

                                            <div class="my-1 border-t border-gray-100 dark:border-gray-700"></div>

                                            {{-- Delete --}}
                                            <button type="button"
                                                @click.prevent="deleteTargetName = '{{ addslashes($u->name) }}'; deleteFormAction = '{{ route('admin.users.destroy', $u) }}'; showDeleteModal = true; open = false;"
                                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                            @endunless
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <div class="mx-auto w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-800/80 flex items-center justify-center mb-3">
                                        <svg class="w-7 h-7 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada user.</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Buat user pertama dengan klik "+ Create User"</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($users->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $users->links() }}
                </div>
                @endif
            </div>

        </div>

        {{-- Delete Confirmation Modal via Component --}}
        <x-delete-modal title="Hapus User?">
            User "<span class="text-gray-900 dark:text-white font-medium" x-text="deleteTargetName"></span>" akan dihapus <span class="text-red-600 dark:text-red-500 font-medium">permanen</span> dari sistem.
        </x-delete-modal>

    </div>
</x-app-layout>