<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Persiapan Data per Standar</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Klik standar untuk melihat & mengelola daftar persiapan dokumen.</p>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if($standards->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-10 text-center text-gray-400 dark:text-gray-500">
            <svg class="mx-auto w-10 h-10 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <p class="text-sm">Belum ada standar. Buat standar terlebih dahulu.</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($standards as $s)
            @php
            $allTasks = $s->preparationStages->flatMap(fn($st) => $st->tasks);
            $total = $allTasks->count();
            $done = $allTasks->where('is_done', true)->count();
            $pct = $total > 0 ? (int) round($done / $total * 100) : 0;
            $stageCount = $s->preparationStages->count();
            @endphp
            <a href="{{ route('admin.standard-preparations.index', $s) }}"
                class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 hover:shadow-md hover:border-indigo-200 transition-all duration-200">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/40 flex items-center justify-center font-bold text-indigo-600 text-sm group-hover:bg-indigo-100 transition-colors">
                        {{ $s->code }}
                    </div>
                    <span class="text-xs font-medium px-2 py-1 rounded-full
                        {{ $pct === 100 ? 'bg-emerald-100 text-emerald-700' : ($pct > 0 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 dark:bg-gray-800/80 text-gray-500 dark:text-gray-400') }}">
                        {{ $pct === 100 ? '✓ Selesai' : ($pct > 0 ? "{$pct}%" : 'Belum') }}
                    </span>
                </div>

                <div class="font-semibold text-gray-800 dark:text-gray-200 text-sm leading-snug mb-1 line-clamp-2">{{ $s->name }}</div>
                <div class="text-xs text-gray-400 dark:text-gray-500 mb-3">{{ $stageCount }} tahap · {{ $done }}/{{ $total }} task selesai</div>

                <div class="w-full bg-gray-100 dark:bg-gray-800/80 rounded-full h-1.5 overflow-hidden">
                    <div class="h-1.5 rounded-full {{ $pct === 100 ? 'bg-emerald-500' : 'bg-indigo-500' }} transition-all"
                        style="width: {{ $pct }}%"></div>
                </div>
            </a>
            @endforeach
        </div>
        @endif

    </div>
</x-app-layout>