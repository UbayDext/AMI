<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('assessor.ami.index') }}" class="text-sm text-indigo-600 hover:underline">← Kembali</a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 mt-1">
                Review: {{ $review->submission?->standard?->code }} — {{ $review->submission?->prodi?->name }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $review->submission?->cycle?->title }}</p>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <x-alert-success :message="session('success')" />

        @php
            $isCompleted = $review->status === 'completed';
            $categoryConfig = [
                'kebijakan'         => ['label' => 'Kebijakan',   'badge' => 'bg-blue-100 text-blue-700'],
                'pelaksanaan'       => ['label' => 'Pelaksanaan', 'badge' => 'bg-emerald-100 text-emerald-700'],
                'evaluasi'          => ['label' => 'Evaluasi',    'badge' => 'bg-amber-100 text-amber-700'],
                'pendukung_digital' => ['label' => 'Digital',     'badge' => 'bg-purple-100 text-purple-700'],
            ];
        @endphp

        {{-- Findings panel --}}
        @php
            $kodeStandar = $review->submission?->standard?->code ?? '-';
            $kodeArea    = $review->submission?->prodi?->name ?? '-';
            $kodePtk     = $kodeStandar . '/' . $kodeArea;
        @endphp
        <x-card padding="p-5" class="mb-5">
            <div class="flex items-center justify-between mb-4">
                <div class="font-semibold text-sm text-gray-800 dark:text-gray-200">Temuan / PTK</div>
                @if(!$isCompleted)
                <span class="text-xs text-gray-400">Tambah temuan di masing-masing pertanyaan di bawah</span>
                @endif
            </div>
            @forelse($review->findings as $f)
            <div class="mb-4 border border-red-200 dark:border-red-800/50 rounded-xl overflow-hidden" x-data="{ openAtl: false }">
                {{-- Finding header --}}
                <div class="flex items-start gap-3 p-3 bg-red-50 dark:bg-red-900/20">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-red-200 dark:bg-red-800 text-red-800 dark:text-red-200">{{ $f->category }}</span>
                            @if($f->tl_status)
                            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded
                                {{ $f->tl_status === 'Close' ? 'bg-teal-100 text-teal-700' : ($f->tl_status === 'On Progress' ? 'bg-blue-100 text-blue-700' : ($f->tl_status === 'Open' ? 'bg-yellow-100 text-yellow-700' : 'bg-orange-100 text-orange-700')) }}">
                                {{ $f->tl_status }}
                            </span>
                            @endif
                            @if($f->efektifitas)
                            <span class="text-[10px] text-gray-500">{{ $f->efektifitas }}</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-700 dark:text-gray-300 mt-1">{{ $f->condition_desc }}</div>
                        @if($f->root_cause) <div class="text-[10px] text-gray-500 mt-0.5">Akar: {{ $f->root_cause }}</div> @endif
                        @if($f->recommendation) <div class="text-[10px] text-gray-400 mt-0.5">→ {{ $f->recommendation }}</div> @endif
                        <div class="text-[10px] text-gray-400 mt-1 flex gap-3">
                            @if($f->pic) <span>PIC: <strong>{{ $f->pic }}</strong></span> @endif
                            @if($f->due_date) <span>Target: {{ $f->due_date->format('d M Y') }}</span> @endif
                        </div>
                        @if($f->realisasi)
                        <div class="mt-1 text-[10px] text-emerald-700 dark:text-emerald-400">Realisasi: {{ $f->realisasi }}</div>
                        @endif
                    </div>
                    <div class="flex gap-1 flex-shrink-0">
                        <button type="button" @click="openAtl = !openAtl"
                            class="text-xs px-2 py-1 rounded bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400 hover:bg-teal-200 transition-colors">
                            ATL
                        </button>
                        @if(!$isCompleted)
                        <form method="POST" action="{{ route('assessor.ami.findings.destroy', [$review, $f]) }}" onsubmit="return confirm('Hapus temuan?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                {{-- ATL form (FM-AMI/06) --}}
                <div x-show="openAtl" x-transition class="border-t border-teal-200 dark:border-teal-800/50">
                    <form method="POST" action="{{ route('assessor.ami.findings.atl', [$review, $f]) }}"
                        class="p-4 bg-white dark:bg-gray-800/50">
                        @csrf @method('PATCH')
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <span class="text-sm font-semibold text-teal-700 dark:text-teal-400">Audit Tindak Lanjut (FM-AMI/06)</span>
                        </div>

                        <div class="overflow-x-auto mb-3">
                            <table class="min-w-full border border-gray-200 dark:border-gray-700 text-xs">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-900/50">
                                        <th colspan="3" class="px-3 py-2 border-b border-gray-200 dark:border-gray-700"></th>
                                        <th colspan="3" class="px-3 py-2 text-center text-[10px] font-semibold text-gray-500 uppercase border-b border-l border-gray-200 dark:border-gray-700">Kode Auto Input</th>
                                    </tr>
                                    <tr class="bg-gray-100 dark:bg-gray-800">
                                        <th class="px-3 py-2 text-left font-medium text-gray-600 dark:text-gray-400 uppercase border border-gray-200 dark:border-gray-700 w-1/4">Realisasi</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-600 dark:text-gray-400 uppercase border border-gray-200 dark:border-gray-700 w-28">Efektifitas</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-600 dark:text-gray-400 uppercase border border-gray-200 dark:border-gray-700 w-28">Status</th>
                                        <th class="px-3 py-2 text-center font-medium text-gray-600 dark:text-gray-400 uppercase border border-gray-200 dark:border-gray-700 w-20">Kode Std</th>
                                        <th class="px-3 py-2 text-center font-medium text-gray-600 dark:text-gray-400 uppercase border border-gray-200 dark:border-gray-700 w-24">Kode Area</th>
                                        <th class="px-3 py-2 text-center font-medium text-gray-600 dark:text-gray-400 uppercase border border-gray-200 dark:border-gray-700 w-28">Kode PTK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="px-2 py-2 border border-gray-200 dark:border-gray-700 align-top">
                                            <textarea name="realisasi" rows="2" placeholder="Realisasi tindak lanjut..."
                                                class="block w-full text-xs dark:bg-gray-900 dark:text-gray-300 border-0 focus:ring-0 resize-none">{{ $f->realisasi }}</textarea>
                                        </td>
                                        <td class="px-2 py-2 border border-gray-200 dark:border-gray-700 align-top">
                                            <select name="efektifitas" class="block w-full text-xs dark:bg-gray-900 dark:text-gray-300 border-0 focus:ring-0">
                                                <option value="">-- Pilih --</option>
                                                <option value="Efektif"       @selected($f->efektifitas==='Efektif')>Efektif</option>
                                                <option value="Kurang"        @selected($f->efektifitas==='Kurang')>Kurang</option>
                                                <option value="Tidak Efektif" @selected($f->efektifitas==='Tidak Efektif')>Tidak Efektif</option>
                                            </select>
                                        </td>
                                        <td class="px-2 py-2 border border-gray-200 dark:border-gray-700 align-top">
                                            <select name="tl_status"
                                                class="atl-tl-status block w-full text-xs font-semibold border-0 focus:ring-0
                                                    {{ $f->tl_status === 'Close' ? 'bg-teal-100 text-teal-700' : ($f->tl_status === 'On Progress' ? 'bg-blue-100 text-blue-700' : ($f->tl_status === 'Open' ? 'bg-yellow-100 text-yellow-700' : ($f->tl_status === 'Toleran' ? 'bg-orange-100 text-orange-700' : 'dark:bg-gray-900 dark:text-gray-300'))) }}"
                                                onchange="styleAtlStatus(this)">
                                                <option value="">-- Pilih --</option>
                                                <option value="Open"        @selected($f->tl_status==='Open')>Open</option>
                                                <option value="On Progress" @selected($f->tl_status==='On Progress')>On Progress</option>
                                                <option value="Close"       @selected($f->tl_status==='Close')>Close</option>
                                                <option value="Toleran"     @selected($f->tl_status==='Toleran')>Toleran</option>
                                            </select>
                                        </td>
                                        <td class="px-2 py-2 border border-gray-200 dark:border-gray-700 text-center align-middle">
                                            <span class="font-mono font-semibold text-gray-700 dark:text-gray-300">{{ $kodeStandar }}</span>
                                        </td>
                                        <td class="px-2 py-2 border border-gray-200 dark:border-gray-700 text-center align-middle">
                                            <span class="font-mono font-semibold text-gray-700 dark:text-gray-300">{{ $kodeArea }}</span>
                                        </td>
                                        <td class="px-2 py-2 border border-gray-200 dark:border-gray-700 text-center align-middle">
                                            <span class="font-mono font-semibold text-indigo-700 dark:text-indigo-400">{{ $kodePtk }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <label class="text-[10px] font-semibold text-gray-500 uppercase">Penanggung Jawab (PIC)</label>
                                <select name="pic" class="mt-0.5 block w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded text-xs px-2 py-1.5">
                                    <option value="">-- Pilih Penanggung Jawab --</option>
                                    @foreach($users as $u)
                                    <option value="{{ $u->name }}" @selected($f->pic === $u->name)>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-shrink-0 self-end">
                                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors">
                                    Simpan ATL
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400">Belum ada temuan.</p>
            @endforelse
        </x-card>

        {{-- Question table --}}
        <form method="POST" action="{{ route('assessor.ami.answers.save', $review) }}">
            @csrf

            @forelse($questions as $q)
            @php
                $ans        = $answersMap[$q->id] ?? null;
                $refs       = $refMap[$q->id] ?? collect();
                $qEvMap     = $evidenceMap[$q->id] ?? collect();
                $totalBukti = $qEvMap->flatten()->count();
                $auditeeAns = $auditeeStatuses[$q->id] ?? null;
                $amiLabels = [
                    'sesuai'                                    => 'Sesuai',
                    'sebagian'                                  => 'Sebagian Sesuai',
                    'tidak_bukti_tidak_memadai'                 => 'Tidak Sesuai – Bukti tidak memadai',
                    'tidak_ada_bukti_tidak_dilaksanakan'        => 'Tidak Sesuai – Ada bukti – Tidak dilaksanakan',
                    'tidak_bukti_tidak_memadai_tidak_konsisten' => 'Tidak Sesuai – Bukti tidak memadai – Tidak konsisten',
                    'tidak_tidak_ada_bukti'                     => 'Tidak Sesuai – Tidak ada bukti',
                    'tidak_dilaksanakan_tidak_ada_bukti'        => 'Tidak dilaksanakan – Tidak ada bukti',
                    'belum_diperiksa'                           => 'Belum Diperiksa',
                ];
                $curAns = $ans?->status ?? 'belum_diperiksa';
                $sc = match(true) {
                    $curAns === 'sesuai'               => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                    $curAns === 'sebagian'             => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                    str_starts_with($curAns, 'tidak')  => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                    default                            => 'bg-gray-100 text-gray-500',
                };
                $sl = $amiLabels[$curAns] ?? 'Belum Diperiksa';
                $amiStatusLabels = [
                    'sesuai'                                    => 'Sesuai',
                    'sebagian'                                  => 'Sebagian Sesuai',
                    'tidak_bukti_tidak_memadai'                 => 'Tidak Sesuai – Bukti tidak memadai',
                    'tidak_ada_bukti_tidak_dilaksanakan'        => 'Tidak Sesuai – Ada bukti – Tidak dilaksanakan',
                    'tidak_bukti_tidak_memadai_tidak_konsisten' => 'Tidak Sesuai – Bukti tidak memadai – Tidak konsisten',
                    'tidak_tidak_ada_bukti'                     => 'Tidak Sesuai – Tidak ada bukti',
                    'tidak_dilaksanakan_tidak_ada_bukti'        => 'Tidak dilaksanakan – Tidak ada bukti',
                ];
                $auditeeStatus = $auditeeAns['status'] ?? '';
                $auditeeScMap  = match(true) {
                    $auditeeStatus === 'sesuai'                  => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                    $auditeeStatus === 'sebagian'                => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                    str_starts_with($auditeeStatus, 'tidak')     => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                    default                                       => 'bg-gray-100 text-gray-500',
                };
            @endphp
            <div class="mb-4" x-data="{ open: true, showFinding: {{ ($curAns !== 'sesuai' && $curAns !== 'belum_diperiksa') ? 'true' : 'false' }} }">
                <x-card padding="p-0">

                    {{-- Header pertanyaan --}}
                    <button type="button" @click="open = !open"
                        class="w-full flex items-start gap-3 px-5 py-4 text-left hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors border-b border-gray-100 dark:border-gray-700">
                        <span class="flex-shrink-0 w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 text-xs font-bold flex items-center justify-center mt-0.5">
                            {{ $q->question_number }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 leading-snug">{{ $q->question_text }}</p>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @if($q->bidang) <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-500 rounded">{{ $q->bidang }}</span> @endif
                                @if($q->auditi) <span class="text-[10px] px-1.5 py-0.5 bg-indigo-50 text-indigo-600 rounded">{{ $q->auditi }}</span> @endif
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-medium {{ $sc }}">{{ $sl }}</span>
                                <span class="text-[10px] {{ $refs->isNotEmpty() ? 'text-blue-500' : 'text-gray-400' }}">{{ $refs->count() }} ref</span>
                                <span class="text-[10px] {{ $totalBukti > 0 ? 'text-emerald-500' : 'text-gray-400' }}">{{ $totalBukti }} bukti</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform mt-1.5 flex-shrink-0" :class="open ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-transition>
                        {{-- 3-column grid: REFERENSI | BUKTI | KETERANGAN --}}
                        <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-gray-100 dark:divide-gray-700">

                            {{-- REFERENSI --}}
                            <div class="p-4">
                                <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Referensi</div>
                                @forelse($refs as $ref)
                                <div class="flex items-start gap-1.5 mb-1.5">
                                    <svg class="w-3 h-3 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    <div class="min-w-0">
                                        <a href="{{ $ref->url }}" target="_blank" rel="noopener"
                                            class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline block truncate">{{ $ref->title }}</a>
                                        @if($ref->notes)
                                        <p class="text-[10px] text-gray-400">{{ $ref->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <p class="text-xs text-gray-400 italic">Belum ada referensi dari auditee.</p>
                                @endforelse
                            </div>

                            {{-- BUKTI --}}
                            <div class="p-4">
                                <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Bukti</div>
                                @if($totalBukti === 0)
                                <p class="text-xs text-red-400 italic">Auditee belum melampirkan bukti.</p>
                                @else
                                <div class="space-y-2">
                                    @foreach($categoryConfig as $catKey => $catCfg)
                                    @php $catEvs = $qEvMap[$catKey] ?? collect(); @endphp
                                    @if($catEvs->isNotEmpty())
                                    <div>
                                        <span class="inline-block text-[10px] px-2 py-0.5 rounded-full font-medium mb-1 {{ $catCfg['badge'] }}">
                                            {{ $catCfg['label'] }}
                                        </span>
                                        @foreach($catEvs as $ev)
                                        <a href="{{ $ev->display_url }}" target="_blank" rel="noopener"
                                            class="flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400 hover:underline mb-0.5 ml-1">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            <span class="truncate">{{ $ev->display_name }}</span>
                                        </a>
                                        @endforeach
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            {{-- KETERANGAN (verdict + finding) --}}
                            <div class="p-4 space-y-3">
                                <div>
                                    <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Keterangan</div>

                                    {{-- Self-assessment auditee (read-only referensi) --}}
                                    @if($auditeeAns)
                                    <div class="mb-2 p-2 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700">
                                        <div class="text-[10px] text-gray-400 mb-1">Self-assessment auditee:</div>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded font-medium {{ $auditeeScMap }}">
                                            {{ $amiStatusLabels[$auditeeAns['status']] ?? $auditeeAns['status'] }}
                                        </span>
                                        @if($auditeeAns['notes'] ?? null)
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 italic">{{ $auditeeAns['notes'] }}</p>
                                        @endif
                                    </div>
                                    @endif

                                    @if(!$isCompleted)
                                    <select name="answers[{{ $q->id }}][status]"
                                        class="ami-review-ket w-full border rounded-lg px-3 py-2 text-sm mb-2 focus:ring-2 focus:ring-indigo-300 focus:outline-none"
                                        data-qid="{{ $q->id }}">
                                        <option value="belum_diperiksa" @selected($curAns==='belum_diperiksa')>— Belum Diperiksa —</option>
                                        <option value="sesuai"                                    @selected($curAns==='sesuai')>✓ Sesuai</option>
                                        <option value="sebagian"                                  @selected($curAns==='sebagian')>◑ Sebagian Sesuai</option>
                                        <option value="tidak_bukti_tidak_memadai"                 @selected($curAns==='tidak_bukti_tidak_memadai')>✗ Tidak Sesuai – Bukti tidak memadai</option>
                                        <option value="tidak_ada_bukti_tidak_dilaksanakan"        @selected($curAns==='tidak_ada_bukti_tidak_dilaksanakan')>✗ Tidak Sesuai – Ada bukti – Tidak dilaksanakan</option>
                                        <option value="tidak_bukti_tidak_memadai_tidak_konsisten" @selected($curAns==='tidak_bukti_tidak_memadai_tidak_konsisten')>✗ Tidak Sesuai – Bukti tidak memadai – Tidak konsisten</option>
                                        <option value="tidak_tidak_ada_bukti"                     @selected($curAns==='tidak_tidak_ada_bukti')>✗ Tidak Sesuai – Tidak ada bukti</option>
                                        <option value="tidak_dilaksanakan_tidak_ada_bukti"        @selected($curAns==='tidak_dilaksanakan_tidak_ada_bukti')>✗ Tidak dilaksanakan – Tidak ada bukti</option>
                                    </select>
                                    <input type="text" name="answers[{{ $q->id }}][notes]"
                                        value="{{ $ans?->notes }}" placeholder="Catatan verifikasi (opsional)"
                                        class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 text-sm dark:bg-gray-800">
                                    @else
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $sc }}">{{ $sl }}</span>
                                    @if($ans?->notes) <p class="text-xs text-gray-500 mt-1">{{ $ans->notes }}</p> @endif
                                    @endif
                                </div>

                                @if(!$isCompleted)
                                <button type="button" @click="showFinding = !showFinding"
                                    class="text-xs text-red-500 hover:text-red-700 flex items-center gap-1 font-medium mt-1">
                                    <svg class="w-3.5 h-3.5 transition-transform" :class="showFinding ? 'rotate-45' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    + Tambah Temuan
                                </button>
                                @endif
                            </div>

                        </div>{{-- end 3-col grid --}}

                        {{-- Form tambah temuan: full-width di bawah grid --}}
                        @if(!$isCompleted)
                        <div x-show="showFinding" x-transition class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 px-4 py-4">
                            <form method="POST" action="{{ route('assessor.ami.findings.store', $review) }}" class="space-y-4">
                                @csrf
                                <input type="hidden" name="question_id" value="{{ $q->id }}">

                                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                                    <h5 class="text-sm font-bold text-red-800 dark:text-red-400 mb-4 flex items-center gap-2">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        Detail PTK (Temuan)
                                    </h5>

                                    {{-- Area Audit --}}
                                    <div class="mb-4">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Area Audit</label>
                                        <div class="grid grid-cols-2 gap-2 max-h-36 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-md p-2 bg-gray-50 dark:bg-gray-900/50">
                                            @foreach($areas as $area)
                                            <label class="flex items-start gap-2 text-xs hover:bg-white dark:hover:bg-gray-800 p-1 rounded cursor-pointer transition-colors">
                                                <input type="checkbox" name="audit_area_ids[]" value="{{ $area->id }}"
                                                    class="mt-0.5 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                <span class="flex-1 leading-snug">{{ $area->name }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        {{-- Kolom Kiri --}}
                                        <div class="space-y-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi Kondisi</label>
                                                <textarea name="condition_desc" required rows="2"
                                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs" placeholder="Uraian kondisi temuan..."></textarea>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Akibat</label>
                                                <textarea name="impact" rows="2"
                                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs" placeholder="Akibat dari temuan..."></textarea>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                                                <input type="text" id="ami-ptk-cat-display-{{ $q->id }}" value="" readonly
                                                    class="block w-full rounded-md shadow-sm text-xs font-semibold cursor-not-allowed border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 bg-gray-100"
                                                    placeholder="— Otomatis berdasarkan Keterangan —">
                                                <input type="hidden" name="category" id="ami-ptk-cat-{{ $q->id }}">
                                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">* Otomatis berdasarkan Keterangan</p>
                                            </div>
                                        </div>

                                        {{-- Kolom Kanan --}}
                                        <div class="space-y-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Akar Penyebab</label>
                                                <textarea name="root_cause" rows="2"
                                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs" placeholder="Akar penyebab..."></textarea>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Rekomendasi</label>
                                                <textarea name="recommendation" rows="2"
                                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs" placeholder="Rekomendasi..."></textarea>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Rencana Perbaikan (Auditee)</label>
                                                <textarea name="corrective_plan" rows="2"
                                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs" placeholder="Rencana perbaikan dari auditee..."></textarea>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Target Selesai</label>
                                                <input type="date" name="due_date"
                                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Penanggung Jawab (PIC)</label>
                                                <select name="pic"
                                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs">
                                                    <option value="">-- Pilih Penanggung Jawab --</option>
                                                    @foreach($users as $u)
                                                    <option value="{{ $u->name }}">{{ $u->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ATL (FM-AMI/06) --}}
                                <div class="bg-white dark:bg-gray-800 rounded-lg border border-teal-200 dark:border-teal-800/50 shadow-sm">
                                    <div class="flex items-center gap-2 px-4 py-3 border-b border-teal-200 dark:border-teal-800/50">
                                        <svg class="w-4 h-4 text-teal-600 dark:text-teal-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                        <span class="text-sm font-bold text-teal-800 dark:text-teal-400">Audit Tindak Lanjut (FM-AMI/06)</span>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full border border-gray-300 dark:border-gray-600 text-sm">
                                            <thead>
                                                <tr class="bg-gray-50 dark:bg-gray-900/50">
                                                    <th colspan="3" class="px-3 py-2 text-center text-xs font-medium text-gray-500 border-b border-gray-300 dark:border-gray-600"></th>
                                                    <th colspan="3" class="px-3 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase border-b border-l border-gray-300 dark:border-gray-600">Kode Auto Input</th>
                                                </tr>
                                                <tr class="bg-gray-100 dark:bg-gray-800/80">
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase border border-gray-300 dark:border-gray-600 w-1/4">Realisasi</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase border border-gray-300 dark:border-gray-600 w-28">Efektifitas</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase border border-gray-300 dark:border-gray-600 w-24">Status</th>
                                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-600 dark:text-gray-400 uppercase border border-gray-300 dark:border-gray-600 w-24">Kode Standar</th>
                                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-600 dark:text-gray-400 uppercase border border-gray-300 dark:border-gray-600 w-28">Kode Area</th>
                                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-600 dark:text-gray-400 uppercase border border-gray-300 dark:border-gray-600 w-32">Kode PTK</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="px-2 py-2 border border-gray-300 dark:border-gray-600 align-top">
                                                        <textarea name="realisasi" rows="2" placeholder="Realisasi tindak lanjut..."
                                                            class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm text-xs"></textarea>
                                                    </td>
                                                    <td class="px-2 py-2 border border-gray-300 dark:border-gray-600 align-top">
                                                        <select name="efektifitas"
                                                            class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm text-xs">
                                                            <option value="">-- Pilih --</option>
                                                            <option value="Efektif">Efektif</option>
                                                            <option value="Kurang">Kurang</option>
                                                            <option value="Tidak Efektif">Tidak Efektif</option>
                                                        </select>
                                                    </td>
                                                    <td class="px-2 py-2 border border-gray-300 dark:border-gray-600 align-top">
                                                        <select name="tl_status"
                                                            class="ptk-tl-status block w-full rounded-md shadow-sm text-xs font-semibold border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300"
                                                            onchange="styleAtlStatus(this)">
                                                            <option value="">-- Pilih --</option>
                                                            <option value="Open">Open</option>
                                                            <option value="On Progress">On Progress</option>
                                                            <option value="Close">Close</option>
                                                            <option value="Toleran">Toleran</option>
                                                        </select>
                                                    </td>
                                                    <td class="px-2 py-2 border border-gray-300 dark:border-gray-600 text-center align-middle">
                                                        <span class="text-xs font-mono font-semibold text-gray-700 dark:text-gray-300">{{ $kodeStandar }}</span>
                                                    </td>
                                                    <td class="px-2 py-2 border border-gray-300 dark:border-gray-600 text-center align-middle">
                                                        <span class="text-xs font-mono font-semibold text-gray-700 dark:text-gray-300">{{ $kodeArea }}</span>
                                                    </td>
                                                    <td class="px-2 py-2 border border-gray-300 dark:border-gray-600 text-center align-middle">
                                                        <span class="text-xs font-mono font-semibold text-indigo-700 dark:text-indigo-400">{{ $kodePtk }}</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit"
                                        class="px-8 py-2.5 text-sm font-semibold bg-red-600 text-white rounded-lg shadow hover:bg-red-700 transition-colors">
                                        Simpan Temuan
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif
                    </div>
                </x-card>
            </div>
            @empty
            <x-card class="text-center py-10 text-sm text-gray-400">Tidak ada pertanyaan.</x-card>
            @endforelse

            @if(!$isCompleted)
            <div class="flex gap-3 mt-4 sticky bottom-4">
                <button type="submit" class="flex-1 py-3 text-sm font-medium bg-indigo-600 text-white rounded-xl shadow hover:bg-indigo-700">
                    Simpan Jawaban
                </button>
            </div>
            @endif
        </form>

        @if(!$isCompleted)
        <form method="POST" action="{{ route('assessor.ami.complete', $review) }}"
            onsubmit="return confirm('Selesaikan review?')" class="mt-3">
            @csrf @method('PATCH')
            <button type="submit" class="w-full py-3 text-sm font-semibold bg-emerald-600 text-white rounded-xl shadow hover:bg-emerald-700">
                ✓ Selesaikan Review
            </button>
        </form>
        @else
        <div class="mt-3 p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-sm text-emerald-700 text-center">
            ✓ Review selesai pada {{ $review->completed_at?->format('d M Y H:i') }}
        </div>
        @endif
    </div>

<script>
function styleAtlStatus(sel) {
    sel.className = sel.className.replace(/\bbg-\S+\b|\btext-\S+\b/g, '').trim();
    sel.classList.add('atl-tl-status', 'block', 'w-full', 'text-xs', 'font-semibold', 'border-0', 'focus:ring-0');
    const map = {
        'Close':       ['bg-teal-100',   'text-teal-700'],
        'On Progress': ['bg-blue-100',   'text-blue-700'],
        'Open':        ['bg-yellow-100', 'text-yellow-700'],
        'Toleran':     ['bg-orange-100', 'text-orange-700'],
    };
    const cls = map[sel.value];
    if (cls) sel.classList.add(...cls);
    else sel.classList.add('dark:bg-gray-900', 'dark:text-gray-300');
}

document.addEventListener('DOMContentLoaded', () => {
    function applyReviewKetColor(sel, val) {
        sel.classList.remove(
            'bg-red-50','text-red-700','border-red-300',
            'dark:bg-red-900/30','dark:text-red-400','dark:border-red-800',
            'bg-yellow-50','text-yellow-700','border-yellow-300',
            'dark:bg-amber-900/30','dark:text-amber-400','dark:border-amber-800',
            'bg-green-50','text-green-700','border-green-300',
            'dark:bg-emerald-900/30','dark:text-emerald-400','dark:border-emerald-800',
            'border-gray-200','dark:border-gray-700','dark:bg-gray-800'
        );
        if (val && val.startsWith('tidak')) {
            sel.classList.add(
                'bg-red-50','text-red-700','border-red-300',
                'dark:bg-red-900/30','dark:text-red-400','dark:border-red-800'
            );
        } else if (val === 'sebagian') {
            sel.classList.add(
                'bg-yellow-50','text-yellow-700','border-yellow-300',
                'dark:bg-amber-900/30','dark:text-amber-400','dark:border-amber-800'
            );
        } else if (val === 'sesuai') {
            sel.classList.add(
                'bg-green-50','text-green-700','border-green-300',
                'dark:bg-emerald-900/30','dark:text-emerald-400','dark:border-emerald-800'
            );
        } else {
            sel.classList.add('border-gray-200','dark:border-gray-700','dark:bg-gray-800');
        }
    }

    function setAlpineShowFinding(sel, val) {
        const root = sel.closest('[x-data]');
        if (root && window.Alpine) {
            try {
                Alpine.$data(root).showFinding = !!(val && val !== 'sesuai' && val !== 'belum_diperiksa');
            } catch (_) {}
        }
    }

    function autoCategory(sel, val) {
        const catInput   = document.getElementById('ami-ptk-cat-' + sel.dataset.qid);
        const catDisplay = document.getElementById('ami-ptk-cat-display-' + sel.dataset.qid);
        if (!catInput) return;

        const map = {
            'sebagian':                            { label: 'Observasi',  cls: 'bg-yellow-100 text-yellow-800 border-yellow-300' },
            'tidak_tidak_ada_bukti':               { label: 'KTS Mayor',  cls: 'bg-red-500 text-white border-red-700' },
            'tidak_dilaksanakan_tidak_ada_bukti':  { label: 'KTS Mayor',  cls: 'bg-red-500 text-white border-red-700' },
        };
        const entry = map[val] ?? (val && val.startsWith('tidak')
            ? { label: 'KTS Minor', cls: 'bg-orange-100 text-orange-800 border-orange-300' }
            : null);

        catInput.value = entry ? entry.label : '';

        if (catDisplay) {
            catDisplay.value = entry ? entry.label : '';
            catDisplay.className = [
                'block w-full rounded-md shadow-sm text-xs font-semibold cursor-not-allowed border',
                entry ? entry.cls : 'border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 bg-gray-100 text-gray-400',
            ].join(' ');
        }
    }

    document.querySelectorAll('.ami-review-ket').forEach(sel => {
        applyReviewKetColor(sel, sel.value);
        sel.addEventListener('change', e => {
            applyReviewKetColor(sel, e.target.value);
            setAlpineShowFinding(sel, e.target.value);
            autoCategory(sel, e.target.value);
        });
    });
});
</script>
</x-app-layout>
