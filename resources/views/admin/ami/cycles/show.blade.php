<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <a href="{{ route('admin.ami.cycles.index') }}" class="text-sm text-indigo-600 hover:underline">← Semua Siklus</a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 mt-1">{{ $cycle->title }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $cycle->period_start?->format('d M Y') ?? '—' }} – {{ $cycle->period_end?->format('d M Y') ?? '—' }}
                </p>
            </div>
            <form method="POST" action="{{ route('admin.ami.cycles.patch', $cycle) }}" class="flex items-center gap-2">
                @csrf @method('PATCH')
                <select name="status" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 dark:bg-gray-800">
                    @foreach(['draft' => 'Draft', 'active' => 'Aktif', 'closed' => 'Selesai'] as $val => $lbl)
                    <option value="{{ $val }}" {{ $cycle->status === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8"
        x-data="{
            showAdd: false,
            deleteModal: false,
            deleteAction: '',
            openDelete(action) { this.deleteAction = action; this.deleteModal = true; },
            confirmDelete() { document.getElementById('delete-submission-form').submit(); }
        }">

        <x-alert-success :message="session('success')" />
        @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>
        @endif

        <x-card padding="p-5" class="mb-6">
            <button @click="showAdd = !showAdd"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                <svg class="w-4 h-4 transition-transform" :class="showAdd ? 'rotate-45' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Submission (Prodi + Standar)
            </button>
            <form x-show="showAdd" x-transition method="POST"
                action="{{ route('admin.ami.cycles.submissions.store', $cycle) }}"
                class="mt-4 flex flex-wrap gap-4 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Prodi *</label>
                    <select name="prodi_id" required
                        class="border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm dark:bg-gray-800 focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                        <option value="">— Pilih Prodi —</option>
                        @foreach($prodis as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Standar *</label>
                    <select name="standard_id" required
                        class="min-w-[320px] border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm dark:bg-gray-800 focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                        <option value="">— Pilih Standar —</option>
                        @foreach($standards as $s)
                        <option value="{{ $s->id }}">{{ $s->code }}: {{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-indigo-600 dark:text-indigo-300 mb-1">Penugasan Auditee</label>
                    <div class="rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm text-indigo-700 dark:border-indigo-800 dark:bg-indigo-950/30 dark:text-indigo-300">Semua auditee aktif, termasuk auditee baru</div>
                    <select name="owner_id" disabled
                        class="hidden">
                        <option value="">— Pilih Auditee —</option>
                        @foreach($auditees as $auditee)
                        <option value="{{ $auditee->id }}">{{ $auditee->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Tambah
                </button>
            </form>
        </x-card>

        @forelse($submissions->groupBy('prodi_id') as $prodiId => $prodiSubmissions)
        @php $prodi = $prodiSubmissions->first()->prodi; @endphp
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 px-1">{{ $prodi?->name }}</h3>
            <div class="space-y-3">
                @foreach($prodiSubmissions as $sub)
                @php
                    $statusBadge = match($sub->status) {
                        'submitted'    => 'bg-blue-100 text-blue-700',
                        'under_review' => 'bg-amber-100 text-amber-700',
                        'accepted'     => 'bg-emerald-100 text-emerald-700',
                        'revision'     => 'bg-red-100 text-red-700',
                        default        => 'bg-gray-100 text-gray-600',
                    };
                    $statusLabel = ['draft'=>'Draft','submitted'=>'Submitted','under_review'=>'Dalam Review','accepted'=>'Diterima','revision'=>'Perlu Revisi'][$sub->status] ?? $sub->status;
                @endphp
                <x-card padding="p-4" x-data="{ showAssign: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-medium text-sm text-gray-800 dark:text-gray-200">
                                    {{ $sub->standard?->code }}: {{ $sub->standard?->name }}
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $statusBadge }}">{{ $statusLabel }}</span>
                                @if($sub->evidences->count())
                                <span class="text-xs text-gray-500">{{ $sub->evidences->count() }} link bukti</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Auditee: <strong>{{ $sub->assignment_group_id ? 'Semua auditee aktif' : ($sub->owner?->name ?? 'Belum ditetapkan') }}</strong>
                            </div>
                            @if($sub->review)
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Auditor: <strong>{{ $sub->review->reviewer?->name }}</strong>
                                · {{ ['pending'=>'Belum mulai','in_progress'=>'Sedang berjalan','completed'=>'Selesai'][$sub->review->status] ?? $sub->review->status }}
                            </div>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button @click="showAssign = !showAssign"
                                class="text-xs px-3 py-1.5 border border-indigo-300 text-indigo-600 rounded-lg hover:bg-indigo-50">
                                {{ $sub->review ? 'Ganti Auditor' : 'Tugaskan Auditor' }}
                            </button>
                            <button type="button"
                                @click="$dispatch('open-delete', { action: '{{ route('admin.ami.cycles.submissions.destroy', [$cycle, $sub]) }}' })"
                                class="p-1.5 text-red-400 hover:text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <form x-show="showAssign" x-transition method="POST"
                        action="{{ route('admin.ami.cycles.submissions.assign', [$cycle, $sub]) }}"
                        class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 flex flex-wrap gap-3 items-end">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Auditor *</label>
                            <select name="reviewer_id" required
                                class="border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 text-sm dark:bg-gray-800">
                                <option value="">— Pilih Auditor —</option>
                                @foreach($auditors as $a)
                                <option value="{{ $a->id }}" {{ $sub->review?->reviewer_id == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">SK Auditor</label>
                            <select name="decree_id"
                                class="border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 text-sm dark:bg-gray-800">
                                <option value="">— Tidak dipilih —</option>
                                @foreach($decrees as $d)
                                <option value="{{ $d->id }}" {{ $sub->review?->decree_id == $d->id ? 'selected' : '' }}>
                                    {{ $d->decree_number ?? $d->period_label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                            class="px-4 py-1.5 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Simpan
                        </button>
                    </form>
                </x-card>
                @endforeach
            </div>
        </div>
        @empty
        <x-card class="text-center py-10 text-sm text-gray-400">
            Belum ada submission. Tambahkan prodi + standar di atas.
        </x-card>
        @endforelse

        {{-- Form hapus tersembunyi --}}
        <form id="delete-submission-form" method="POST" :action="deleteAction" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        {{-- Modal konfirmasi hapus --}}
        <div x-show="deleteModal" x-transition.opacity
            @open-delete.window="openDelete($event.detail.action)"
            @keydown.escape.window="deleteModal = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">

            <div x-show="deleteModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-sm p-6"
                @click.stop>

                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 text-center mb-1">
                    Hapus Submission?
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">
                    Data submission, bukti, dan review terkait akan ikut terhapus. Tindakan ini tidak dapat dibatalkan.
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
