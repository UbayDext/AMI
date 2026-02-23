<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            Edit User
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @php
            // Determine initial primary role (filter out standard codes)
            $primaryRole = $user->roles->firstWhere(fn($r) => !in_array($r->name, $standards->pluck('code')->toArray()))->name ?? '';
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-[24px] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden" x-data="{ role: '{{ old('role', $primaryRole) }}' }">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="px-10 py-8 space-y-10">

                        {{-- ─── Basic Information ─── --}}
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/60 flex items-center justify-center text-indigo-500">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Basic Information</h3>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <span class="text-xs font-semibold {{ $user->is_active ? 'text-emerald-500' : 'text-gray-400 dark:text-gray-500' }} uppercase tracking-widest">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                                    <div class="relative inline-flex h-6 w-11 items-center rounded-full {{ $user->is_active ? 'bg-emerald-400' : 'bg-gray-200 dark:bg-gray-700' }}">
                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white dark:bg-gray-800 shadow-sm transition {{ $user->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Name</label>
                                    <div class="relative">
                                        <input type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Name" required autofocus
                                            class="w-full pl-4 pr-10 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 text-sm text-gray-700 dark:text-gray-300 font-medium transition-colors placeholder:text-gray-300" />
                                    </div>
                                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Email</label>
                                    <div class="relative">
                                        <input type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Email" required
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
                            <div class="flex items-center gap-3 mb-6 px-4 py-3 bg-indigo-50 dark:bg-indigo-900/40/50 dark:bg-indigo-900/20 rounded-2xl">
                                <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/60 flex items-center justify-center text-indigo-500">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-base font-semibold text-gray-600 dark:text-gray-400">Role & Access</h3>
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
                                        :class="role === 'asesor' ? 'bg-emerald-100 text-emerald-700 border border-emerald-100 shadow-sm' : 'bg-gray-50 dark:bg-gray-900/50 text-gray-400 dark:text-gray-500 border border-transparent'">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                        Asesor
                                    </div>
                                    <div class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-semibold transition-colors"
                                        :class="role === 'standar' ? 'bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/50 shadow-sm' : 'bg-gray-50 dark:bg-gray-900/50 text-gray-400 dark:text-gray-500 border border-transparent'">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                        Standar
                                    </div>
                                </div>
                            </div>

                            <!-- Assigned Standard -->
                            <div class="mt-6 p-5 border border-indigo-100 dark:border-indigo-900/50 bg-indigo-50 dark:bg-indigo-900/40/50 dark:bg-indigo-900/20 rounded-2xl" x-show="role === 'standar'" x-cloak x-transition>
                                <label class="block text-sm font-semibold text-indigo-900 dark:text-indigo-200 mb-1">Assigned Standard <span class="text-rose-500">*</span></label>
                                <p class="text-xs text-indigo-500 mb-4">Select the specific standard this user will manage.</p>
                                <div class="relative">
                                    <select name="assigned_standard" :required="role === 'standar'"
                                        class="w-full pl-4 pr-10 py-2.5 text-sm bg-white dark:bg-gray-800 border border-indigo-200 dark:border-indigo-700/50 rounded-xl focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 text-indigo-900 dark:text-indigo-200 font-medium appearance-none">
                                        <option value="">-- Select Standard --</option>
                                        @foreach($standards as $standard)
                                        <option value="{{ $standard->code }}" @selected(old('assigned_standard', $userAssignedStandard)===$standard->code)>
                                            {{ $standard->code }} - {{ $standard->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-indigo-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('assigned_standard')" class="mt-1" />
                            </div>
                        </div>

                        {{-- ─── Change Password ─── --}}
                        <div>
                            <div class="flex items-center gap-3 mb-4 px-4 py-3 bg-indigo-50 dark:bg-indigo-900/40/50 dark:bg-indigo-900/20 rounded-2xl">
                                <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/60 flex items-center justify-center text-indigo-500">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </div>
                                <h3 class="text-base font-semibold text-gray-600 dark:text-gray-400">Change Password</h3>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 pl-4">Leave blank if you don't want to change the password.</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ pwd: '' }">
                                <!-- Password -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">New Password</label>
                                    <div class="relative">
                                        <input type="password" name="password" x-model="pwd" placeholder="••••••••" autocomplete="new-password"
                                            class="w-full pl-4 pr-10 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 text-sm text-gray-700 dark:text-gray-300 tracking-widest transition-colors placeholder:text-gray-300 placeholder:tracking-normal" />
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="mt-1" />

                                    <!-- Strength Indicator -->
                                    <div class="mt-4" x-show="pwd.length > 0" x-transition>
                                        <div class="flex justify-between text-xs mb-1.5 font-medium">
                                            <span class="text-gray-400 dark:text-gray-500">Strength: <span class="text-gray-600 dark:text-gray-400" x-text="pwd.length < 8 ? 'Weak' : 'Strong'"></span></span>
                                        </div>
                                        <div class="h-1.5 w-1/2 bg-gray-100 dark:bg-gray-800/80 rounded-full overflow-hidden">
                                            <div class="h-full transition-all duration-300 rounded-full"
                                                :class="pwd.length < 8 ? 'w-1/2 bg-indigo-400' : 'w-full bg-emerald-400'"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Confirm New Password</label>
                                    <div class="relative">
                                        <input type="password" name="password_confirmation" placeholder="••••••••"
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
                            Update User
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>