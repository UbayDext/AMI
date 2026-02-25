<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Persiapan Data per Standar</h2>
            <p class="text-[13px] text-gray-500 dark:text-gray-400 mt-1">Lihat dan upload dokumen persiapan untuk setiap standar.</p>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if($standards->isEmpty())
        <x-card class="text-center py-12">
            <x-empty-state icon="folder" title="Belum ada standar" subtitle="Buat standar terlebih dahulu untuk memulai persiapan data." />
        </x-card>
        @else
        @php
        $globalTotalTasks = 0;
        $globalDoneTasks = 0;
        foreach($standards as $s) {
        $stageTasks = $s->preparationStages->flatMap(fn($st) => $st->tasks);
        $globalTotalTasks += $stageTasks->count();
        $globalDoneTasks += $stageTasks->where('is_done', true)->count();
        }
        $globalPct = $globalTotalTasks > 0 ? (int) round($globalDoneTasks / $globalTotalTasks * 100) : 0;
        @endphp

        {{-- Global Progress Banner --}}
        <div class="mb-6 relative overflow-hidden bg-indigo-600 rounded-2xl p-6 md:p-8 flex items-center justify-between shadow-sm">
            <div class="relative z-10 text-white">
                <div class="text-indigo-100 text-[13px] font-medium mb-1.5">Progress Keseluruhan</div>
                <div class="text-4xl sm:text-5xl font-bold mb-2 tracking-tight">{{ $globalPct }}%</div>
                <div class="text-indigo-100 text-[13px] font-medium">{{ $globalDoneTasks }} dari {{ $globalTotalTasks }} task selesai</div>
            </div>
            <div class="relative z-10">
                <svg class="w-20 h-20 sm:w-28 sm:h-28 transform -rotate-90" aria-hidden="true">
                    {{-- r=32 / 44 --}}
                    <circle class="text-white/20" stroke-width="8" stroke="currentColor" fill="transparent" r="44" cx="56" cy="56" style="transform-origin: center; transform: scale(0.8);" />
                    <circle class="text-white drop-shadow-md" stroke-width="8" stroke-dasharray="276" stroke-dashoffset="{{ 276 - (276 * $globalPct) / 100 }}" stroke-linecap="round" stroke="currentColor" fill="transparent" r="44" cx="56" cy="56" style="transform-origin: center; transform: scale(0.8); transition: stroke-dashoffset 1s ease-in-out;" />
                </svg>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($standards as $s)
            @php
            $allTasks = $s->preparationStages->flatMap(fn($st) => $st->tasks);
            $total = $allTasks->count();
            $done = $allTasks->where('is_done', true)->count();
            $pct = $total > 0 ? (int) round($done / $total * 100) : 0;
            @endphp
            <a href="{{ route('admin.standard-preparations.index', $s) }}"
                class="group flex flex-col bg-white dark:bg-[#151b2b] rounded-[18px] border border-gray-100 dark:border-gray-800 p-6 hover:border-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/5 transition-all duration-300">
                <div class="flex-1">
                    <div class="flex items-start justify-between gap-3 mb-6">
                        <div class="flex-shrink-0 w-11 h-11 rounded-[14px] bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center font-bold text-indigo-600 dark:text-indigo-400 text-sm group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/50 transition-colors">
                            {{ $s->code }}
                        </div>
                        <span class="text-[13px] font-medium {{ $pct === 100 ? 'text-emerald-600 dark:text-emerald-500' : ($pct > 0 ? 'text-amber-600 dark:text-amber-500' : 'text-gray-400 dark:text-gray-500') }}">
                            {{ $pct === 100 ? 'Selesai' : ($pct > 0 ? 'Proses (' . $pct . '%)' : 'Belum mulai') }}
                        </span>
                    </div>

                    <div class="font-semibold text-gray-900 dark:text-gray-100 text-[15px] leading-relaxed mb-1">{{ $s->name }}</div>
                    <div class="text-[13px] text-gray-500 dark:text-gray-400 mb-6">{{ $done }}/{{ $total }} dokumen selesai</div>
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
</x-app-layout>