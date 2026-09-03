<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('internal.ami.index', ['prodi' => $submission->prodi_id]) }}"
                class="text-sm text-indigo-600 hover:underline">← Kembali</a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 mt-1">
                {{ $submission->standard?->code }}: {{ $submission->standard?->name }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $submission->prodi?->name }} · {{ $submission->cycle?->title }}
            </p>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8">
        <x-alert-success :message="session('success')" />
        @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>
        @endif

        @php
            $bannerClass = match($submission->status) {
                'accepted'     => 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400',
                'revision'     => 'bg-red-50 border-red-200 text-red-700',
                'under_review' => 'bg-amber-50 border-amber-200 text-amber-700',
                'submitted'    => 'bg-blue-50 border-blue-200 text-blue-700',
                default        => 'bg-gray-50 border-gray-200 text-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400',
            };
            // Backend menentukan ownership dan penugasan ST; view hanya mengikuti hasilnya.
            $canEdit    = $canEditSubmission;
            $isLocked   = !$canEdit;
            // Keterangan adalah hasil review auditor; seluruh pengguna pada
            // halaman internal hanya boleh melihatnya.
            $isAuditee  = true;
            $canEditKet = false;

            $categoryConfig = [
                'kebijakan'         => ['label' => 'Dokumen Kebijakan',        'desc' => 'SK, Pedoman, SOP',                    'color' => 'blue',   'icon' => '📋'],
                'pelaksanaan'       => ['label' => 'Dokumen Pelaksanaan',       'desc' => 'Jadwal, Absensi, Undangan, Notulen', 'color' => 'green',  'icon' => '📄'],
                'evaluasi'          => ['label' => 'Dokumen Evaluasi',          'desc' => 'Monitoring, RTL, Evaluasi',          'color' => 'amber',  'icon' => '📊'],
                'pendukung_digital' => ['label' => 'Dokumen Pendukung Digital', 'desc' => 'Screenshot LMS, Link Cloud, Rekaman','color' => 'purple', 'icon' => '🔗'],
            ];
            $colorMap = [
                'blue'   => ['bg' => 'bg-blue-50 dark:bg-blue-900/10',       'border' => 'border-blue-200 dark:border-blue-800/50'],
                'green'  => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/10', 'border' => 'border-emerald-200 dark:border-emerald-800/50'],
                'amber'  => ['bg' => 'bg-amber-50 dark:bg-amber-900/10',     'border' => 'border-amber-200 dark:border-amber-800/50'],
                'purple' => ['bg' => 'bg-purple-50 dark:bg-purple-900/10',   'border' => 'border-purple-200 dark:border-purple-800/50'],
            ];
        @endphp

        <div class="mb-5 p-3 border rounded-xl text-sm {{ $bannerClass }}">
            {{ match($submission->status) {
                'accepted'     => '✓ Submission diterima oleh auditor.',
                'revision'     => '⚠ Auditor meminta revisi. Perbarui dan submit ulang.',
                'under_review' => 'Submission sedang direview oleh auditor.',
                'submitted'    => 'Submission telah dikirim. Menunggu review auditor.',
                default        => 'Draft — isi referensi dan bukti untuk setiap pertanyaan, lalu submit.',
            } }}
        </div>

        <form method="POST" action="#" onsubmit="return false">
        @csrf

        @forelse($questions as $q)
        @php
            $refs      = $refMap[$q->id] ?? collect();
            $qEvMap    = $evidenceMap[$q->id] ?? collect();
            $totalEvs  = $qEvMap->flatten()->count();
            $savedStat  = $answerMap[$q->id] ?? null;
            $statBadge  = match(true) {
                ($savedStat['status'] ?? '') === 'sesuai'              => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                ($savedStat['status'] ?? '') === 'sebagian'            => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                str_starts_with($savedStat['status'] ?? '', 'tidak')   => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                default                                                 => 'bg-gray-100 text-gray-500',
            };
        @endphp
        <div class="mb-6">
            <x-card padding="p-0">

                {{-- Question Header --}}
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 text-xs font-bold flex items-center justify-center mt-0.5">
                            {{ $q->question_number }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 leading-snug">{{ $q->question_text }}</p>
                            <div class="flex flex-wrap gap-2 mt-1.5">
                                @if($q->bidang)
                                <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-500 rounded">{{ $q->bidang }}</span>
                                @endif
                                @if($q->auditi)
                                <span class="text-[10px] px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 rounded">{{ $q->auditi }}</span>
                                @endif
                                <span class="text-[10px] {{ $refs->count() > 0 ? 'text-blue-600' : 'text-gray-400' }}">{{ $refs->count() }} referensi</span>
                                <span class="text-[10px] {{ $totalEvs > 0 ? 'text-emerald-600' : 'text-gray-400' }}">{{ $totalEvs }}/20 bukti</span>
                                @if($savedStat)
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full font-medium {{ $statBadge }}">
                                    {{ str($savedStat['status'])->replace('_', ' ')->title() }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-gray-100 dark:divide-gray-700">

                    {{-- REFERENSI --}}
                    <div class="p-4" x-data="{ showForm: false }">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Referensi</span>
                            @if(!$isLocked)
                            <button @click="showForm = !showForm"
                                class="text-[10px] font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-0.5">
                                <svg class="w-3 h-3 transition-transform" :class="showForm ? 'rotate-45' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambah Link
                            </button>
                            @endif
                        </div>

                        @if(!$isLocked)
                        <form x-show="showForm" x-transition method="POST"
                            action="{{ route('internal.ami.references.store', $submission) }}"
                            class="mb-3 space-y-1.5 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
                            @csrf
                            <input type="hidden" name="ami_question_id" value="{{ $q->id }}">
                            <input type="text" name="title" required placeholder="Judul dokumen referensi"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1.5 text-xs dark:bg-gray-800 focus:ring-1 focus:ring-indigo-300 focus:outline-none">
                            <input type="url" name="url" required placeholder="https://…"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1.5 text-xs dark:bg-gray-800 focus:ring-1 focus:ring-indigo-300 focus:outline-none">
                            <input type="text" name="notes" placeholder="Catatan (opsional)"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1.5 text-xs dark:bg-gray-800 focus:outline-none">
                            <button type="submit"
                                class="w-full py-1.5 text-xs font-medium bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                Simpan Referensi
                            </button>
                        </form>
                        @endif

                        @forelse($refs as $ref)
                        <div class="flex items-start gap-2 py-1.5 {{ !$loop->first ? 'border-t border-gray-100 dark:border-gray-700' : '' }}">
                            <svg class="w-3 h-3 text-indigo-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            <div class="flex-1 min-w-0">
                                <a href="{{ $ref->url }}" target="_blank" rel="noopener"
                                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline block truncate">{{ $ref->title }}</a>
                                @if($ref->notes)
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $ref->notes }}</p>
                                @endif
                            </div>
                            @if(!$isLocked)
                            <form method="POST" action="{{ route('internal.ami.references.destroy', [$submission, $ref]) }}"
                                onsubmit="return confirm('Hapus referensi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                        @empty
                        <p class="text-[10px] text-gray-400 italic">Belum ada referensi.</p>
                        @endforelse
                    </div>

                    {{-- BUKTI — 4 kategori, max 20 per pertanyaan --}}
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Bukti</span>
                            @if($totalEvs >= 20)
                            <span class="text-[10px] text-amber-500 font-medium">Batas 20 bukti tercapai</span>
                            @else
                            <span class="text-[10px] text-gray-400">{{ $totalEvs }}/20</span>
                            @endif
                        </div>

                        <div class="space-y-3">
                            @foreach(['kebijakan', 'pelaksanaan', 'evaluasi', 'pendukung_digital'] as $cat)
                            @php
                                $cfg  = $categoryConfig[$cat];
                                $clr  = $colorMap[$cfg['color']];
                                $evs  = $qEvMap[$cat] ?? collect();
                                $categoryFiles = $prepFiles->where('category', $cat)->values();
                                $canAdd = !$isLocked && $categoryFiles->isNotEmpty() && $totalEvs < 20;
                            @endphp
                            <div class="{{ $clr['bg'] }} {{ $clr['border'] }} border rounded-xl p-3"
                                x-data="{ showForm: false, files: {{ Illuminate\Support\Js::from($categoryFiles) }}, selectedId: '' }">

                                <div class="flex items-center justify-between mb-1.5">
                                    <div>
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $cfg['icon'] }} {{ $cfg['label'] }}</span>
                                        <div class="text-[10px] text-gray-400">{{ $cfg['desc'] }}</div>
                                    </div>
                                    @if($canAdd)
                                    <button @click="showForm = !showForm"
                                        class="text-[10px] font-medium text-gray-500 hover:text-gray-700 border border-gray-300 rounded px-1.5 py-0.5 transition-colors"
                                        :class="showForm ? 'bg-gray-100' : ''">
                                        + Pilih
                                    </button>
                                    @endif
                                </div>

                                {{-- Dropdown pilih dokumen persiapan --}}
                                @if($canAdd)
                                <div x-show="showForm" x-transition class="mb-2">
                                    <form method="POST" action="{{ route('internal.ami.evidences.store', $submission) }}" class="space-y-1.5">
                                        @csrf
                                        <input type="hidden" name="ami_question_id" value="{{ $q->id }}">
                                        <input type="hidden" name="category" value="{{ $cat }}">
                                        <select name="preparation_task_id" required x-model="selectedId"
                                            class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1.5 text-xs dark:bg-gray-800 focus:outline-none">
                                            <option value="">— Pilih dokumen —</option>
                                            <template x-for="f in files" :key="f.id">
                                                <option :value="f.id" x-text="f.label"></option>
                                            </template>
                                        </select>
                                        <button type="submit"
                                            class="w-full py-2 text-xs font-semibold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                            ✓ Simpan Bukti
                                        </button>
                                    </form>
                                </div>
                                @endif

                                {{-- Daftar bukti yang sudah dipilih --}}
                                @forelse($evs as $ev)
                                <div class="flex items-center gap-1.5 py-1 {{ !$loop->first ? 'border-t border-black/5' : '' }}">
                                    <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    <a href="{{ $ev->display_url }}" target="_blank" rel="noopener"
                                        class="flex-1 text-xs text-indigo-600 dark:text-indigo-400 hover:underline truncate">
                                        {{ $ev->display_name }}
                                    </a>
                                    @if(!$isLocked)
                                    <form method="POST" action="{{ route('internal.ami.evidences.destroy', [$submission, $ev]) }}"
                                        onsubmit="return confirm('Hapus bukti ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 flex-shrink-0">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                                @empty
                                <p class="text-[10px] text-gray-400 italic">Belum ada dokumen.</p>
                                @endforelse
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- KETERANGAN --}}
                    @php
                        $amiStatusLabels = [
                            'sesuai'                                    => 'Sesuai',
                            'sebagian'                                  => 'Sebagian Sesuai',
                            'tidak_bukti_tidak_memadai'                 => 'Tidak Sesuai – Bukti tidak memadai',
                            'tidak_ada_bukti_tidak_dilaksanakan'        => 'Tidak Sesuai – Ada bukti – Tidak dilaksanakan',
                            'tidak_bukti_tidak_memadai_tidak_konsisten' => 'Tidak Sesuai – Bukti tidak memadai – Tidak konsisten',
                            'tidak_tidak_ada_bukti'                     => 'Tidak Sesuai – Tidak ada bukti',
                            'tidak_dilaksanakan_tidak_ada_bukti'        => 'Tidak dilaksanakan – Tidak ada bukti',
                        ];
                        $curStatus = $savedStat['status'] ?? 'sesuai';
                    @endphp
                    <div class="p-4">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Keterangan</div>

                        @if($isAuditee)
                        {{-- Auditee: tampilkan hasil penilaian auditor (read-only) --}}
                        @php
                            $auditorAns   = $auditorAnswers[$q->id] ?? null;
                            $auditorSt    = $auditorAns?->status;
                            $auditorBadge = match(true) {
                                $auditorSt === 'sesuai'              => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                $auditorSt === 'sebagian'            => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                str_starts_with($auditorSt ?? '', 'tidak') => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                default                              => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                            };
                        @endphp
                        @if($auditorAns && $auditorAns->status !== 'belum_diperiksa')
                        <span class="inline-flex items-center text-xs px-2.5 py-1 rounded-full font-medium {{ $auditorBadge }}">
                            {{ $amiStatusLabels[$auditorAns->status] ?? $auditorAns->status }}
                        </span>
                        @if($auditorAns->notes)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 italic">{{ $auditorAns->notes }}</p>
                        @endif
                        @else
                        <div class="flex items-center gap-1.5 text-xs text-gray-400 italic">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Menunggu penilaian auditor.
                        </div>
                        @endif

                        @elseif($canEditKet)
                        {{-- Pemilik submission atau admin dapat mengisi keterangan --}}
                        <select name="statuses[{{ $q->id }}][status]"
                            class="ami-ket-select w-full border rounded-lg px-3 py-2 text-sm mb-2 focus:ring-2 focus:ring-indigo-300 focus:outline-none"
                            data-qid="{{ $q->id }}">
                            <option value="sesuai"                                    @selected($curStatus==='sesuai')>✓ Sesuai</option>
                            <option value="sebagian"                                  @selected($curStatus==='sebagian')>◑ Sebagian Sesuai</option>
                            <option value="tidak_bukti_tidak_memadai"                 @selected($curStatus==='tidak_bukti_tidak_memadai')>✗ Tidak Sesuai – Bukti tidak memadai</option>
                            <option value="tidak_ada_bukti_tidak_dilaksanakan"        @selected($curStatus==='tidak_ada_bukti_tidak_dilaksanakan')>✗ Tidak Sesuai – Ada bukti – Tidak dilaksanakan</option>
                            <option value="tidak_bukti_tidak_memadai_tidak_konsisten" @selected($curStatus==='tidak_bukti_tidak_memadai_tidak_konsisten')>✗ Tidak Sesuai – Bukti tidak memadai – Tidak konsisten</option>
                            <option value="tidak_tidak_ada_bukti"                     @selected($curStatus==='tidak_tidak_ada_bukti')>✗ Tidak Sesuai – Tidak ada bukti</option>
                            <option value="tidak_dilaksanakan_tidak_ada_bukti"        @selected($curStatus==='tidak_dilaksanakan_tidak_ada_bukti')>✗ Tidak dilaksanakan – Tidak ada bukti</option>
                        </select>
                        <textarea name="statuses[{{ $q->id }}][notes]"
                            rows="2"
                            placeholder="Catatan (opsional)"
                            id="ami-notes-{{ $q->id }}"
                            class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 text-sm dark:bg-gray-800 focus:outline-none {{ $curStatus === 'sesuai' ? 'hidden' : '' }}">{{ $savedStat['notes'] ?? '' }}</textarea>

                        @else
                        {{-- Status lainnya: read-only self-assessment --}}
                        @if($savedStat)
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $statBadge }}">
                            {{ $amiStatusLabels[$savedStat['status']] ?? $savedStat['status'] }}
                        </span>
                        @if($savedStat['notes'] ?? null)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">{{ $savedStat['notes'] }}</p>
                        @endif
                        @else
                        <p class="text-xs text-gray-400 italic">—</p>
                        @endif
                        @endif
                    </div>

                </div>{{-- end grid --}}
            </x-card>
        </div>
        @empty
        <x-card class="text-center py-10 text-sm text-gray-400">
            Belum ada pertanyaan untuk standar <strong>{{ $submission->standard?->code }}</strong>.
        </x-card>
        @endforelse

        @if($canEdit)
        <div class="mt-4 sticky bottom-4 flex gap-3">
            @if($canEditKet)
            <button type="submit"
                class="flex-1 py-3.5 text-sm font-semibold bg-white dark:bg-gray-800 border border-indigo-500 text-indigo-600 dark:text-indigo-400 rounded-xl shadow hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                Simpan Keterangan
            </button>
            @endif
        </div>
        </form>

        <form method="POST" action="{{ route('internal.ami.submit', $submission) }}"
            onsubmit="return confirm('Submit ke auditor? Pastikan semua referensi dan bukti sudah lengkap.')"
            class="mt-3">
            @csrf
            <button type="submit"
                class="w-full py-3.5 text-sm font-semibold bg-indigo-600 text-white rounded-xl shadow-lg hover:bg-indigo-700 transition-colors">
                Submit ke Auditor
            </button>
        </form>
        @else
        </form>
        @endif
    </div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    function applyKetColor(sel, val) {
        sel.classList.remove(
            'bg-red-50','text-red-700','border-red-300','dark:bg-red-900/30','dark:text-red-400','dark:border-red-800',
            'bg-yellow-50','text-yellow-700','border-yellow-300','dark:bg-amber-900/30','dark:text-amber-400','dark:border-amber-800',
            'bg-green-50','text-green-700','border-green-300','dark:bg-emerald-900/30','dark:text-emerald-400','dark:border-emerald-800',
            'border-gray-200','dark:border-gray-700','dark:bg-gray-800'
        );
        if (val && val.startsWith('tidak')) {
            sel.classList.add('bg-red-50','text-red-700','border-red-300','dark:bg-red-900/30','dark:text-red-400','dark:border-red-800');
        } else if (val === 'sebagian') {
            sel.classList.add('bg-yellow-50','text-yellow-700','border-yellow-300','dark:bg-amber-900/30','dark:text-amber-400','dark:border-amber-800');
        } else {
            sel.classList.add('bg-green-50','text-green-700','border-green-300','dark:bg-emerald-900/30','dark:text-emerald-400','dark:border-emerald-800');
        }

        const notesEl = document.getElementById('ami-notes-' + sel.dataset.qid);
        if (notesEl) {
            if (val === 'sesuai') {
                notesEl.classList.add('hidden');
            } else {
                notesEl.classList.remove('hidden');
            }
        }
    }

    document.querySelectorAll('.ami-ket-select').forEach(sel => {
        applyKetColor(sel, sel.value);
        sel.addEventListener('change', e => applyKetColor(sel, e.target.value));
    });
});
</script>
</x-app-layout>
