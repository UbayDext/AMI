<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Persiapan Data per Standar</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Lihat dan upload dokumen persiapan untuk setiap standar.</p>
            </div>

            {{-- Prodi Checklist Filter --}}
            @if($prodis->isNotEmpty())
            <form method="GET" action="{{ route('internal.standard-preparations.index') }}" id="prodi-filter-form"
                class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">Filter Prodi:</span>

                {{-- Semua Prodi --}}
                <label class="inline-flex items-center gap-1.5 cursor-pointer select-none group">
                    <span class="relative flex items-center justify-center w-4 h-4">
                        <input type="radio" name="prodi" value=""
                            {{ !$prodi ? 'checked' : '' }}
                            onchange="this.form.submit()"
                            class="peer sr-only">
                        <span class="w-4 h-4 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                            peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-colors">
                        </span>
                        <svg class="absolute w-2.5 h-2.5 text-white pointer-events-none opacity-0 peer-checked:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <span class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors {{ !$prodi ? 'font-semibold text-indigo-600 dark:text-indigo-400' : '' }}">
                        Semua Prodi
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
                            peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-colors">
                        </span>
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

        @if($standards->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center text-gray-400 dark:text-gray-500">
            <svg class="mx-auto w-10 h-10 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <p class="text-sm">Belum ada standar yang tersedia.</p>
        </div>
        @else

        @php
        $globalTotal = $standards->sum('total_tasks');
        $globalDone  = $standards->sum('done_tasks');
        $globalPct   = $globalTotal > 0 ? (int) round($globalDone / $globalTotal * 100) : 0;
        @endphp

        @unless(auth()->user()->hasRole('asesor'))
        @if($prodi)
        <div class="mb-6 bg-gradient-to-r from-indigo-500 to-indigo-700 rounded-2xl p-5 text-white flex items-center justify-between gap-4">
            <div>
                <div class="text-sm font-medium opacity-80">
                    Progress Keseluruhan{{ $prodi ? ' — ' . $prodi->name : '' }}
                </div>
                <div class="text-3xl font-bold mt-1">{{ $globalPct }}%</div>
                <div class="text-sm opacity-70 mt-0.5">{{ $globalDone }} dari {{ $globalTotal }} task selesai</div>
            </div>
            <div class="w-24 h-24 flex-shrink-0">
                <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                    <circle cx="18" cy="18" r="15.9155" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="3.5" />
                    <circle cx="18" cy="18" r="15.9155" fill="none" stroke="white" stroke-width="3.5"
                        stroke-dasharray="{{ $globalPct }}, 100" stroke-linecap="round" />
                </svg>
            </div>
        </div>
        @endif
        @endunless

        @if(!$prodi && !auth()->user()->hasRole('asesor'))
        <div class="mb-4 px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/60 text-amber-700 dark:text-amber-400 rounded-xl text-sm">
            Pilih prodi di atas untuk melihat dan mengelola progress per prodi.
        </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($standards as $s)
            @php
            $showRoute = route('internal.standard-preparations.show', $s) . ($prodi ? '?prodi=' . $prodi->id : '');
            @endphp
            <a href="{{ $showRoute }}"
                class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 hover:shadow-md hover:border-indigo-200 transition-all duration-200">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/40 flex items-center justify-center font-bold text-indigo-700 dark:text-indigo-400 text-sm group-hover:bg-indigo-100 transition-colors">
                        {{ $s->code }}
                    </div>
                    @unless(auth()->user()->hasRole('asesor'))
                    @if($prodi)
                    <span class="text-xs font-medium px-2 py-1 rounded-full
                        {{ $s->percent === 100 ? 'bg-emerald-100 text-emerald-700'
                            : ($s->percent > 0 ? 'bg-amber-100 text-amber-700'
                            : 'bg-gray-100 dark:bg-gray-800/80 text-gray-500 dark:text-gray-400') }}">
                        {{ $s->percent === 100 ? 'Selesai' : ($s->percent > 0 ? "Proses ({$s->percent}%)" : 'Belum mulai') }}
                    </span>
                    @endif
                    @endunless
                </div>

                <p class="font-semibold text-sm text-gray-800 dark:text-gray-200 leading-snug line-clamp-2 mb-1">{{ $s->name }}</p>

                @unless(auth()->user()->hasRole('asesor'))
                @if($prodi)
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">
                    {{ $s->done_tasks }}/{{ $s->total_tasks }} dokumen selesai
                </p>
                <div class="w-full bg-gray-100 dark:bg-gray-800/80 rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full transition-all duration-500
                        {{ $s->percent === 100 ? 'bg-emerald-500' : 'bg-indigo-500' }}"
                        style="width: {{ $s->percent }}%"></div>
                </div>
                @else
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">{{ $s->total_tasks }} dokumen tersedia</p>
                @endif
                @endunless

                <div class="mt-3 text-xs font-medium text-indigo-600 group-hover:text-indigo-800 flex items-center gap-1">
                    Lihat checklist
                    <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</x-app-layout>
