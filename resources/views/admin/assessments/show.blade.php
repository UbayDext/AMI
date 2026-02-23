<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Assessment Details') }}
            </h2>
            <a href="{{ route('admin.assessments.index') }}" class="mt-4 sm:mt-0 px-4 py-2 bg-gray-100 dark:bg-gray-800/80 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 text-sm font-medium transition">
                &larr; {{ __('Back to List') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Details Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                        Information
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                        Basic details about the assessment.
                    </p>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Accreditation Year
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                                {{ $assessment->accreditationYear->year ?? '-' }}
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Unit Name
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                                {{ $assessment->unit_name }}
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Assessor
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                                {{ $assessment->assessor->name ?? 'Unknown' }}
                                <span class="text-gray-500 dark:text-gray-400 text-xs">({{ $assessment->assessor->email ?? '' }})</span>
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Status
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $assessment->status === 'submitted' ? 'bg-green-100 text-green-800' : 
                                      ($assessment->status === 'draft' ? 'bg-gray-100 dark:bg-gray-800/80 text-gray-800 dark:text-gray-200' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($assessment->status) }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Findings Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                            Findings (Temuan)
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                            List of findings recorded for this assessment.
                        </p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Code</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Standard</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Area</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Severity</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                            @forelse($assessment->findings as $f)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $f->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $f->standard->code ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $f->auditAreaNames }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $f->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $f->severity }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No findings recorded.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Kategori Chart Card -->
            @php
            $kategoriList = ['Sesuai', 'Observasi', 'KTS Minor', 'KTS Mayor', 'OFI'];
            $kategoriCounts = array_fill_keys($kategoriList, 0);
            $kategoriCounts['Tidak Terkategori'] = 0;

            $ketList = [
            'sesuai' => 'Sesuai',
            'sebagian_sesuai' => 'Sebagian Sesuai',
            'tidak_sesuai_bukti_tidak_memadai' => 'Tidak Sesuai – Bukti Tdk Memadai',
            'tidak_sesuai_ada_bukti_tidak_dilaksanakan' => 'Tidak Sesuai – Tdk Dilaksanakan',
            'tidak_sesuai_bukti_tidak_memadai_tidak_konsisten' => 'Tidak Sesuai – Tdk Konsisten',
            'tidak_sesuai_tidak_ada_bukti' => 'Tidak Sesuai – Tdk Ada Bukti',
            'tidak_dilaksanakan_tidak_ada_bukti' => 'Tdk Dilaksanakan – Tdk Ada Bukti',
            ];
            $ketCounts = array_fill_keys(array_values($ketList), 0);

            foreach ($ptks as $ptk) {
            $cat = $ptk->category ?? null;
            if ($cat && isset($kategoriCounts[$cat])) {
            $kategoriCounts[$cat]++;
            } else {
            $kategoriCounts['Tidak Terkategori']++;
            }
            }
            foreach ($answers as $ans) {
            $statusKey = $ans->status ?? null;
            if ($statusKey && isset($ketList[$statusKey])) {
            $ketCounts[$ketList[$statusKey]]++;
            }
            }
            // Remove zero-count entries for cleaner chart
            $kategoriFiltered = array_filter($kategoriCounts, fn($v) => $v > 0);
            $ketFiltered = array_filter($ketCounts, fn($v) => $v > 0);
            $totalAnswered = array_sum($ketCounts);
            $totalPtk = array_sum($kategoriCounts);
            @endphp

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Ringkasan Kategori Jawaban</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Distribusi kategori PTK dan keterangan berdasarkan jawaban asesor.</p>
                </div>

                <div class="px-6 py-6">
                    <!-- Summary Stats Row -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        <div class="bg-indigo-50 dark:bg-indigo-900/40 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-indigo-700 dark:text-indigo-400">{{ $answers->count() }}</div>
                            <div class="text-xs text-indigo-500 mt-1">Total Jawaban</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-700">{{ $kategoriCounts['Sesuai'] ?? 0 }}</div>
                            <div class="text-xs text-green-500 mt-1">Sesuai</div>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-orange-700">{{ ($kategoriCounts['KTS Minor'] ?? 0) + ($kategoriCounts['KTS Mayor'] ?? 0) }}</div>
                            <div class="text-xs text-orange-500 mt-1">KTS (Minor+Mayor)</div>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-red-700">{{ $ptks->count() }}</div>
                            <div class="text-xs text-red-500 mt-1">Total PTK</div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Kategori PTK Chart -->
                        <div class="flex flex-col items-center">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Distribusi Kategori PTK</h4>
                            @if($totalPtk > 0)
                            <div class="relative" style="width:260px;height:260px">
                                <canvas id="chartKategori"></canvas>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-x-6 gap-y-1.5 text-xs w-full max-w-xs">
                                @foreach($kategoriFiltered as $label => $count)
                                @php
                                $dot = match($label) {
                                'Sesuai' => 'bg-green-500',
                                'Observasi' => 'bg-yellow-400',
                                'KTS Minor' => 'bg-orange-500',
                                'KTS Mayor' => 'bg-red-500',
                                'OFI' => 'bg-blue-500',
                                default => 'bg-gray-400',
                                };
                                @endphp
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $dot }} flex-shrink-0"></span>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $label }}: <strong>{{ $count }}</strong></span>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="flex items-center justify-center h-40 text-gray-400 dark:text-gray-500 text-sm italic">Belum ada PTK tercatat.</div>
                            @endif
                        </div>

                        <!-- Keterangan (Status) Chart -->
                        <div class="flex flex-col items-center">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Distribusi Keterangan Jawaban</h4>
                            @if($totalAnswered > 0)
                            <div class="relative" style="width:260px;height:260px">
                                <canvas id="chartKeterangan"></canvas>
                            </div>
                            <div class="mt-4 grid grid-cols-1 gap-y-1.5 text-xs w-full max-w-xs">
                                @foreach($ketFiltered as $label => $count)
                                <div class="flex items-center justify-between gap-2 bg-gray-50 dark:bg-gray-900/50 rounded px-2 py-1">
                                    <span class="text-gray-600 dark:text-gray-400 truncate">{{ $label }}</span>
                                    <span class="font-bold text-gray-800 dark:text-gray-200 flex-shrink-0">{{ $count }}</span>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="flex items-center justify-center h-40 text-gray-400 dark:text-gray-500 text-sm italic">Belum ada jawaban tercatat.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($totalPtk > 0 || $totalAnswered > 0)
            @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // ---- Kategori PTK Chart ----
                    @if($totalPtk > 0)
                    const katData = @json(array_values($kategoriFiltered));
                    const katLabels = @json(array_keys($kategoriFiltered));
                    const katColors = katLabels.map(l => {
                        const map = {
                            'Sesuai': '#22c55e',
                            'Observasi': '#facc15',
                            'KTS Minor': '#f97316',
                            'KTS Mayor': '#ef4444',
                            'OFI': '#3b82f6',
                            'Tidak Terkategori': '#9ca3af',
                        };
                        return map[l] || '#9ca3af';
                    });
                    new Chart(document.getElementById('chartKategori'), {
                        type: 'doughnut',
                        data: {
                            labels: katLabels,
                            datasets: [{
                                data: katData,
                                backgroundColor: katColors,
                                borderWidth: 2,
                                borderColor: '#fff',
                            }]
                        },
                        options: {
                            cutout: '60%',
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: ctx => ` ${ctx.label}: ${ctx.parsed} (${Math.round(ctx.parsed / katData.reduce((a,b)=>a+b,0) * 100)}%)`
                                    }
                                }
                            }
                        }
                    });
                    @endif

                    // ---- Keterangan Chart ----
                    @if($totalAnswered > 0)
                    const ketLabels = @json(array_keys($ketFiltered));
                    const ketData = @json(array_values($ketFiltered));
                    const ketColors = [
                        '#22c55e', '#facc15', '#fb923c', '#f97316', '#ef4444', '#dc2626', '#991b1b'
                    ];
                    new Chart(document.getElementById('chartKeterangan'), {
                        type: 'doughnut',
                        data: {
                            labels: ketLabels,
                            datasets: [{
                                data: ketData,
                                backgroundColor: ketColors.slice(0, ketData.length),
                                borderWidth: 2,
                                borderColor: '#fff',
                            }]
                        },
                        options: {
                            cutout: '60%',
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: ctx => ` ${ctx.label}: ${ctx.parsed} (${Math.round(ctx.parsed / ketData.reduce((a,b)=>a+b,0) * 100)}%)`
                                    }
                                }
                            }
                        }
                    });
                    @endif
                });
            </script>
            @endpush
            @endif

            <!-- Detailed Results (Answers & PTK) -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detailed Assessment Results</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All questions with assessor answers, evidence, and corrective actions (PTK).</p>
                </div>

                @php
                $no = 1;
                $ketLabels = [
                'sesuai' => 'Sesuai',
                'sebagian_sesuai' => 'Sebagian Sesuai',
                'tidak_sesuai_bukti_tidak_memadai' => 'Tidak Sesuai – Bukti tidak memadai',
                'tidak_sesuai_ada_bukti_tidak_dilaksanakan' => 'Tidak Sesuai – Ada bukti tidak dilaksanakan',
                'tidak_sesuai_bukti_tidak_memadai_tidak_konsisten' => 'Tidak Sesuai – Bukti tidak memadai & tidak konsisten',
                'tidak_sesuai_tidak_ada_bukti' => 'Tidak Sesuai – Tidak ada bukti',
                'tidak_dilaksanakan_tidak_ada_bukti' => 'Tidak dilaksanakan – Tidak ada bukti',
                ];
                $ketColors = [
                'sesuai' => 'bg-green-100 text-green-800 border-green-300',
                'sebagian_sesuai' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                'tidak_sesuai_bukti_tidak_memadai' => 'bg-orange-100 text-orange-800 border-orange-300',
                'tidak_sesuai_ada_bukti_tidak_dilaksanakan' => 'bg-orange-100 text-orange-800 border-orange-300',
                'tidak_sesuai_bukti_tidak_memadai_tidak_konsisten' => 'bg-red-100 text-red-800 border-red-300',
                'tidak_sesuai_tidak_ada_bukti' => 'bg-red-500 text-white border-red-700',
                'tidak_dilaksanakan_tidak_ada_bukti' => 'bg-red-500 text-white border-red-700',
                ];
                $catColors = [
                'Sesuai' => 'bg-green-100 text-green-800 border-green-300',
                'Observasi'=> 'bg-yellow-100 text-yellow-800 border-yellow-300',
                'KTS Minor'=> 'bg-orange-100 text-orange-800 border-orange-300',
                'KTS Mayor'=> 'bg-red-500 text-white border-red-700',
                'OFI' => 'bg-blue-100 text-blue-800 border-blue-300',
                ];
                @endphp

                @forelse($groupedQuestions as $categoryId => $items)
                @php
                $cat = $items->first()?->category;
                $catTitle = $cat ? $cat->name : 'Uncategorized';
                $standardGroups = $items->groupBy(fn($q) => $q->standard_id ?? 0);
                @endphp

                <!-- Category Header -->
                <div class="border-b border-gray-300 dark:border-gray-600 bg-gradient-to-r from-indigo-50 to-white px-6 py-3">
                    <h4 class="text-sm font-bold text-indigo-900 dark:text-indigo-200 uppercase tracking-wide">{{ $catTitle }}</h4>
                </div>

                @foreach($standardGroups as $stdId => $stdQuestions)
                @php $std = $stdQuestions->first()->standard; @endphp

                <!-- Standard Sub-header -->
                <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-6 py-2 border-l-4 border-l-indigo-300">
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                        {{ $std?->code ?? 'No Standard' }}
                        @if($std?->name) — {{ $std->name }} @endif
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-100 dark:bg-gray-800/80">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider w-8">No</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider" style="min-width:280px">Pertanyaan</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider" style="min-width:180px">Bukti / Jawaban</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider" style="min-width:180px">Keterangan</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider" style="min-width:260px">Detail PTK</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100">
                            @foreach($stdQuestions as $q)
                            @php
                            $ans = $answers[$q->id] ?? null;
                            $ptk = $ptks[$q->id] ?? null;
                            $ket = $ans?->status ?? null;
                            $ketLabel = $ket ? ($ketLabels[$ket] ?? ucfirst(str_replace('_',' ',$ket))) : '-';
                            $ketColor = $ket ? ($ketColors[$ket] ?? 'bg-gray-100 dark:bg-gray-800/80 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600') : '';
                            $ptkKat = $ptk?->category;
                            $catColor = $catColors[$ptkKat ?? ''] ?? 'bg-gray-100 dark:bg-gray-800/80 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600';
                            $rowBg = match(true) {
                            $ket === 'sesuai' => '',
                            $ket === 'sebagian_sesuai' => 'bg-yellow-50/30',
                            !empty($ket) && str_starts_with($ket, 'tidak') => 'bg-red-50/30',
                            !empty($ket) => 'bg-orange-50/30',
                            default => ''
                            };
                            @endphp
                            <tr class="hover:bg-gray-50 {{ $rowBg }}">
                                {{-- No --}}
                                <td class="px-3 py-3 text-gray-400 dark:text-gray-500 text-center align-top">{{ $no++ }}</td>

                                {{-- Question --}}
                                <td class="px-3 py-3 text-gray-900 dark:text-gray-100 align-top">
                                    <div class="font-medium leading-snug">{{ $q->label }}</div>
                                    @if($q->reference)
                                    <div class="text-gray-400 dark:text-gray-500 mt-1">Ref: {{ $q->reference }}</div>
                                    @endif
                                </td>

                                {{-- Bukti --}}
                                <td class="px-3 py-3 text-gray-700 dark:text-gray-300 align-top">
                                    @if($ans?->value_text)
                                    <div>{{ $ans->value_text }}</div>
                                    @else
                                    <span class="text-gray-300 italic">—</span>
                                    @endif
                                    @if($ans?->file_path)
                                    <a href="{{ Storage::url($ans->file_path) }}" target="_blank" class="mt-1 inline-flex items-center gap-1 text-indigo-600 hover:underline">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        Lihat File
                                    </a>
                                    @endif
                                    @if($ans?->reason)
                                    <div class="mt-1 text-gray-500 dark:text-gray-400 italic border-t border-gray-100 dark:border-gray-700 pt-1">{{ $ans->reason }}</div>
                                    @endif
                                </td>

                                {{-- Keterangan --}}
                                <td class="px-3 py-3 align-top">
                                    @if($ket)
                                    <span class="inline-flex px-2 py-1 rounded border font-semibold text-[10px] leading-tight {{ $ketColor }}">
                                        {{ $ketLabel }}
                                    </span>
                                    @else
                                    <span class="text-gray-300 italic">Belum diisi</span>
                                    @endif
                                </td>

                                {{-- PTK Detail --}}
                                <td class="px-3 py-3 align-top">
                                    @if($ptk)
                                    <div class="space-y-1.5">
                                        {{-- Kategori badge --}}
                                        <div>
                                            <span class="inline-flex px-2 py-0.5 rounded border text-[10px] font-bold {{ $catColor }}">
                                                {{ $ptkKat ?? '-' }}
                                            </span>
                                        </div>
                                        @if($ptk->condition_desc)
                                        <div><span class="font-semibold text-gray-500 dark:text-gray-400">Kondisi:</span> {{ $ptk->condition_desc }}</div>
                                        @endif
                                        @if($ptk->root_cause)
                                        <div><span class="font-semibold text-gray-500 dark:text-gray-400">Akar:</span> {{ $ptk->root_cause }}</div>
                                        @endif
                                        @if($ptk->recommendation)
                                        <div><span class="font-semibold text-gray-500 dark:text-gray-400">Rekom:</span> {{ $ptk->recommendation }}</div>
                                        @endif
                                        @if($ptk->corrective_plan)
                                        <div><span class="font-semibold text-gray-500 dark:text-gray-400">Plan:</span> {{ $ptk->corrective_plan }}</div>
                                        @endif
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @if($ptk->start_date)
                                            <span class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 text-[10px]">
                                                Start: {{ $ptk->start_date->format('d/m/Y') }}
                                            </span>
                                            @endif
                                            @if($ptk->end_date)
                                            <span class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 text-[10px]">
                                                End: {{ $ptk->end_date->format('d/m/Y') }}
                                            </span>
                                            @endif
                                            @php
                                            $tlColor = match($ptk->tl_status) {
                                            'Close' => 'bg-green-100 text-green-800 border-green-200',
                                            'Open' => 'bg-red-100 text-red-800 border-red-200',
                                            default => 'bg-orange-100 text-orange-800 border-orange-200'
                                            };
                                            @endphp
                                            <span class="px-1.5 py-0.5 rounded border text-[10px] font-bold {{ $tlColor }}">
                                                {{ $ptk->tl_status ?? 'Open' }}
                                            </span>
                                        </div>
                                    </div>
                                    @else
                                    <span class="text-gray-300 italic">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endforeach
                @empty
                <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No questions found for this assessment.</div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>