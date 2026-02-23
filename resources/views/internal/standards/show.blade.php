<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <a href="{{ route('internal.standard-preparations.index') }}" class="text-sm text-indigo-600 hover:underline">
                        ← Semua Standar
                    </a>
                </div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                    {{ $standard->code }}: {{ $standard->name }}
                </h2>
                @unless(auth()->user()->hasRole('asesor'))
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Persiapan dokumen · {{ $done }}/{{ $total }} selesai ({{ $percent }}%)</p>
                @endunless
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm">
            {{ session('success') }}
        </div>
        @endif
        @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
            <ul class="list-disc ml-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        @if($stages->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center text-gray-400 dark:text-gray-500">
            <svg class="mx-auto w-10 h-10 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <p class="text-sm">Belum ada tahap persiapan untuk standar ini.</p>
            <p class="text-xs mt-1">Hubungi admin untuk menambahkan tahap.</p>
        </div>
        @else

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- LEFT: Stage list --}}
            <div class="lg:col-span-4 space-y-3">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
                    <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Tahap Persiapan</div>

                    @unless(auth()->user()->hasRole('asesor'))
                    {{-- Overall progress --}}
                    <div class="mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                            <span>Progres keseluruhan</span>
                            <span class="font-medium">{{ $percent }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-800/80 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full {{ $percent === 100 ? 'bg-emerald-500' : 'bg-indigo-500' }} transition-all"
                                style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                    @endunless

                    <div class="space-y-2">
                        @foreach($stages as $s)
                        @php
                        $isActive = $activeStage && $activeStage->id === $s->id;
                        $sDone = $s->tasks->where('is_done', true)->count();
                        $sTotal = $s->tasks->count();
                        $sPct = $sTotal > 0 ? (int) round($sDone / $sTotal * 100) : 0;
                        @endphp
                        <a href="{{ route('internal.standard-preparations.show', [$standard, 'stage' => $s->id]) }}"
                            class="block rounded-xl p-3 border transition-colors
                               {{ $isActive ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/40' : 'border-gray-100 dark:border-gray-700 hover:bg-gray-50' }}">
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="text-sm font-medium {{ $isActive ? 'text-indigo-700 dark:text-indigo-400' : 'text-gray-800 dark:text-gray-200' }}">
                                    {{ $s->title }}
                                </div>
                                @unless(auth()->user()->hasRole('asesor'))
                                <span class="text-xs {{ $sPct === 100 ? 'text-emerald-600 font-semibold' : 'text-gray-400 dark:text-gray-500' }}">
                                    {{ $sDone }}/{{ $sTotal }}
                                </span>
                                @endunless
                            </div>
                            @unless(auth()->user()->hasRole('asesor'))
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1 overflow-hidden">
                                <div class="h-1 rounded-full {{ $sPct === 100 ? 'bg-emerald-500' : 'bg-indigo-400' }}"
                                    style="width: {{ $sPct }}%"></div>
                            </div>
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
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $activeStage->title }}</div>
                            @if($activeStage->description)
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $activeStage->description }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-4">
                        @forelse($tasks as $t)
                        <div class="border rounded-xl p-4 {{ $t->is_done ? 'bg-emerald-50 border-emerald-200' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <div class="font-medium text-sm text-gray-800 dark:text-gray-200">{{ $t->title }}</div>
                                        @if($t->is_required)
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-800 text-white">Wajib</span>
                                        @else
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800/80 text-gray-600 dark:text-gray-400">Opsional</span>
                                        @endif
                                    </div>
                                    @if($t->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 whitespace-pre-line">{{ $t->description }}</p>
                                    @endif
                                    @if($t->is_done && $t->done_at)
                                    <div class="text-xs text-emerald-600 mt-1.5">
                                        ✓ Selesai {{ $t->done_at->format('d M Y H:i') }}
                                    </div>
                                    @endif
                                </div>

                                {{-- Toggle done --}}
                                @unless(auth()->user()->hasRole('asesor'))
                                <form method="POST" action="{{ route('internal.preparations.toggle', $t) }}" class="shrink-0">
                                    @csrf
                                    <input type="hidden" name="done" value="{{ $t->is_done ? 0 : 1 }}">
                                    <button type="submit"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors
                                            {{ $t->is_done
                                                ? 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50'
                                                : 'bg-indigo-600 border-indigo-600 text-white hover:bg-indigo-700' }}">
                                        {{ $t->is_done ? 'Buka kembali' : 'Tandai selesai' }}
                                    </button>
                                </form>
                                @endunless
                            </div>

                            {{-- Upload / Link section --}}
                            @unless(auth()->user()->hasRole('asesor'))
                            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                @if(auth()->user()->hasRole('standar'))
                                {{-- Standar role: submit a link --}}
                                <form method="POST" action="{{ route('internal.preparations.storeLink', $t) }}"
                                    class="space-y-2">
                                    @csrf
                                    <input type="url" name="link_url" required
                                        placeholder="https://docs.google.com/..."
                                        class="block w-full text-xs border border-gray-200 dark:border-gray-700 rounded-lg p-2 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                                    <input type="text" name="link_name"
                                        placeholder="Nama / keterangan link (opsional)"
                                        class="block w-full text-xs border border-gray-200 dark:border-gray-700 rounded-lg p-2 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                                    <button type="submit"
                                        class="px-3 py-2 text-xs font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                        Simpan Link
                                    </button>
                                </form>
                                @else
                                {{-- Admin / Asesor: file upload --}}
                                <form method="POST" action="{{ route('internal.preparations.upload', $t) }}"
                                    enctype="multipart/form-data" class="flex items-center gap-2">
                                    @csrf
                                    <input type="file" name="file" required
                                        class="block flex-1 text-xs border border-gray-200 dark:border-gray-700 rounded-lg p-2 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                                    <button type="submit"
                                        class="flex-shrink-0 px-3 py-2 text-xs font-medium bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                                        Upload
                                    </button>
                                </form>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Maks 10MB (PDF, Doc, Image)</p>
                                @endif
                            </div>
                            @endunless

                            {{-- File / Link list --}}
                            @if($t->files->isNotEmpty())
                            <div class="mt-3 space-y-2">
                                @foreach($t->files->sortByDesc('created_at') as $f)
                                <div class="flex items-center justify-between gap-3 border border-gray-100 dark:border-gray-700 rounded-lg p-2 bg-white dark:bg-gray-800 text-xs">
                                    <div class="min-w-0 flex items-center gap-1.5">
                                        @if($f->link_url)
                                        <svg class="w-3.5 h-3.5 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                        <a href="{{ $f->link_url }}" target="_blank"
                                            class="text-indigo-600 hover:underline truncate block max-w-xs">
                                            {{ $f->original_name }}
                                        </a>
                                        @else
                                        <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <a href="{{ asset('storage/'.$f->file_path) }}" target="_blank"
                                            class="text-indigo-600 hover:underline truncate block max-w-xs">
                                            {{ $f->original_name }}
                                        </a>
                                        @endif
                                        <span class="text-gray-400 dark:text-gray-500 ml-1">{{ optional($f->created_at)->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @unless(auth()->user()->hasRole('asesor'))
                                    <form method="POST" action="{{ route('internal.preparations.files.destroy', $f) }}"
                                        onsubmit="return confirm('Hapus ini?')">
                                        @csrf @method('DELETE')
                                        <button class="px-2 py-1 border rounded text-gray-500 dark:text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                    @endunless
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                            <svg class="mx-auto w-8 h-8 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
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
</x-app-layout>