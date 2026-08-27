<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Persiapan — {{ $standard->code }}: {{ $standard->name }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola tahap & dokumen persiapan per standar.</p>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                {{-- Prodi Selector --}}
                @if($prodis->isNotEmpty())
                <form method="GET" action="{{ route('admin.standard-preparations.index', $standard) }}" class="flex items-center gap-2">
                    @if(request('stage'))
                    <input type="hidden" name="stage" value="{{ request('stage') }}">
                    @endif
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">Prodi:</label>
                    <select name="prodi" onchange="this.form.submit()"
                        class="rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm py-1.5 pr-8 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">— Pilih Prodi —</option>
                        @foreach($prodis as $p)
                        <option value="{{ $p->id }}" {{ $prodi?->id === $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                        @endforeach
                    </select>
                </form>
                @endif
                <a href="{{ route('admin.standard-preparations.landing') . ($prodi ? '?prodi=' . $prodi->id : '') }}"
                    class="text-sm text-indigo-600 hover:underline whitespace-nowrap">← Semua Standar</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{ showDeleteModal: false, deleteTargetName: '', deleteFormAction: '' }">

        <x-alert-success :message="session('success')" />
        @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>
        @endif

        @unless($prodi)
        <div class="mb-4 px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/60 text-amber-700 dark:text-amber-400 rounded-xl text-sm">
            Pilih prodi di atas untuk melihat progress dan file upload per prodi.
        </div>
        @endunless

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- LEFT: Stage list + add stage --}}
            <div class="lg:col-span-4 space-y-4">
                <x-card padding="p-5">
                    <div class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Evidence</div>

                    <div class="space-y-2 mb-4" id="stages-list">
                        @forelse($stages as $s)
                        @php
                        $isActive = $activeStage && $activeStage->id === $s->id;
                        if ($prodi) {
                            $allProg = $s->tasks->flatMap(fn($t) => $t->progress)->where('is_applicable', true);
                            $sDone   = $allProg->where('is_done', true)->count();
                            $sTotal  = $allProg->count();
                        } else {
                            $sTotal = $s->tasks->count();
                            $sDone  = 0;
                        }
                        $stageUrl = route('admin.standard-preparations.index', [$standard, 'stage' => $s->id])
                            . ($prodi ? '&prodi=' . $prodi->id : '');
                        @endphp
                        <div class="flex items-start gap-2">
                            <a href="{{ $stageUrl }}"
                                class="flex-1 block border rounded-xl p-3 text-sm transition-colors
                                   {{ $isActive ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/40' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/40' }}">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-medium {{ $isActive ? 'text-indigo-700 dark:text-indigo-400' : 'text-gray-800 dark:text-gray-200' }}">{{ $s->title }}</span>
                                    @if($s->prodi_id)
                                    <span class="text-xs px-1.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">{{ $s->prodi?->name }}</span>
                                    @endif
                                </div>
                                @if($s->description)
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-1">{{ $s->description }}</div>
                                @endif
                                <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                    {{ $s->tasks->count() }} task
                                    @if($prodi)
                                     · <span class="{{ $sDone === $sTotal && $sTotal > 0 ? 'text-emerald-600' : '' }}">{{ $sDone }}/{{ $sTotal }} selesai</span>
                                    @endif
                                </div>
                            </a>
                            <button type="button"
                                @click.prevent="deleteTargetName = 'Tahap: {{ addslashes($s->title) }}'; deleteFormAction = '{{ route('admin.standard-preparations.stages.destroy', [$standard, $s]) }}{{ $prodi ? '?prodi=' . $prodi->id : '' }}'; showDeleteModal = true;"
                                class="mt-2 p-2 text-red-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors" title="Hapus Tahap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                        @empty
                        <p class="text-sm text-gray-400 dark:text-gray-500">Belum ada tahap. Tambahkan di bawah.</p>
                        @endforelse
                    </div>

                    {{-- Add Stage Form --}}
                    <details class="group">
                        <summary class="cursor-pointer text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                            <svg class="w-4 h-4 transition-transform group-open:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Tahap
                        </summary>
                        <form method="POST" action="{{ route('admin.standard-preparations.stages.store', $standard) }}"
                            class="mt-3 space-y-3 border-t pt-3">
                            @csrf
                            @if($prodi)
                            <input type="hidden" name="prodi" value="{{ $prodi->id }}">
                            @endif
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Judul Tahap *</label>
                                <input type="text" name="title" required
                                    class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none"
                                    placeholder="e.g. Pengumpulan Dokumen">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Kategori Jenis Dokumen *</label>
                                <select name="category" required class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                                    <option value="">Pilih kategori dokumen</option>
                                    <option value="kebijakan">Dokumen Kebijakan</option>
                                    <option value="pelaksanaan">Dokumen Pelaksanaan</option>
                                    <option value="evaluasi">Dokumen Evaluasi</option>
                                    <option value="pendukung_digital">Dokumen Pendukung Digital</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Prodi Tujuan</label>
                                <select name="prodi_ids[]" multiple size="4" class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                                    @foreach($prodis as $optionProdi)
                                    <option value="{{ $optionProdi->id }}" @selected($prodi?->id === $optionProdi->id || $activeStage->prodi_id === $optionProdi->id)>{{ $optionProdi->name }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-400">Ctrl/Cmd untuk memilih beberapa. Kosong berarti semua prodi.</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Deskripsi</label>
                                <textarea name="description" rows="2"
                                    class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none"
                                    placeholder="Opsional"></textarea>
                            </div>
                            <button type="submit"
                                class="w-full py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                Simpan Tahap
                            </button>
                        </form>
                    </details>
                </x-card>
            </div>

            {{-- RIGHT: Tasks for active stage --}}
            <div class="lg:col-span-8 space-y-4">
                @if($activeStage)
                <x-card padding="p-5">
                    <div id="stage-header-wrapper">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $activeStage->title }}</div>
                                @if($activeStage->description)
                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $activeStage->description }}</div>
                                @endif
                            </div>
                            @if($prodi)
                            @php
                            $allProg   = $activeStage->tasks->flatMap(fn($t) => $t->progress)->where('is_applicable', true);
                            $stageTotal = $allProg->count();
                            $stageDone  = $allProg->where('is_done', true)->count();
                            $stagePct   = $stageTotal > 0 ? (int) round($stageDone / $stageTotal * 100) : 0;
                            @endphp
                            <div class="text-right text-sm">
                                <div class="font-medium text-gray-700 dark:text-gray-300">{{ $stageDone }}/{{ $stageTotal }}</div>
                                <div class="text-xs text-gray-400 dark:text-gray-500">{{ $stagePct }}% selesai</div>
                            </div>
                            @endif
                        </div>

                        @if($prodi)
                        <div class="w-full bg-gray-100 dark:bg-gray-800/80 rounded-full h-1.5 mb-5 overflow-hidden">
                            <div class="h-1.5 bg-indigo-500 rounded-full transition-all" style="width: {{ $stagePct ?? 0 }}%"></div>
                        </div>
                        @endif
                    </div>

                    <div class="space-y-3 mb-6" id="tasks-list">
                        @forelse($activeStage->tasks->sortBy('sort_order') as $t)
                        @php
                        $prog   = $t->progress->first();
                        $isDone = $prog?->is_done ?? false;
                        $files  = $t->files;
                        @endphp
                        <div class="border rounded-xl p-4
                            {{ $isDone
                                ? 'bg-emerald-50 border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800/50'
                                : 'bg-gray-50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($t->link)
                                        <a href="{{ $t->link }}" target="_blank" rel="noopener noreferrer"
                                            class="font-medium text-sm text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                                            {{ $t->title }}
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </a>
                                        @else
                                        <span class="font-medium text-sm text-gray-800 dark:text-gray-200">{{ $t->title }}</span>
                                        @endif
                                        @if($t->is_required)
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-800 text-white">Wajib</span>
                                        @else
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400">Opsional</span>
                                        @endif
                                        @if($t->prodi_id)
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">{{ $t->prodi?->name }}</span>
                                        @endif
                                        @if($isDone)
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">✓ Selesai</span>
                                        @endif
                                    </div>

                                    {{-- File list per prodi --}}
                                    @if($files->isNotEmpty())
                                    <div class="mt-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg p-3 space-y-2">
                                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
                                            Dokumen Terlampir{{ $prodi ? ' — ' . $prodi->name : '' }}
                                        </div>
                                        @foreach($files->sortByDesc('created_at') as $f)
                                        <div class="flex items-center gap-2 text-xs">
                                            <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            <div class="flex-1 min-w-0 flex items-center gap-2">
                                                <a href="{{ $f->link_url ?: asset('storage/' . $f->file_path) }}" target="_blank"
                                                    class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline truncate max-w-xs block">
                                                    {{ $f->original_name }}
                                                </a>
                                                <span class="text-gray-400 dark:text-gray-500 flex-shrink-0">
                                                    · {{ optional($f->created_at)->format('d/m/Y H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                <button type="button"
                                    @click.prevent="deleteTargetName = 'Task: {{ addslashes($t->title) }}'; deleteFormAction = '{{ route('admin.standard-preparations.tasks.destroy', [$standard, $activeStage, $t]) }}{{ $prodi ? '?prodi=' . $prodi->id : '' }}'; showDeleteModal = true;"
                                    class="p-1.5 text-red-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors" title="Hapus Task">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-6 text-sm text-gray-400 dark:text-gray-500">
                            <svg class="mx-auto w-8 h-8 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Belum ada task. Tambahkan di bawah.
                        </div>
                        @endforelse
                    </div>

                    {{-- Add Task Form --}}
                    <details class="group border-t pt-4">
                        <summary class="cursor-pointer text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                            <svg class="w-4 h-4 transition-transform group-open:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Task Baru
                        </summary>
                        <form method="POST"
                            action="{{ route('admin.standard-preparations.tasks.store', [$standard, $activeStage]) }}"
                            class="mt-3 space-y-3">
                            @csrf
                            @if($prodi)
                            <input type="hidden" name="prodi" value="{{ $prodi->id }}">
                            <input type="hidden" name="prodi_id" value="{{ $prodi->id }}">
                            @endif
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Judul Dokumen / Task *</label>
                                <input type="text" name="title" required
                                    class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none"
                                    placeholder="e.g. Surat Pernyataan Dekan">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Link URL Dokumen</label>
                                <input type="url" name="link"
                                    class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none"
                                    placeholder="https://drive.google.com/...">
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="is_required" value="1" id="req_task" checked
                                    class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                                <label for="req_task" class="text-sm text-gray-700 dark:text-gray-300">Dokumen wajib</label>
                            </div>
                            <button type="submit"
                                class="w-full py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                Tambah Task
                            </button>
                        </form>
                    </details>
                </x-card>
                @else
                <x-card class="text-center py-12">
                    <x-empty-state icon="document" title="Pilih tahap" subtitle="Pilih atau tambahkan tahap terlebih dahulu di menu sebelah kiri." />
                </x-card>
                @endif
            </div>
        </div>

        <x-delete-modal title="Hapus Item?">
            Anda yakin ingin menghapus <span class="text-white font-medium" x-text="deleteTargetName"></span> secara permanen?
        </x-delete-modal>
    </div>

    @push('scripts')
    <script>
        setInterval(() => {
            fetch(window.location.href)
                .then(res => res.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    ['stages-list', 'stage-header-wrapper', 'tasks-list'].forEach(id => {
                        const n = doc.getElementById(id), o = document.getElementById(id);
                        if (n && o) o.innerHTML = n.innerHTML;
                    });
                });
        }, 60000);
    </script>
    @endpush
</x-app-layout>
