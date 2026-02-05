<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl">Persiapan Dokumen Internal</h2>
                <p class="text-sm text-gray-600">
                    Upload dokumen + checklist progres per tahap.
                </p>
            </div>

            <div class="flex items-center gap-2">
                {{-- Filter tahun (opsional kalau kamu pakai query ?year=ID) --}}
                <form method="GET" action="{{ route('admin.preparations.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="stage" value="{{ request('stage') }}">
                    <input
                        name="year"
                        value="{{ request('year') }}"
                        placeholder="Year ID (opsional)"
                        class="border rounded px-3 py-2 text-sm w-40"
                    />
                    <button class="px-3 py-2 text-sm border rounded">Terapkan</button>
                    <a href="{{ route('admin.preparations.index') }}" class="px-3 py-2 text-sm border rounded">Reset</a>
                </form>
            </div>
        </div>
    </x-slot>

    @php
        $active = $activeStage ?? null;
        $tasks = $active?->tasks?->sortBy('sort_order') ?? collect();

        $totalTasks = $total ?? $tasks->count();
        $doneTasks  = $done ?? $tasks->where('is_done', true)->count();
        $percent    = $totalTasks > 0 ? (int) round(($doneTasks / $totalTasks) * 100) : 0;

        // dokumen terbaru dari semua task pada stage aktif (untuk preview)
        $latestFile = $tasks
            ->flatMap(fn($t) => $t->files ?? collect())
            ->sortByDesc('created_at')
            ->first();

        $previewUrl = $latestFile ? asset('storage/' . $latestFile->file_path) : null;
        $previewName = $latestFile?->original_name;
        $previewMime = $latestFile?->mime_type;

        $isPdf = $previewMime ? str_contains($previewMime, 'pdf') : ($previewUrl ? str_ends_with(strtolower($previewUrl), '.pdf') : false);
        $isImage = $previewMime ? str_starts_with($previewMime, 'image/') : false;
    @endphp

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- LEFT: checklist --}}
            <div class="lg:col-span-5 space-y-4">
                {{-- Stage selector --}}
                <div class="bg-white rounded shadow p-5">
                    <div class="flex items-center justify-between">
                        <div class="font-semibold">Tahap Persiapan</div>
                        <div class="text-sm text-gray-600">
                            {{ $doneTasks }}/{{ $totalTasks }} selesai
                        </div>
                    </div>

                    <div class="mt-3 w-full bg-gray-200 rounded h-2 overflow-hidden">
                        <div class="h-2 bg-emerald-500" style="width: {{ $percent }}%"></div>
                    </div>
                    <div class="mt-2 text-xs text-gray-600">{{ $percent }}%</div>

                    <div class="mt-4 space-y-2">
                        @forelse($stages as $s)
                            @php
                                $isActive = $active && $active->id === $s->id;
                                $linkParams = array_filter([
                                    'year' => request('year'),
                                    'stage' => $s->id,
                                ]);
                            @endphp

                            <a
                                href="{{ route('internal.preparations.index', $linkParams) }}"
                                class="block border rounded p-3 hover:bg-gray-50 {{ $isActive ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200' }}"
                            >
                                <div class="flex items-center justify-between gap-3">
                                    <div class="font-medium">
                                        {{ $s->title }}
                                    </div>
                                    @if($isActive)
                                        <span class="text-xs px-2 py-1 rounded bg-emerald-600 text-white">Aktif</span>
                                    @endif
                                </div>
                                @if($s->description)
                                    <div class="text-sm text-gray-600 mt-1 line-clamp-2">
                                        {{ $s->description }}
                                    </div>
                                @endif
                            </a>
                        @empty
                            <div class="text-sm text-gray-500">Belum ada tahap. Tambahkan data stage dulu.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Task list --}}
                <div class="bg-white rounded shadow p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="font-semibold">{{ $active?->title ?? 'Pilih tahap' }}</div>
                            @if($active?->description)
                                <div class="text-sm text-gray-600 mt-1">{{ $active->description }}</div>
                            @endif
                        </div>
                        @if (session('success'))
                            <div class="text-xs px-3 py-2 rounded bg-emerald-100 text-emerald-800">
                                {{ session('success') }}
                            </div>
                        @endif
                    </div>

                    @if ($errors->any())
                        <div class="mt-3 p-3 rounded bg-red-50 text-red-700 text-sm">
                            <b>Validasi gagal:</b>
                            <ul class="list-disc ml-5 mt-1">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mt-4 space-y-3">
                        @forelse($tasks as $t)
                            <div class="border rounded p-4 {{ $t->is_done ? 'bg-emerald-50 border-emerald-200' : 'bg-white border-gray-200' }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <div class="font-medium">{{ $t->title }}</div>
                                            @if($t->is_required)
                                                <span class="text-xs px-2 py-1 rounded bg-gray-900 text-white">Wajib</span>
                                            @else
                                                <span class="text-xs px-2 py-1 rounded bg-gray-200 text-gray-700">Opsional</span>
                                            @endif
                                        </div>

                                        @if($t->description)
                                            <div class="text-sm text-gray-600 mt-1 whitespace-pre-line">{{ $t->description }}</div>
                                        @endif

                                        @if($t->is_done && $t->done_at)
                                            <div class="text-xs text-emerald-800 mt-2">
                                                Selesai: {{ $t->done_at->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Toggle done --}}
                                    <form method="POST" action="{{ route('internal.preparations.toggle', $t) }}" class="shrink-0">
                                        @csrf
                                        <input type="hidden" name="done" value="{{ $t->is_done ? 0 : 1 }}">
                                        <button
                                            class="px-3 py-2 text-sm rounded border {{ $t->is_done ? 'bg-white' : 'bg-gray-900 text-white border-gray-900' }}"
                                            type="submit"
                                        >
                                            {{ $t->is_done ? 'Buka' : 'Selesai' }}
                                        </button>
                                    </form>
                                </div>

                                {{-- Upload --}}
                                <div class="mt-3">
                                    <form method="POST" action="{{ route('internal.preparations.upload', $t) }}" enctype="multipart/form-data" class="flex items-center gap-2">
                                        @csrf
                                        <input
                                            type="file"
                                            name="file"
                                            class="block w-full text-sm border rounded p-2 bg-white"
                                            required
                                        />
                                        <button class="px-3 py-2 text-sm bg-emerald-600 text-white rounded">
                                            Upload
                                        </button>
                                    </form>
                                    <div class="text-xs text-gray-500 mt-1">Maks 10MB. PDF/Doc/Image boleh.</div>
                                </div>

                                {{-- File list --}}
                                <div class="mt-3 space-y-2">
                                    @forelse(($t->files ?? collect())->sortByDesc('created_at') as $f)
                                        <div class="flex items-center justify-between gap-3 text-sm border rounded p-2 bg-white">
                                            <div class="min-w-0">
                                                <a
                                                    href="{{ asset('storage/' . $f->file_path) }}"
                                                    target="_blank"
                                                    class="underline break-all"
                                                >
                                                    {{ $f->original_name }}
                                                </a>
                                                <div class="text-xs text-gray-500">
                                                    {{ optional($f->created_at)->format('d/m/Y H:i') }}
                                                </div>
                                            </div>

                                            <form method="POST" action="{{ route('internal.preparations.files.destroy', $f) }}" onsubmit="return confirm('Hapus file ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="px-3 py-2 text-xs rounded border bg-white hover:bg-gray-50">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    @empty
                                        <div class="text-sm text-gray-400">Belum ada file.</div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500">Tidak ada task untuk tahap ini.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- RIGHT: preview --}}
            <div class="lg:col-span-7">
                <div class="bg-white rounded shadow p-5 lg:sticky lg:top-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="font-semibold">Preview Dokumen</div>
                            <div class="text-sm text-gray-600">
                                Menampilkan dokumen terakhir yang diupload pada tahap aktif.
                            </div>
                        </div>

                        @if($previewUrl)
                            <a href="{{ $previewUrl }}" target="_blank" class="px-3 py-2 text-sm border rounded">
                                Buka Tab Baru
                            </a>
                        @endif
                    </div>

                    <div class="mt-4">
                        @if(!$previewUrl)
                            <div class="p-6 border rounded bg-gray-50 text-gray-600 text-sm">
                                Belum ada dokumen untuk dipreview. Upload file pada salah satu task.
                            </div>
                        @else
                            <div class="text-sm mb-3">
                                <div class="font-medium break-all">{{ $previewName }}</div>
                                <div class="text-xs text-gray-500">{{ $previewMime }}</div>
                            </div>

                            <div class="border rounded overflow-hidden bg-white" style="height: 640px;">
                                @if($isPdf)
                                    <iframe src="{{ $previewUrl }}" class="w-full h-full"></iframe>
                                @elseif($isImage)
                                    <img src="{{ $previewUrl }}" class="w-full h-full object-contain" />
                                @else
                                    <div class="p-6 text-sm text-gray-700">
                                        Preview untuk tipe file ini tidak didukung.
                                        <div class="mt-2">
                                            <a href="{{ $previewUrl }}" target="_blank" class="underline">
                                                Klik untuk download / buka file
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- info kecil --}}
                    <div class="mt-4 text-xs text-gray-500">
                        Tips: Upload file di task manapun → preview otomatis update ke file terbaru.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
