<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Assessment Details') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('assessor.assessments.report', $assessment) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Report
                </a>
                <a href="{{ route('assessor.assessments.fill', $assessment) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Continue Filling
                </a>
                <a href="{{ route('assessor.assessments.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="p-4 bg-green-100 border border-green-200 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
            @endif

            <!-- Assessment Info -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Information</h3>
                </div>
                <div class="px-6 py-5 bg-gray-50 dark:bg-gray-900/50">
                    <dl class="grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unit Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-semibold">{{ $assessment->unit_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Accreditation Year</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $assessment->accreditationYear->year ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $assessment->status === 'submitted' ? 'bg-green-100 text-green-800' : 'bg-gray-100 dark:bg-gray-800/80 text-gray-800 dark:text-gray-200' }}">
                                    {{ ucfirst($assessment->status) }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Assessment Responses Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-6 py-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Assessment Responses</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Menampilkan seluruh pertanyaan beserta bukti dan keterangan.</p>
                </div>

                @php
                $no = 1;

                $ketLabels = [
                'sesuai' => 'Sesuai',
                'sebagian_sesuai' => 'Sebagian Sesuai',
                'tidak_sesuai_bukti_tidak_memadai' => 'Tidak Sesuai - Bukti tidak memadai',
                'tidak_sesuai_ada_bukti_tidak_dilaksanakan' => 'Tidak Sesuai - Ada bukti - Tidak dilaksanakan',
                'tidak_sesuai_bukti_tidak_memadai_tidak_konsisten' => 'Tidak Sesuai - Bukti tidak memadai - Tidak konsisten',
                'tidak_sesuai_tidak_ada_bukti' => 'Tidak Sesuai - Tidak ada bukti',
                'tidak_dilaksanakan_tidak_ada_bukti' => 'Tidak dilaksanakan - Tidak ada bukti',
                ];

                $categoryColors = [
                'Sesuai' => 'bg-green-100 text-green-800 border-green-300 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-800',
                'Observasi' => 'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-800',
                'KTS Minor' => 'bg-orange-100 text-orange-800 border-orange-300 dark:bg-orange-900/40 dark:text-orange-300 dark:border-orange-800',
                'KTS Mayor' => 'bg-red-500 text-white border-red-700 dark:bg-red-900/60 dark:text-red-200 dark:border-red-800',
                'OFI' => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-800',
                ];
                @endphp

                @forelse($groupedQuestions as $categoryId => $items)
                @php
                $cat = $items->first()?->category;
                $catTitle = $cat ? $cat->name : 'Uncategorized';
                @endphp

                <!-- Category Header -->
                <div class="border-b border-gray-300 dark:border-gray-600 bg-gradient-to-r from-indigo-50 to-white dark:from-indigo-900/40 dark:to-gray-800 px-6 py-3">
                    <h4 class="text-sm font-bold text-indigo-900 dark:text-indigo-200 uppercase tracking-wide">
                        {{ $catTitle }}
                    </h4>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-800/80">
                            <tr>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap w-10">No</th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider" style="min-width: 250px;">Pertanyaan</th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider" style="min-width: 100px;">Referensi</th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider" style="min-width: 200px;">Bukti</th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider" style="min-width: 150px;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($items as $q)
                            @php
                            $id = $q->id;
                            $ans = $answers[$id] ?? null;
                            $ptk = $ptks[$id] ?? null;

                            $ket = $ans?->status ?? 'sesuai';
                            $bukti = $ans?->value_text;
                            $ketLabel = $ketLabels[$ket] ?? ucfirst(str_replace('_', ' ', $ket));

                            // Detect category
                            $ptkKategori = $ptk?->category;
                            if (empty($ptkKategori) && !empty($ket)) {
                            if ($ket === 'sesuai') {
                            $ptkKategori = 'Sesuai';
                            } elseif ($ket === 'sebagian_sesuai') {
                            $ptkKategori = 'Observasi';
                            } elseif (in_array($ket, ['tidak_sesuai_tidak_ada_bukti', 'tidak_dilaksanakan_tidak_ada_bukti'])) {
                            $ptkKategori = 'KTS Mayor';
                            } elseif (str_starts_with($ket, 'tidak')) {
                            $ptkKategori = 'KTS Minor';
                            }
                            }

                            $categoryColor = $categoryColors[$ptkKategori ?? ''] ?? 'bg-gray-100 dark:bg-gray-800/80 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600';
                            $isSesuai = $ket === 'sesuai';
                            $showPts = !$isSesuai && $ptkKategori !== 'Sesuai';

                            // PTS data
                            $pts = $ptk?->pts;

                            // Auto-generate codes for PTS
                            $kodeStandar = $ptk?->standard?->code ?? '-';
                            $kodeAreaParts = [];
                            if ($ptk && !empty($ptk->audit_area_ids)) {
                            foreach ($ptk->audit_area_ids as $areaId) {
                            if ($areas->has($areaId)) {
                            $kodeAreaParts[] = $areas[$areaId]->code ?? $areas[$areaId]->name;
                            }
                            }
                            }
                            $kodeArea = !empty($kodeAreaParts) ? implode(', ', $kodeAreaParts) : '-';
                            $kodePtk = ($kodeStandar !== '-' && $kodeArea !== '-') ? $kodeStandar . '/' . implode('-', $kodeAreaParts) : '-';
                            @endphp

                            <!-- Main Row -->
                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-3 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap align-top text-center border-r">
                                    {{ $no }}
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-800 dark:text-gray-200 align-top border-r">
                                    <div class="font-semibold">{{ $q->label ?? $q->text }}</div>
                                    @if($q->standard)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <span class="font-semibold">{{ $q->standard->code }}</span>
                                        @if($q->standard->name) - {{ $q->standard->name }} @endif
                                    </div>
                                    @endif
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-600 dark:text-gray-400 align-top border-r">
                                    {{ $q->reference ?? '-' }}
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-800 dark:text-gray-200 align-top border-r">
                                    @if($bukti)
                                    <div>{{ $bukti }}</div>
                                    @else
                                    <span class="text-gray-400 dark:text-gray-500 italic">-</span>
                                    @endif
                                    @if ($ans?->file_path)
                                    <div class="text-xs mt-2">
                                        <a href="{{ asset('storage/' . $ans->file_path) }}" target="_blank" class="text-indigo-600 hover:underline flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            {{ basename($ans->file_path) }}
                                        </a>
                                    </div>
                                    @endif
                                </td>
                                <td class="px-3 py-4 align-top">
                                    <span class="inline-flex px-3 py-1.5 text-xs font-semibold rounded-md border {{ $categoryColor }}">
                                        {{ $ketLabel }}
                                    </span>
                                </td>
                            </tr>

                            {{-- PTK Detail Row (only for non-sesuai) --}}
                            @if(!$isSesuai && $ptk)
                            <tr class="border-b border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50">
                                <td colspan="5" class="px-4 py-4">
                                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                                        <h5 class="text-sm font-bold text-red-800 mb-3 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                            Detail PTK (Temuan)
                                        </h5>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Area Audit</label>
                                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                                        @if(!empty($ptk->audit_area_ids))
                                                        @foreach($ptk->audit_area_ids as $areaId)
                                                        @if($areas->has($areaId))
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 mr-1 mb-1">{{ $areas[$areaId]->name }}</span>
                                                        @endif
                                                        @endforeach
                                                        @else
                                                        <span class="text-gray-400 dark:text-gray-500 italic">-</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Deskripsi Kondisi</label>
                                                    <div class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-900/50 rounded p-2 border border-gray-100 dark:border-gray-700 min-h-[2rem]">{{ $ptk->condition_desc ?: '-' }}</div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Akibat</label>
                                                    <div class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-900/50 rounded p-2 border border-gray-100 dark:border-gray-700 min-h-[2rem]">{{ $ptk->impact ?: '-' }}</div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Kategori</label>
                                                    <span class="inline-flex px-3 py-1.5 text-xs font-semibold rounded-md border {{ $categoryColor }}">{{ $ptkKategori ?? '-' }}</span>
                                                </div>
                                            </div>
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Akar Penyebab</label>
                                                    <div class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-900/50 rounded p-2 border border-gray-100 dark:border-gray-700 min-h-[2rem]">{{ $ptk->root_cause ?: '-' }}</div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Rekomendasi</label>
                                                    <div class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-900/50 rounded p-2 border border-gray-100 dark:border-gray-700 min-h-[2rem]">{{ $ptk->recommendation ?: '-' }}</div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Rencana Perbaikan (Auditee)</label>
                                                    <div class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-900/50 rounded p-2 border border-gray-100 dark:border-gray-700 min-h-[2rem]">{{ $ptk->corrective_plan ?: '-' }}</div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Start Date</label>
                                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $ptk->start_date ? $ptk->start_date->format('d/m/Y') : '-' }}</div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">End Date</label>
                                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $ptk->end_date ? $ptk->end_date->format('d/m/Y') : '-' }}</div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Realisasi (Tindak Lanjut)</label>
                                                    <div class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-900/50 rounded p-2 border border-gray-100 dark:border-gray-700 min-h-[2rem]">{{ $ptk->realisasi ?: '-' }}</div>
                                                </div>
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Efektifitas</label>
                                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $ptk->efektifitas ?: '-' }}</div>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-md border
                                                                {{ $ptk->tl_status === 'Close' ? 'bg-teal-100 text-teal-800 border-teal-200 dark:bg-teal-900/40 dark:text-teal-300 dark:border-teal-800' :
                                                                  ($ptk->tl_status === 'Open' ? 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/40 dark:text-yellow-300 dark:border-yellow-800' :
                                                                  ($ptk->tl_status === 'Toleran' ? 'bg-orange-100 text-orange-800 border-orange-200 dark:bg-orange-900/40 dark:text-orange-300 dark:border-orange-800' : 'bg-gray-100 dark:bg-gray-800/80 text-gray-800 dark:text-gray-200 border-gray-200 dark:border-gray-700')) }}">
                                                            {{ $ptk->tl_status ?: '-' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endif

                            @php $no++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @empty
                <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                    <p>No questions available for this assessment.</p>
                </div>
                @endforelse
            </div>

            {{-- ============================================================ --}}
            {{-- PTS (AUDIT TINDAK LANJUT) SECTION - FM-AMI/06 --}}
            {{-- Only for items where keterangan != sesuai AND kategori != Sesuai --}}
            {{-- ============================================================ --}}
            @php
            // Collect all PTKs that qualify for PTS
            $ptsItems = collect();
            foreach ($groupedQuestions as $categoryId => $items) {
            foreach ($items as $q) {
            $qid = $q->id;
            $ans = $answers[$qid] ?? null;
            $ptk = $ptks[$qid] ?? null;
            $ket = $ans?->status ?? 'sesuai';

            $ptkKat = $ptk?->category;
            if (empty($ptkKat) && !empty($ket)) {
            if ($ket === 'sesuai') $ptkKat = 'Sesuai';
            elseif ($ket === 'sebagian_sesuai') $ptkKat = 'Observasi';
            elseif (in_array($ket, ['tidak_sesuai_tidak_ada_bukti', 'tidak_dilaksanakan_tidak_ada_bukti'])) $ptkKat = 'KTS Mayor';
            elseif (str_starts_with($ket, 'tidak')) $ptkKat = 'KTS Minor';
            }

            if ($ket !== 'sesuai' && $ptkKat !== 'Sesuai' && $ptk) {
            $ptsItems->push([
            'question' => $q,
            'ptk' => $ptk,
            'kategori' => $ptkKat,
            ]);
            }
            }
            }
            @endphp

            @if($ptsItems->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-emerald-50 to-white dark:from-emerald-900/40 dark:to-gray-800 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Audit Tindak Lanjut (FM-AMI/06)
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Formulir pemantauan tindak lanjut untuk temuan yang tidak sesuai.</p>
                </div>

                <form method="POST" action="{{ route('assessor.assessments.pts.update', $assessment) }}">
                    @csrf
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-800/80">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap" style="min-width: 200px;">Realisasi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap" style="min-width: 130px;">Efektifitas</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap" style="min-width: 110px;">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" colspan="3">Kode Auto Input</th>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-900/50">
                                    <th colspan="3"></th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">Kode Standar</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">Kode Area</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">Kode PTK</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($ptsItems as $item)
                                @php
                                $ptk = $item['ptk'];
                                $pts = $ptk->pts;
                                $ptkId = $ptk->id;

                                // Auto-generate codes
                                $kodeStandar = $ptk->standard?->code ?? '-';
                                $kodeAreaParts = [];
                                if (!empty($ptk->audit_area_ids)) {
                                foreach ($ptk->audit_area_ids as $areaId) {
                                if ($areas->has($areaId)) {
                                $kodeAreaParts[] = $areas[$areaId]->code ?? $areas[$areaId]->name;
                                }
                                }
                                }
                                $kodeArea = !empty($kodeAreaParts) ? implode(', ', $kodeAreaParts) : '-';
                                $kodePtk = ($kodeStandar !== '-' || !empty($kodeAreaParts))
                                ? $kodeStandar . '/' . implode('-', $kodeAreaParts)
                                : '-';
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    {{-- Realisasi --}}
                                    <td class="px-4 py-3 align-top border-r">
                                        <textarea
                                            name="pts_realisasi_{{ $ptkId }}"
                                            rows="2"
                                            class="block w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs"
                                            placeholder="Isi realisasi...">{{ $pts?->realisasi }}</textarea>
                                    </td>

                                    {{-- Efektifitas --}}
                                    <td class="px-4 py-3 align-top border-r">
                                        <select
                                            name="pts_efektifitas_{{ $ptkId }}"
                                            class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs font-semibold
                                                {{ ($pts?->efektifitas === 'Efektif') ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : (($pts?->efektifitas === 'Belum Efektif') ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : '') }}">
                                            <option class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" value="">-- Pilih --</option>
                                            <option class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" value="Efektif" @selected($pts?->efektifitas === 'Efektif')>Efektif</option>
                                            <option class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" value="Belum Efektif" @selected($pts?->efektifitas === 'Belum Efektif')>Belum Efektif</option>
                                        </select>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-4 py-3 align-top border-r">
                                        <select
                                            name="pts_status_{{ $ptkId }}"
                                            class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs font-semibold
                                                {{ ($pts?->status === 'Close') ? 'bg-emerald-600 text-white' : 'bg-gray-100 dark:bg-gray-800/80 text-gray-700 dark:text-gray-300' }}">
                                            <option class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" value="Open" @selected(($pts?->status ?? 'Open') === 'Open')>Open</option>
                                            <option class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" value="Close" @selected($pts?->status === 'Close')>Close</option>
                                        </select>
                                    </td>

                                    {{-- Kode Standar (auto) --}}
                                    <td class="px-4 py-3 align-top text-sm text-gray-700 dark:text-gray-300 font-mono border-r">
                                        {{ $kodeStandar }}
                                    </td>

                                    {{-- Kode Area (auto) --}}
                                    <td class="px-4 py-3 align-top text-sm text-gray-700 dark:text-gray-300 font-mono border-r">
                                        {{ $kodeArea }}
                                    </td>

                                    {{-- Kode PTK (auto) --}}
                                    <td class="px-4 py-3 align-top text-sm text-gray-700 dark:text-gray-300 font-mono font-semibold">
                                        {{ $kodePtk }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end">
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan PTS
                        </button>
                    </div>
                </form>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>