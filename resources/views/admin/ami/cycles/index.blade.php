<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">AMI — Siklus Audit</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola siklus Audit Mutu Internal.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8"
        x-data="{
            showForm: false,
            deleteModal: false,
            deleteAction: '',
            openDelete(action) { this.deleteAction = action; this.deleteModal = true; },
            confirmDelete() { document.getElementById('delete-cycle-form').submit(); }
        }">

        <x-alert-success :message="session('success')" />

        {{-- Form buat siklus baru --}}
        <x-card padding="p-5" class="mb-6">
            <button @click="showForm = !showForm"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                <svg class="w-4 h-4 transition-transform" :class="showForm ? 'rotate-45' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Siklus AMI Baru
            </button>
            <form x-show="showForm" x-transition method="POST" action="{{ route('admin.ami.cycles.store') }}"
                class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Judul Siklus *</label>
                    <input type="text" name="title" required placeholder="e.g. AMI 2025/2026 Semester Genap"
                        class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none dark:bg-gray-800">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tanggal Mulai</label>
                    <input type="date" name="period_start"
                        class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none dark:bg-gray-800">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tanggal Selesai</label>
                    <input type="date" name="period_end"
                        class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none dark:bg-gray-800">
                </div>
                <div class="sm:col-span-2">
                    <button type="submit"
                        class="px-5 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Simpan Siklus
                    </button>
                </div>
            </form>
        </x-card>

        {{-- Daftar siklus --}}
        <div class="space-y-3">
            @forelse($cycles as $c)
            @php
                $badge = match($c->status) {
                    'active' => 'bg-emerald-100 text-emerald-700',
                    'closed' => 'bg-gray-200 text-gray-600',
                    default  => 'bg-amber-100 text-amber-700',
                };
                $label = match($c->status) { 'active' => 'Aktif', 'closed' => 'Selesai', default => 'Draft' };
            @endphp
            <x-card padding="p-4">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $c->title }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $badge }}">{{ $label }}</span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $c->submissions_count }} submission
                            @if($c->period_start)
                             · {{ $c->period_start->format('d M Y') }} – {{ $c->period_end?->format('d M Y') ?? '...' }}
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('admin.ami.cycles.show', $c) }}"
                            class="px-3 py-1.5 text-xs font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Kelola
                        </a>
                        <button type="button"
                            @click="openDelete('{{ route('admin.ami.cycles.destroy', $c) }}')"
                            class="p-1.5 text-red-400 hover:text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </x-card>
            @empty
            <x-card class="text-center py-10 text-sm text-gray-400">Belum ada siklus AMI.</x-card>
            @endforelse
        </div>

        {{-- Form hapus (tersembunyi, di-submit oleh modal) --}}
        <form id="delete-cycle-form" method="POST" :action="deleteAction" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        {{-- Modal konfirmasi hapus --}}
        <div x-show="deleteModal" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
            @keydown.escape.window="deleteModal = false">

            <div x-show="deleteModal" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-sm p-6"
                @click.stop>

                {{-- Ikon warning --}}
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 text-center mb-1">
                    Hapus Siklus AMI?
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">
                    Semua submission dan data terkait akan ikut terhapus. Tindakan ini tidak dapat dibatalkan.
                </p>

                <div class="flex gap-3">
                    <button type="button" @click="deleteModal = false"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        Batal
                    </button>
                    <button type="button" @click="confirmDelete()"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
