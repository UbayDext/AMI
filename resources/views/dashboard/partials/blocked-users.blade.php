@if($blockedUsers->isNotEmpty())
<div class="mb-4">
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-2xl shadow-sm overflow-hidden relative">
        <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>
        <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-red-100 dark:border-red-800/30 bg-white/50 dark:bg-gray-800/30">
            <div class="flex items-start sm:items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-900/50 flex flex-shrink-0 items-center justify-center text-red-600 dark:text-red-400 shadow-sm border border-red-200/50 dark:border-red-800/50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-red-900 dark:text-red-200 text-lg leading-tight mb-1">Akun Terblokir</h3>
                    <p class="text-sm text-red-700/80 dark:text-red-300/80">
                        {{ $blockedUsers->count() }} akun dikunci karena terlalu banyak gagal login (3x berturut-turut).
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.users.index', ['status' => 'blocked']) }}" class="hidden sm:inline-flex items-center text-sm font-semibold text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                Kelola User
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        <div class="bg-white/30 dark:bg-[#151b2b] divide-y divide-red-100 dark:divide-red-800/30">
            @foreach($blockedUsers->take(5) as $user)
            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-white/60 dark:hover:bg-gray-800/40 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-center font-bold text-gray-700 dark:text-gray-300">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100 leading-tight">
                            {{ $user->name }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                            <span>{{ $user->email }}</span>
                            <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                            <span class="text-red-600 dark:text-red-400 font-medium">{{ $user->failed_login_attempts }}x gagal</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-sm justify-between sm:justify-end">
                    <div class="text-gray-400 dark:text-gray-500 text-xs hidden md:block">
                        Dikunci: {{ $user->last_failed_login_at?->diffForHumans() }}
                    </div>
                    <form action="{{ route('admin.users.unblock', $user) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 font-semibold hover:bg-blue-100 dark:hover:bg-blue-900/40 shadow-sm border border-blue-100 dark:border-blue-800/30 transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                            Buka Blokir
                        </button>
                    </form>
                </div>
            </div>
            @endforeach

            @if($blockedUsers->count() > 5)
            <div class="px-6 py-3 bg-red-50/50 dark:bg-red-900/10 text-center">
                <a href="{{ route('admin.users.index', ['status' => 'blocked']) }}" class="text-sm font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                    Lihat {{ $blockedUsers->count() - 5 }} akun lainnya...
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endif