@php
    $completionRate = $totalAssessments > 0 ? round($submittedAssessments / $totalAssessments * 100) : 0;
    $cards = [
        ['label' => 'Total Assessment', 'value' => $totalAssessments, 'detail' => 'Assessment pada filter aktif', 'tone' => 'indigo', 'icon' => 'document'],
        ['label' => 'Sudah Disubmit', 'value' => $submittedAssessments, 'detail' => $completionRate.'% dari total assessment', 'tone' => 'emerald', 'icon' => 'check'],
        ['label' => 'Total Temuan', 'value' => $totalFindings, 'detail' => 'Temuan audit teridentifikasi', 'tone' => 'rose', 'icon' => 'alert'],
        ['label' => 'Tindak Lanjut PTK', 'value' => $totalPtks, 'detail' => 'Rencana perbaikan terdaftar', 'tone' => 'amber', 'icon' => 'refresh'],
    ];
    $tones = [
        'indigo' => ['icon' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-400', 'bar' => 'bg-indigo-500'],
        'emerald' => ['icon' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-400', 'bar' => 'bg-emerald-500'],
        'rose' => ['icon' => 'bg-rose-50 text-rose-600 dark:bg-rose-900/40 dark:text-rose-400', 'bar' => 'bg-rose-500'],
        'amber' => ['icon' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/40 dark:text-amber-400', 'bar' => 'bg-amber-500'],
    ];
@endphp

<section>
    <div class="mb-3"><h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Ringkasan Utama</h3><p class="text-xs text-slate-400">Indikator aktivitas audit mutu internal.</p></div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($cards as $card)
        @php($tone = $tones[$card['tone']])
        <article class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
            <span class="absolute inset-x-0 top-0 h-1 {{ $tone['bar'] }}"></span>
            <div class="flex items-start justify-between gap-4">
                <div><p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ $card['label'] }}</p><p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ number_format($card['value']) }}</p></div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $tone['icon'] }}">
                    @if($card['icon'] === 'check')
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($card['icon'] === 'alert')
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @elseif($card['icon'] === 'refresh')
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    @else
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    @endif
                </span>
            </div>
            <p class="mt-4 border-t border-slate-100 pt-3 text-xs text-slate-500 dark:border-slate-700 dark:text-slate-400">{{ $card['detail'] }}</p>
        </article>
        @endforeach
    </div>
</section>
