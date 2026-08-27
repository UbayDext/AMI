<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    FOSK — Dokumen Akreditasi
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola dokumen LKPT & LKPD berdasarkan 9 kriteria akreditasi.</p>
            </div>
            <div>
                <select onchange="window.location.href='{{ route('admin.fosk.index') }}?year_id='+this.value"
                    class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($years as $y)
                    <option value="{{ $y->id }}" @selected($y->id == $yearId)>Tahun {{ $y->year }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <x-alert-success :message="session('success')" />

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($criteria as $c)
            @php
            $pct = $c->total_docs > 0 ? round($c->final_docs / $c->total_docs * 100) : 0;
            $colors = [
            'K1' => 'from-violet-500 to-purple-600',
            'K2' => 'from-blue-500 to-cyan-600',
            'K3' => 'from-teal-500 to-emerald-600',
            'K4' => 'from-amber-500 to-orange-600',
            'K5' => 'from-rose-500 to-pink-600',
            'K6' => 'from-indigo-500 to-blue-600',
            'K7' => 'from-emerald-500 to-green-600',
            'K8' => 'from-fuchsia-500 to-purple-600',
            'K9' => 'from-sky-500 to-blue-600',
            ];
            $gradient = $colors[$c->code] ?? 'from-gray-500 to-gray-600';
            @endphp
            <a href="{{ route('admin.fosk.show', ['criteria' => $c->id, 'year_id' => $yearId]) }}"
                class="group block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-lg hover:border-indigo-200 dark:hover:border-indigo-800 transition-all duration-300 hover:-translate-y-0.5">

                {{-- Gradient header --}}
                <div class="h-2 bg-gradient-to-r {{ $gradient }}"></div>

                <div class="p-5">
                    {{-- Code badge + name --}}
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-gradient-to-r {{ $gradient }} text-white shadow-sm">
                                {{ $c->code }}
                            </span>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100 leading-snug group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                {{ $c->name }}
                            </h3>
                        </div>
                        <div class="ml-2 flex-shrink-0">
                            <svg class="w-5 h-5 text-gray-300 dark:text-gray-600 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 mb-3">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ $c->lkpt_count }} LKPT
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ $c->lkpd_count }} LKPD
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $c->final_docs }}/{{ $c->total_docs }} Final
                        </span>
                    </div>

                    {{-- Progress bar --}}
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                        <div class="h-1.5 bg-gradient-to-r {{ $gradient }} rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                    </div>
                    <div class="text-right text-[10px] text-gray-400 dark:text-gray-500 mt-1">{{ $pct }}% selesai</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</x-app-layout>