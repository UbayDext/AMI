<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            Create User
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 rounded-[24px] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden" x-data="{ role: '{{ old('role') }}' }">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    <div class="px-10 py-8 space-y-10">

                        {{-- ─── Basic Information ─── --}}
                        <div>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/60 flex items-center justify-center text-indigo-500">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Basic Information</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Name</label>
                                    <div class="relative">
                                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Name" required autofocus
                                            class="w-full pl-4 pr-10 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 text-sm text-gray-700 dark:text-gray-300 font-medium transition-colors placeholder:text-gray-300" />
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Email</label>
                                    <div class="relative">
                                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required
                                            class="w-full pl-4 pr-10 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 text-sm text-gray-700 dark:text-gray-300 font-medium transition-colors placeholder:text-gray-300" />
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                                </div>
                            </div>
                        </div>

                        {{-- ─── Role & Access ─── --}}
                        <div>
                            <div class="flex items-center justify-between mb-6 px-4 py-3 bg-indigo-50 dark:bg-indigo-900/40/50 dark:bg-indigo-900/20 rounded-2xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/60 flex items-center justify-center text-indigo-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-semibold text-gray-600 dark:text-gray-400">Role & Access</h3>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                                <!-- Primary Role -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Primary Role</label>
                                    <div class="relative">
                                        <select name="role" x-model="role" required
                                            class="w-full pl-4 pr-10 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 text-sm text-gray-700 dark:text-gray-300 font-medium transition-colors appearance-none">
                                            <option value="">-- Select Role --</option>
                                            @foreach($roles as $r)
                                            <option value="{{ $r }}">{{ ucfirst($r) }}</option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400 dark:text-gray-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('role')" class="mt-1" />
                                </div>

                                <!-- Role preview pills -->
                                <div class="flex flex-wrap gap-2 pt-[1.8rem]">
                                    <div class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-semibold transition-colors"
                                        :class="role === 'admin' ? 'bg-purple-100 text-purple-700 border border-purple-100 shadow-sm' : 'bg-gray-50 dark:bg-gray-900/50 text-gray-400 dark:text-gray-500 border border-transparent'">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                        Admin
                                    </div>
                                    <div class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-semibold transition-colors"
                                        :class="role === 'auditor' ? 'bg-emerald-100 text-emerald-700 border border-emerald-100 shadow-sm' : 'bg-gray-50 dark:bg-gray-900/50 text-gray-400 dark:text-gray-500 border border-transparent'">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                        Auditor
                                    </div>
                                    <div class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-semibold transition-colors"
                                        :class="role === 'auditee' ? 'bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/50 shadow-sm' : 'bg-gray-50 dark:bg-gray-900/50 text-gray-400 dark:text-gray-500 border border-transparent'">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                        Auditee
                                    </div>
                                </div>
                            </div>

                            <!-- Assigned Standard -->
                            <div class="mt-6 p-5 border border-indigo-100 dark:border-indigo-900/50 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl" x-show="role === 'auditee'" x-cloak x-transition
                                x-data="{ selected: {{ json_encode(old('assigned_standards', [])) }}, max: 20 }">
                                <label class="block text-sm font-semibold text-indigo-900 dark:text-indigo-200 mb-1">Assigned Standard <span class="text-rose-500">*</span></label>
                                <p class="text-xs text-indigo-500 mb-4">Pilih standar yang akan dikelola user ini. <span class="font-semibold">Maksimal 20 standar.</span></p>

                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">
                                        Dipilih: <span x-text="selected.length"></span>/5
                                    </span>
                                    <button type="button" @click="selected = []" x-show="selected.length > 0"
                                        class="text-xs text-red-500 hover:text-red-700 font-medium transition-colors">
                                        Reset pilihan
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 gap-2 max-h-60 overflow-y-auto pr-1">
                                    @foreach($standards as $standard)
                                    <label class="flex items-center gap-3 p-2.5 rounded-xl border cursor-pointer transition-all"
                                        :class="selected.includes('{{ $standard->code }}')
                                            ? 'border-indigo-400 bg-white dark:bg-indigo-900/30 shadow-sm'
                                            : 'border-transparent hover:border-indigo-200 hover:bg-white/60'"
                                        @click.prevent="
                                            if (selected.includes('{{ $standard->code }}')) {
                                                selected = selected.filter(s => s !== '{{ $standard->code }}')
                                            } else if (selected.length < max) {
                                                selected = [...selected, '{{ $standard->code }}']
                                            }
                                        ">
                                        <div class="flex-shrink-0 w-4 h-4 rounded border-2 flex items-center justify-center transition-all"
                                            :class="selected.includes('{{ $standard->code }}') ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300'">
                                            <svg x-show="selected.includes('{{ $standard->code }}')" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        <input type="checkbox" name="assigned_standards[]" value="{{ $standard->code }}"
                                            class="sr-only"
                                            :checked="selected.includes('{{ $standard->code }}')"
                                            @change.stop />
                                        <div class="min-w-0">
                                            <span class="text-xs font-bold text-indigo-700 dark:text-indigo-300">{{ $standard->code }}</span>
                                            <span class="text-xs text-gray-600 dark:text-gray-400 ml-1">- {{ $standard->name }}</span>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>

                                <p x-show="selected.length >= max" class="text-xs text-amber-600 dark:text-amber-400 mt-2 font-medium">
                                    ⚠ Maksimal 5 standar sudah dipilih.
                                </p>
                                <x-input-error :messages="$errors->get('assigned_standards')" class="mt-1" />
                                <x-input-error :messages="$errors->get('assigned_standards.*')" class="mt-1" />
                            </div>

                            {{-- Info: self-service role request --}}
                            <div class="mt-4 flex items-start gap-3 p-3.5 bg-sky-50 dark:bg-sky-900/20 border border-sky-100 dark:border-sky-800 rounded-2xl">
                                <svg class="w-4 h-4 text-sky-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-xs text-sky-700 dark:text-sky-400 leading-relaxed">
                                    Setelah akun dibuat, user dapat mengajukan perubahan role secara mandiri melalui halaman
                                    <strong>Permintaan Role</strong>. Admin akan menerima notifikasi untuk menyetujui atau menolak permintaan tersebut.
                                </p>
                            </div>

                        </div>

                        {{-- ─── Login Access Time ─── --}}
                        <div>
                            <div class="flex items-center justify-between mb-6 px-4 py-3 bg-indigo-50 dark:bg-indigo-900/40/50 dark:bg-indigo-900/20 rounded-2xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/60 flex items-center justify-center text-indigo-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-semibold text-gray-600 dark:text-gray-400">Login Access Time (Optional)</h3>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 pl-4">Set empty to allow access at any time.</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Start Time -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Start Time</label>
                                    <div class="relative">
                                        <input type="time" name="login_start_time" value="{{ old('login_start_time') }}"
                                            class="w-full pl-4 pr-10 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 text-sm text-gray-700 dark:text-gray-300 font-medium transition-colors" />
                                    </div>
                                    <x-input-error :messages="$errors->get('login_start_time')" class="mt-1" />
                                </div>

                                <!-- End Time -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">End Time</label>
                                    <div class="relative">
                                        <input type="time" name="login_end_time" value="{{ old('login_end_time') }}"
                                            class="w-full pl-4 pr-10 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 text-sm text-gray-700 dark:text-gray-300 font-medium transition-colors" />
                                    </div>
                                    <x-input-error :messages="$errors->get('login_end_time')" class="mt-1" />
                                </div>
                            </div>
                        </div>

                        {{-- ─── Security Settings ─── --}}
                        <div>
                            <div class="flex items-center justify-between mb-6 px-4 py-3 bg-indigo-50 dark:bg-indigo-900/40/50 dark:bg-indigo-900/20 rounded-2xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/60 flex items-center justify-center text-indigo-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-semibold text-gray-600 dark:text-gray-400">Security Settings</h3>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ pwd: '' }">
                                <!-- Password -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Password</label>
                                    <div class="relative">
                                        <input type="password" name="password" x-model="pwd" placeholder="••••••••" required autocomplete="new-password"
                                            class="w-full pl-10 pr-10 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 text-sm text-gray-700 dark:text-gray-300 tracking-widest transition-colors placeholder:text-gray-300 placeholder:tracking-normal" />
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                            </svg>
                                        </div>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="mt-1" />

                                    <!-- Strength Indicator -->
                                    <div class="mt-4">
                                        <div class="flex justify-between text-xs mb-1.5 font-medium">
                                            <span class="text-gray-400 dark:text-gray-500">Strength: <span class="text-gray-600 dark:text-gray-400" x-text="pwd.length === 0 ? 'None' : (pwd.length < 8 ? 'Weak' : 'Strong')">None</span></span>
                                        </div>
                                        <div class="h-1.5 w-1/2 bg-gray-100 dark:bg-gray-800/80 rounded-full overflow-hidden">
                                            <div class="h-full transition-all duration-300 rounded-full"
                                                :class="pwd.length === 0 ? 'w-0 bg-transparent' : (pwd.length < 8 ? 'w-1/2 bg-indigo-400' : 'w-full bg-emerald-400')"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Confirm Password</label>
                                    <div class="relative">
                                        <input type="password" name="password_confirmation" placeholder="••••••••" required
                                            class="w-full pl-4 pr-10 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 text-sm text-gray-700 dark:text-gray-300 tracking-widest transition-colors placeholder:text-gray-300 placeholder:tracking-normal" />
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- ─── Footer ─── --}}
                    <div class="px-10 py-6 text-sm flex items-center justify-end gap-3 mt-4">
                        <a href="{{ route('admin.users.index') }}"
                            class="px-5 py-2.5 font-semibold text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-100 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 font-semibold text-white bg-indigo-500 rounded-xl hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-200 shadow-sm transition-colors">
                            Create User
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>