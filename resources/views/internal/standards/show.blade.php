<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <a href="{{ route('internal.standard-preparations.index') . ($prodi ? '?prodi=' . $prodi->id : '') }}"
                        class="text-sm text-indigo-600 hover:underline">
                        ← Semua Standar
                    </a>
                </div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                    {{ $standard->code }}: {{ $standard->name }}
                </h2>
                @unless(auth()->user()->hasRole('asesor'))
                @if($prodi)
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $prodi->name }} · {{ $done }}/{{ $total }} selesai ({{ $percent }}%)
                </p>
                @endif
                @endunless
            </div>

            {{-- Prodi Checklist Filter --}}
            @if($prodis->isNotEmpty())
            <form method="GET" action="{{ route('internal.standard-preparations.show', $standard) }}"
                class="flex items-center gap-2 flex-wrap">
                @if(request('stage'))
                <input type="hidden" name="stage" value="{{ request('stage') }}">
                @endif
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">Prodi:</span>

                <label class="inline-flex items-center gap-1.5 cursor-pointer select-none group">
                    <span class="relative flex items-center justify-center w-4 h-4">
                        <input type="radio" name="prodi" value=""
                            {{ !$prodi ? 'checked' : '' }}
                            onchange="this.form.submit()"
                            class="peer sr-only">
                        <span class="w-4 h-4 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                            peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-colors"></span>
                        <svg class="absolute w-2.5 h-2.5 text-white pointer-events-none opacity-0 peer-checked:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <span class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors {{ !$prodi ? 'font-semibold text-indigo-600 dark:text-indigo-400' : '' }}">
                        Semua
                    </span>
                </label>

                <span class="text-gray-200 dark:text-gray-700 text-xs">|</span>

                @foreach($prodis as $p)
                <label class="inline-flex items-center gap-1.5 cursor-pointer select-none group">
                    <span class="relative flex items-center justify-center w-4 h-4">
                        <input type="radio" name="prodi" value="{{ $p->id }}"
                            {{ $prodi?->id === $p->id ? 'checked' : '' }}
                            onchange="this.form.submit()"
                            class="peer sr-only">
                        <span class="w-4 h-4 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                            peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-colors"></span>
                        <svg class="absolute w-2.5 h-2.5 text-white pointer-events-none opacity-0 peer-checked:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <span class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors {{ $prodi?->id === $p->id ? 'font-semibold text-indigo-600 dark:text-indigo-400' : '' }}">
                        {{ $p->name }}
                    </span>
                </label>
                @endforeach
            </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="mb-4 p-4 bg-emerald-50 dark:bg-emerald-900/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-400 rounded-xl text-sm">
            {{ session('success') }}
        </div>
        @endif
        @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
            <ul class="list-disc ml-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        @unless($prodi)
        <div class="mb-4 px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/60 text-amber-700 dark:text-amber-400 rounded-xl text-sm">
            Pilih prodi di atas untuk melihat progress dan mengelola dokumen per prodi.
        </div>
        @endunless

        @if($stages->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center text-gray-400 dark:text-gray-500">
            <svg class="mx-auto w-10 h-10 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <p class="text-sm">Belum ada evidence untuk standar ini.</p>
            @if(auth()->user()->hasRole('auditee') || auth()->user()->hasRole('admin'))
            <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'create-stage')"
                class="mt-4 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                + Tambah Evidence
            </button>
            @else
            <p class="text-xs mt-1">Hubungi admin untuk menambahkan evidence.</p>
            @endif
        </div>
        @else

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- LEFT: Stage list --}}
            <div class="lg:col-span-4 space-y-3">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Evidence</div>
                        @if(auth()->user()->hasRole('auditee') || auth()->user()->hasRole('admin'))
                        <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'create-stage')"
                            class="text-xs font-medium text-indigo-600 hover:text-indigo-700">+ Tambah</button>
                        @endif
                    </div>

                    @unless(auth()->user()->hasRole('asesor'))
                    @if($prodi && $total > 0)
                    <div class="mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                            <span>Progres — {{ $prodi->name }}</span>
                            <span class="font-medium">{{ $percent }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-800/80 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full {{ $percent === 100 ? 'bg-emerald-500' : 'bg-indigo-500' }} transition-all"
                                style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                    @endif
                    @endunless

                    <div class="space-y-2">
                        @foreach($stages as $s)
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
                        $sPct = $sTotal > 0 ? (int) round($sDone / $sTotal * 100) : 0;
                        $stageUrl = route('internal.standard-preparations.show', [$standard, 'stage' => $s->id])
                            . ($prodi ? '&prodi=' . $prodi->id : '');
                        @endphp
                        <a href="{{ $stageUrl }}"
                            class="block rounded-xl p-3 border transition-colors
                               {{ $isActive ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/40' : 'border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/40' }}">
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="text-sm font-medium {{ $isActive ? 'text-indigo-700 dark:text-indigo-400' : 'text-gray-800 dark:text-gray-200' }}">
                                    {{ $s->title }}
                                </div>
                                <div class="flex items-center gap-3">
                                    @unless(auth()->user()->hasRole('asesor'))
                                    @if($prodi)
                                    <span class="text-xs {{ $sPct === 100 ? 'text-emerald-600 font-semibold' : 'text-gray-400 dark:text-gray-500' }}">
                                        {{ $sDone }}/{{ $sTotal }}
                                    </span>
                                    @endif
                                    @endunless
                                    @if($s->created_by === auth()->id() || auth()->user()->hasRole('admin'))
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'edit-stage-{{ $s->id }}')"
                                            class="text-xs text-indigo-600 hover:text-indigo-800">Edit</button>
                                        <button type="button" x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'delete-stage-{{ $s->id }}')"
                                            class="text-xs text-red-600 hover:text-red-800">Hapus</button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @unless(auth()->user()->hasRole('asesor'))
                            @if($prodi)
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1 overflow-hidden">
                                <div class="h-1 rounded-full {{ $sPct === 100 ? 'bg-emerald-500' : 'bg-indigo-400' }}"
                                    style="width: {{ $sPct }}%"></div>
                            </div>
                            @endif
                            @endunless
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- RIGHT: Task checklist --}}
            <div class="lg:col-span-8">
                @if($activeStage)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <div class="flex items-start justify-between w-full mb-5">
                        <div>
                            <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $activeStage->title }}</div>
                            @if($activeStage->description)
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $activeStage->description }}</div>
                            @endif
                        </div>
                        @if(auth()->user()->hasRole('auditee') || auth()->user()->hasRole('admin'))
                        <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'create-task')"
                            class="shrink-0 px-3 py-1.5 text-xs font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition-colors">
                            + Tambah Dokumen
                        </button>
                        @endif
                    </div>

                    <div class="space-y-4">
                        @forelse($tasks as $t)
                        @php
                        $prog    = $t->progress->first();  // sudah difilter per prodi di controller
                        $isDone  = $prog?->is_done ?? false;
                        $files   = $t->files;              // sudah difilter per prodi di controller
                        @endphp
                        <div class="border rounded-xl p-4
                            {{ $isDone
                                ? 'bg-emerald-50 border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800/50'
                                : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700' }}">

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
                                        <div class="font-medium text-sm text-gray-800 dark:text-gray-200">{{ $t->title }}</div>
                                        @endif
                                        @if($t->is_required)
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-800 text-white">Wajib</span>
                                        @else
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800/80 text-gray-600 dark:text-gray-400">Opsional</span>
                                        @endif
                                        @if($t->prodi_id)
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">{{ $t->prodi?->name }}</span>
                                        @endif
                                        @if($t->created_by === auth()->id() || auth()->user()->hasRole('admin'))
                                        <div class="ml-2 flex items-center gap-2">
                                            <button type="button" x-data=""
                                                x-on:click="$dispatch('open-modal', 'edit-task-{{ $t->id }}')"
                                                class="text-xs text-indigo-600 hover:text-indigo-800">Edit</button>
                                            <button type="button" x-data=""
                                                x-on:click="$dispatch('open-modal', 'delete-task-{{ $t->id }}')"
                                                class="text-xs text-red-600 hover:text-red-800">Hapus</button>
                                        </div>
                                        @endif
                                    </div>
                                    @if($t->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 whitespace-pre-line">{{ $t->description }}</p>
                                    @endif
                                    @if($isDone && $prog?->done_at)
                                    <div class="text-xs text-emerald-600 mt-1.5">
                                        ✓ Selesai {{ $prog->done_at->format('d M Y H:i') }}
                                        @if($prog->doneBy) · {{ $prog->doneBy->name }} @endif
                                    </div>
                                    @endif
                                </div>

                                {{-- Toggle done (hanya jika prodi dipilih) --}}
                                @unless(auth()->user()->hasRole('asesor'))
                                @if($prodi)
                                <form method="POST" action="{{ route('internal.preparations.toggle', $t) }}" class="shrink-0">
                                    @csrf
                                    <input type="hidden" name="prodi_id" value="{{ $prodi->id }}">
                                    <input type="hidden" name="done" value="{{ $isDone ? 0 : 1 }}">
                                    <button type="submit"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors
                                            {{ $isDone
                                                ? 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50'
                                                : 'bg-indigo-600 border-indigo-600 text-white hover:bg-indigo-700' }}">
                                        {{ $isDone ? 'Buka kembali' : 'Tandai selesai' }}
                                    </button>
                                </form>
                                @endif
                                @endunless
                            </div>

                            {{-- File & Link Section --}}
                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">

                                {{-- File list --}}
                                @if($files->isNotEmpty())
                                <div class="divide-y divide-gray-100 dark:divide-gray-700/60 mb-3">
                                    @foreach($files->sortByDesc('created_at') as $f)
                                    <div class="flex items-center justify-between gap-3 py-2 text-xs">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                            </svg>
                                            <a href="{{ $f->link_url ?: asset("storage/{$f->file_path}") }}" target="_blank"
                                                class="text-indigo-600 dark:text-indigo-400 hover:underline truncate font-medium">
                                                {{ $f->original_name }}
                                            </a>
                                            <span class="text-gray-400 dark:text-gray-500 hidden sm:block flex-shrink-0">
                                                · {{ $f->uploader?->name ?? '—' }} · {{ $f->created_at->format('d/m/Y') }}
                                            </span>
                                        </div>
                                        @if($f->uploaded_by === auth()->id() || auth()->user()->hasRole('admin'))
                                        <form method="POST" action="{{ route('internal.preparations.files.destroy', $f) }}" class="flex-shrink-0">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Hapus file ini?')"
                                                class="text-red-400 hover:text-red-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                {{-- Upload & Link forms (hanya jika prodi dipilih) --}}
                                @unless(auth()->user()->hasRole('asesor'))
                                @if($prodi)
                                <div x-data="{ tab: 'file' }" class="mt-2">
                                    <div class="flex gap-1 mb-2">
                                        {{-- <button type="button" @click="tab = 'file'"
                                            :class="tab === 'file' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:bg-gray-100'"
                                            class="px-3 py-1 rounded-lg text-xs font-medium transition-colors">
                                            Upload File
                                        </button> --}}
                                        {{-- <button type="button" @click="tab = 'link'"
                                            :class="tab === 'link' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:bg-gray-100'"
                                            class="px-3 py-1 rounded-lg text-xs font-medium transition-colors">
                                            Tambah Link
                                        </button> --}}
                                    </div>

                                    {{-- Upload file --}}
                                    {{-- <div x-show="tab === 'file'">
                                        <form method="POST" action="{{ route('internal.preparations.upload', $t) }}"
                                            enctype="multipart/form-data" class="flex items-center gap-2">
                                            @csrf
                                            <input type="hidden" name="prodi_id" value="{{ $prodi->id }}">
                                            <input type="file" name="file" required
                                                class="block w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer" />
                                            <button type="submit"
                                                class="shrink-0 px-3 py-1.5 text-xs font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                                Upload
                                            </button>
                                        </form>
                                        <p class="text-xs text-gray-400 mt-1">PDF, Word, Excel, PPT, gambar, ZIP · maks 10 MB</p>
                                    </div> --}}

                                    {{-- Link URL --}}
                                    <div x-show="tab === 'link'">
                                        <form method="POST" action="{{ route('internal.preparations.storeLink', $t) }}"
                                            class="space-y-2">
                                            @csrf
                                            <input type="hidden" name="prodi_id" value="{{ $prodi->id }}">
                                            <div class="flex items-center gap-2">
                                                <input type="url" name="link_url" required placeholder="https://..."
                                                    class="flex-1 rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900/50 text-xs py-1.5 focus:border-indigo-500 focus:ring-indigo-500" />
                                                <button type="submit"
                                                    class="shrink-0 px-3 py-1.5 text-xs font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                                    Simpan
                                                </button>
                                            </div>
                                            <input type="text" name="link_name" placeholder="Nama link (opsional)"
                                                class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900/50 text-xs py-1.5 focus:border-indigo-500 focus:ring-indigo-500" />
                                        </form>
                                    </div>
                                </div>
                                @endif
                                @endunless
                            </div>
                            {{-- End File Section --}}
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                            <svg class="mx-auto w-8 h-8 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-sm">Belum ada dokumen yang perlu disiapkan untuk tahap ini.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    @if(auth()->user()->hasRole('auditee') || auth()->user()->hasRole('admin'))

    {{-- Modal Create Evidence (Stage) --}}
    <x-modal name="create-stage" :show="false" maxWidth="md">
        <form method="POST" action="{{ route('internal.standard-preparations.stages.store', $standard) }}" class="relative overflow-hidden">
            @csrf
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
            <div class="p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2.5 bg-indigo-50 dark:bg-indigo-900/40 rounded-xl text-indigo-600 dark:text-indigo-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Tambah Evidence</h2>
                </div>
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Judul Evidence <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required placeholder="Contoh: Dokumen Kebijakan SPMI"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900/50 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm transition-all text-sm px-4 py-2.5" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Deskripsi</label>
                        <textarea name="description" rows="3" placeholder="Tuliskan keterangan singkat..."
                            class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900/50 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm transition-all text-sm px-4 py-2.5"></textarea>
                    </div>
                </div>
            </div>
            <div class="px-8 py-5 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all shadow-sm">Batal</button>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-all shadow-md hover:-translate-y-0.5">Simpan</button>
            </div>
        </form>
    </x-modal>

    {{-- Modals Edit & Delete Evidence (Stage) --}}
    @foreach($stages as $s)
    <x-modal name="edit-stage-{{ $s->id }}" :show="false" maxWidth="md">
        <form method="POST" action="{{ route('internal.standard-preparations.stages.update', [$standard, $s]) }}" class="relative overflow-hidden">
            @csrf @method('PUT')
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
            <div class="p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2.5 bg-indigo-50 dark:bg-indigo-900/40 rounded-xl text-indigo-600 dark:text-indigo-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Edit Evidence</h2>
                </div>
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Judul Evidence <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" value="{{ $s->title }}" required
                            class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900/50 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm transition-all text-sm px-4 py-2.5" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Deskripsi</label>
                        <textarea name="description" rows="3"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900/50 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm transition-all text-sm px-4 py-2.5">{{ $s->description }}</textarea>
                    </div>
                </div>
            </div>
            <div class="px-8 py-5 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all shadow-sm">Batal</button>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-all shadow-md hover:-translate-y-0.5">Simpan Perubahan</button>
            </div>
        </form>
    </x-modal>

    <x-modal name="delete-stage-{{ $s->id }}" :show="false" maxWidth="sm">
        <form method="POST" action="{{ route('internal.standard-preparations.stages.destroy', [$standard, $s]) }}" class="relative overflow-hidden">
            @csrf @method('DELETE')
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-rose-500 to-red-500"></div>
            <div class="p-8 text-center">
                <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-rose-50 dark:bg-rose-900/20 mb-5">
                    <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Hapus Evidence?</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Evidence <strong>"{{ $s->title }}"</strong> beserta seluruh dokumen akan dihapus. Tidak dapat dibatalkan.</p>
            </div>
            <div class="px-8 py-5 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex justify-center gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="flex-1 px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all shadow-sm">Batal</button>
                <button type="submit"
                    class="flex-1 px-5 py-2.5 text-sm font-semibold text-white bg-rose-600 rounded-xl hover:bg-rose-700 transition-all shadow-md hover:-translate-y-0.5">Ya, Hapus</button>
            </div>
        </form>
    </x-modal>
    @endforeach

    {{-- Modals Dokumen (Task) --}}
    @if($activeStage)
    <x-modal name="create-task" :show="false" maxWidth="md">
        <form method="POST" action="{{ route('internal.standard-preparations.tasks.store', [$standard, $activeStage]) }}" class="relative overflow-hidden">
            @csrf
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-400 to-teal-500"></div>
            <div class="p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2.5 bg-emerald-50 dark:bg-emerald-900/40 rounded-xl text-emerald-600 dark:text-emerald-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Tambah Dokumen</h2>
                </div>
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Judul Dokumen <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required placeholder="Contoh: SK Rektor"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900/50 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 shadow-sm transition-all text-sm px-4 py-2.5" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Kategori Jenis Dokumen <span class="text-rose-500">*</span></label>
                        <select name="category" required class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900/50 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm px-4 py-2.5">
                            <option value="">Pilih kategori dokumen</option>
                            <option value="kebijakan">Dokumen Kebijakan</option>
                            <option value="pelaksanaan">Dokumen Pelaksanaan</option>
                            <option value="evaluasi">Dokumen Evaluasi</option>
                            <option value="pendukung_digital">Dokumen Pendukung Digital</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Prodi Tujuan</label>
                        <select name="prodi_ids[]" multiple size="4" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900/50 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm px-4 py-2.5">
                            @foreach($prodis as $optionProdi)
                            <option value="{{ $optionProdi->id }}" @selected($prodi?->id === $optionProdi->id || $activeStage->prodi_id === $optionProdi->id)>{{ $optionProdi->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-400">Gunakan Ctrl/Cmd untuk memilih beberapa prodi. Kosongkan untuk semua prodi.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Deskripsi <span class="text-rose-500">*</span></label>
                        <textarea name="description" required rows="3" placeholder="Jelaskan secara singkat..."
                            class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900/50 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 shadow-sm transition-all text-sm px-4 py-2.5"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Link Referensi <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                            </div>
                            <input type="url" name="link" required placeholder="https://..."
                                class="w-full pl-9 rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900/50 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 shadow-sm transition-all text-sm py-2.5" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-8 py-5 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all shadow-sm">Batal</button>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition-all shadow-md hover:-translate-y-0.5">Simpan Dokumen</button>
            </div>
        </form>
    </x-modal>

    @foreach($tasks as $t)
    <x-modal name="edit-task-{{ $t->id }}" :show="false" maxWidth="md">
        <form method="POST" action="{{ route('internal.standard-preparations.tasks.update', [$standard, $activeStage, $t]) }}" class="relative overflow-hidden">
            @csrf @method('PUT')
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-400 to-teal-500"></div>
            <div class="p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2.5 bg-emerald-50 dark:bg-emerald-900/40 rounded-xl text-emerald-600 dark:text-emerald-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Edit Dokumen</h2>
                </div>
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Judul Dokumen <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" value="{{ $t->title }}" required
                            class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900/50 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 shadow-sm transition-all text-sm px-4 py-2.5" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Kategori Jenis Dokumen <span class="text-rose-500">*</span></label>
                        <select name="category" required class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900/50 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm px-4 py-2.5">
                            <option value="kebijakan" @selected($t->category === 'kebijakan')>Dokumen Kebijakan</option>
                            <option value="pelaksanaan" @selected($t->category === 'pelaksanaan')>Dokumen Pelaksanaan</option>
                            <option value="evaluasi" @selected($t->category === 'evaluasi')>Dokumen Evaluasi</option>
                            <option value="pendukung_digital" @selected($t->category === 'pendukung_digital')>Dokumen Pendukung Digital</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Prodi Tujuan</label>
                        <select name="prodi_ids[]" multiple size="4" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900/50 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm px-4 py-2.5">
                            @foreach($prodis as $optionProdi)
                            <option value="{{ $optionProdi->id }}" @selected($t->prodis->contains('id', $optionProdi->id) || ($t->prodis->isEmpty() && $t->prodi_id === $optionProdi->id))>{{ $optionProdi->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-400">Kosongkan untuk semua prodi.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Deskripsi <span class="text-rose-500">*</span></label>
                        <textarea name="description" required rows="3"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900/50 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 shadow-sm transition-all text-sm px-4 py-2.5">{{ $t->description }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Link Referensi <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                            </div>
                            <input type="url" name="link" value="{{ $t->link }}" required placeholder="https://..."
                                class="w-full pl-9 rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900/50 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 shadow-sm transition-all text-sm py-2.5" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-8 py-5 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all shadow-sm">Batal</button>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition-all shadow-md hover:-translate-y-0.5">Simpan Perubahan</button>
            </div>
        </form>
    </x-modal>

    <x-modal name="delete-task-{{ $t->id }}" :show="false" maxWidth="sm">
        <form method="POST" action="{{ route('internal.standard-preparations.tasks.destroy', [$standard, $activeStage, $t]) }}" class="relative overflow-hidden">
            @csrf @method('DELETE')
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-rose-500 to-red-500"></div>
            <div class="p-8 text-center">
                <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-rose-50 dark:bg-rose-900/20 mb-5">
                    <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Hapus Dokumen?</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Dokumen <strong>"{{ $t->title }}"</strong> akan dihapus. Tidak dapat dibatalkan.</p>
            </div>
            <div class="px-8 py-5 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex justify-center gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="flex-1 px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all shadow-sm">Batal</button>
                <button type="submit"
                    class="flex-1 px-5 py-2.5 text-sm font-semibold text-white bg-rose-600 rounded-xl hover:bg-rose-700 transition-all shadow-md hover:-translate-y-0.5">Ya, Hapus</button>
            </div>
        </form>
    </x-modal>
    @endforeach
    @endif
    @endif
</x-app-layout>
