<x-app-layout>
    <x-slot name="header">
        <div data-onboarding-standard="intro" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Persiapan Data per Standar</h2>
                <p class="text-[13px] text-gray-500 dark:text-gray-400 mt-1">Lihat dan upload dokumen persiapan untuk setiap standar.</p>
            </div>

            {{-- Prodi Selector --}}
            <div class="flex flex-wrap items-center gap-2"><button type="button" onclick="window.dispatchEvent(new CustomEvent('restart-standard-onboarding'))" class="rounded-xl border border-indigo-200 px-4 py-2 text-xs font-bold text-indigo-600 dark:border-indigo-800 dark:text-indigo-400">Lihat Panduan</button>@if($prodis->isNotEmpty())
            <form data-onboarding-standard="prodi-filter" method="GET" action="{{ route('admin.standard-preparations.landing') }}" class="flex items-center gap-2">
                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">Filter Prodi:</label>
                <select name="prodi" onchange="this.form.submit()"
                    class="rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm py-1.5 pr-8 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">— Semua Prodi —</option>
                    @foreach($prodis as $p)
                    <option value="{{ $p->id }}" {{ $prodi?->id === $p->id ? 'selected' : '' }}>
                        {{ $p->name }}
                    </option>
                    @endforeach
                </select>
            </form>
            @endif</div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if($standards->isEmpty())
        <x-card class="text-center py-12">
            <x-empty-state icon="folder" title="Belum ada standar" subtitle="Buat standar terlebih dahulu untuk memulai persiapan data." />
        </x-card>
        @else

        @php
        $globalTotal = $standards->sum('total_tasks');
        $globalDone  = $standards->sum('done_tasks');
        $globalPct   = $globalTotal > 0 ? (int) round($globalDone / $globalTotal * 100) : 0;
        @endphp

        @if($prodi)
        <div data-onboarding-standard="progress" class="mb-6 relative overflow-hidden bg-indigo-600 rounded-2xl p-6 md:p-8 flex items-center justify-between shadow-sm">
            <div class="relative z-10 text-white">
                <div class="text-indigo-100 text-[13px] font-medium mb-1.5">
                    Progress Keseluruhan — {{ $prodi->name }}
                </div>
                <div class="text-4xl sm:text-5xl font-bold mb-2 tracking-tight">{{ $globalPct }}%</div>
                <div class="text-indigo-100 text-[13px] font-medium">{{ $globalDone }} dari {{ $globalTotal }} task selesai</div>
            </div>
            <div class="relative z-10">
                <svg class="w-20 h-20 sm:w-28 sm:h-28 transform -rotate-90" aria-hidden="true">
                    <circle class="text-white/20" stroke-width="8" stroke="currentColor" fill="transparent" r="44" cx="56" cy="56" style="transform-origin: center; transform: scale(0.8);" />
                    <circle class="text-white drop-shadow-md" stroke-width="8"
                        stroke-dasharray="276" stroke-dashoffset="{{ 276 - (276 * $globalPct) / 100 }}"
                        stroke-linecap="round" stroke="currentColor" fill="transparent" r="44" cx="56" cy="56"
                        style="transform-origin: center; transform: scale(0.8); transition: stroke-dashoffset 1s ease-in-out;" />
                </svg>
            </div>
        </div>
        @else
        <div data-onboarding-standard="progress" class="mb-4 px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/60 text-amber-700 dark:text-amber-400 rounded-xl text-sm">
            Pilih prodi di atas untuk melihat progress per prodi. Template task dapat dikelola tanpa memilih prodi.
        </div>
        @endif

        <div data-onboarding-standard="standard-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($standards as $s)
            @php
            $showUrl = route('admin.standard-preparations.index', $s) . ($prodi ? '?prodi=' . $prodi->id : '');
            @endphp
            <a href="{{ $showUrl }}"
                class="group flex flex-col bg-white dark:bg-[#151b2b] rounded-[18px] border border-gray-100 dark:border-gray-800 p-6 hover:border-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/5 transition-all duration-300">
                <div class="flex-1">
                    <div class="flex items-start justify-between gap-3 mb-6">
                        <div class="flex-shrink-0 w-11 h-11 rounded-[14px] bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center font-bold text-indigo-600 dark:text-indigo-400 text-sm group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/50 transition-colors">
                            {{ $s->code }}
                        </div>
                        @if($prodi)
                        <span class="text-[13px] font-medium {{ $s->percent === 100 ? 'text-emerald-600 dark:text-emerald-500' : ($s->percent > 0 ? 'text-amber-600 dark:text-amber-500' : 'text-gray-400 dark:text-gray-500') }}">
                            {{ $s->percent === 100 ? 'Selesai' : ($s->percent > 0 ? 'Proses (' . $s->percent . '%)' : 'Belum mulai') }}
                        </span>
                        @endif
                    </div>

                    <div class="font-semibold text-gray-900 dark:text-gray-100 text-[15px] leading-relaxed mb-1">{{ $s->name }}</div>
                    <div class="text-[13px] text-gray-500 dark:text-gray-400 mb-6">
                        @if($prodi)
                            {{ $s->done_tasks }}/{{ $s->total_tasks }} dokumen selesai
                        @else
                            {{ $s->total_tasks }} dokumen tersedia
                        @endif
                    </div>

                    @if($prodi && $s->total_tasks > 0)
                    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 mb-4 overflow-hidden">
                        <div class="h-1.5 rounded-full transition-all duration-500
                            {{ $s->percent === 100 ? 'bg-emerald-500' : 'bg-indigo-500' }}"
                            style="width: {{ $s->percent }}%"></div>
                    </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-gray-100/50 dark:border-gray-800/50">
                    <span class="text-[13px] font-medium text-indigo-600 dark:text-indigo-400 flex items-center gap-1 group-hover:gap-2 transition-all">
                        Lihat checklist
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>
    @if(auth()->user()->hasRole('admin'))
        @push('scripts')
            @include('admin.standard-preparations.partials.onboarding')
        @endpush
    @endif
</x-app-layout>
