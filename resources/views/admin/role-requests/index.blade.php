<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Permintaan Role</h1>
            @if($counts['pending'] > 0)
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 text-sm font-semibold rounded-full">
                <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
                {{ $counts['pending'] }} menunggu
            </span>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Flash --}}
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3500)"
                class="flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 text-emerald-800 dark:text-emerald-400 rounded-2xl text-sm">
                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm">
                {{ session('error') }}
            </div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-4">
                @foreach(['pending' => ['label'=>'Menunggu','color'=>'amber'], 'approved' => ['label'=>'Disetujui','color'=>'emerald'], 'rejected' => ['label'=>'Ditolak','color'=>'red']] as $s => $meta)
                @php
                    $colorMap = [
                        'amber'   => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400',
                        'emerald' => 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400',
                        'red'     => 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
                    ];
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['status' => $s]) }}"
                    class="bg-white dark:bg-gray-800 rounded-2xl border {{ $status === $s ? 'border-indigo-300 dark:border-indigo-600 ring-1 ring-indigo-200' : 'border-gray-100 dark:border-gray-700' }} shadow-sm p-5 flex items-center gap-4 transition-all hover:shadow-md">
                    <span class="w-12 h-12 rounded-2xl {{ $colorMap[$meta['color']] }} flex items-center justify-center text-2xl font-bold">
                        {{ $counts[$s] }}
                    </span>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $meta['label'] }}</span>
                </a>
                @endforeach
            </div>

            {{-- Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                @if($requests->isEmpty())
                <div class="text-center py-16 text-gray-400 dark:text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-sm">Tidak ada permintaan.</p>
                </div>
                @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role Diminta</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Alasan</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Waktu</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @foreach($requests as $rr)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-colors" x-data="{ showApprove: false, showReject: false }">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $rr->user->name }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $rr->user->email }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                    Role saat ini:
                                    @forelse($rr->user->roles as $role)
                                        <span class="font-medium">{{ $role->name }}</span>@unless($loop->last), @endunless
                                    @empty
                                        <span class="italic">—</span>
                                    @endforelse
                                </p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $rr->requested_role === 'auditor' ? 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400' : 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400' }}">
                                    {{ $rr->roleLabel() }}
                                </span>
                            </td>
                            <td class="px-5 py-4 max-w-[180px]">
                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">{{ $rr->reason ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                                {{ $rr->created_at->diffForHumans() }}
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $badge = match($rr->status) {
                                        'pending'  => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300',
                                        'approved' => 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300',
                                        'rejected' => 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300',
                                    };
                                @endphp
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                    {{ $rr->statusLabel() }}
                                </span>
                                @if($rr->review_note)
                                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1 max-w-[120px] line-clamp-1" title="{{ $rr->review_note }}">
                                    {{ $rr->review_note }}
                                </p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($rr->isPending())
                                <div class="flex items-center gap-2">
                                    {{-- Approve button --}}
                                    <button @click="showApprove = true"
                                        class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded-xl hover:bg-emerald-700 transition-colors">
                                        Setujui
                                    </button>
                                    {{-- Reject button --}}
                                    <button @click="showReject = true"
                                        class="px-3 py-1.5 bg-red-500 text-white text-xs font-semibold rounded-xl hover:bg-red-600 transition-colors">
                                        Tolak
                                    </button>
                                </div>

                                {{-- Approve Modal --}}
                                <div x-show="showApprove" x-transition
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm"
                                    style="display:none">
                                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-7 max-w-sm w-full mx-4" @click.stop>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Setujui Permintaan</h3>
                                        <p class="text-sm text-gray-400 dark:text-gray-500 mb-5">
                                            Assign role <strong>{{ $rr->roleLabel() }}</strong> ke <strong>{{ $rr->user->name }}</strong>
                                            @if(!$rr->user->is_active) dan aktifkan akun-nya @endif.
                                        </p>
                                        <form method="POST" action="{{ route('admin.role-requests.approve', $rr) }}" class="space-y-4">
                                            @csrf @method('PATCH')
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Catatan (Opsional)</label>
                                                <input type="text" name="review_note" placeholder="Misal: Sesuai kebutuhan tim..."
                                                    class="w-full bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 text-gray-700 dark:text-gray-300">
                                            </div>
                                            <div class="flex gap-2.5">
                                                <button type="submit" class="flex-1 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-colors">
                                                    Ya, Setujui
                                                </button>
                                                <button type="button" @click="showApprove = false"
                                                    class="flex-1 py-2.5 border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                    Batal
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                {{-- Reject Modal --}}
                                <div x-show="showReject" x-transition
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm"
                                    style="display:none">
                                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-7 max-w-sm w-full mx-4" @click.stop>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Tolak Permintaan</h3>
                                        <p class="text-sm text-gray-400 dark:text-gray-500 mb-5">
                                            Tolak permintaan role dari <strong>{{ $rr->user->name }}</strong>.
                                        </p>
                                        <form method="POST" action="{{ route('admin.role-requests.reject', $rr) }}" class="space-y-4">
                                            @csrf @method('PATCH')
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Alasan Penolakan <span class="text-red-400">*</span></label>
                                                <input type="text" name="review_note" required placeholder="Misal: Role tidak sesuai jabatan..."
                                                    class="w-full bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 text-gray-700 dark:text-gray-300">
                                            </div>
                                            <div class="flex gap-2.5">
                                                <button type="submit" class="flex-1 py-2.5 bg-red-500 text-white text-sm font-semibold rounded-xl hover:bg-red-600 transition-colors">
                                                    Ya, Tolak
                                                </button>
                                                <button type="button" @click="showReject = false"
                                                    class="flex-1 py-2.5 border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                    Batal
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            {{-- Pagination --}}
            @if($requests->hasPages())
            <div class="px-1">{{ $requests->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
