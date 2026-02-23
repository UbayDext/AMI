{{-- ── Stat Cards ──────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

    {{-- Total Assessments --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center gap-5 hover:shadow-md transition-shadow duration-200">
        <div class="flex-shrink-0 w-14 h-14 bg-indigo-50 dark:bg-indigo-900/40 rounded-2xl flex items-center justify-center">
            <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Assessments</p>
            <p class="mt-1 text-3xl font-extrabold text-gray-900 dark:text-gray-100">{{ $totalAssessments }}</p>
            <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">Semua tahun akreditasi</p>
        </div>
    </div>

    {{-- Submitted --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center gap-5 hover:shadow-md transition-shadow duration-200">
        <div class="flex-shrink-0 w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center">
            <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Submitted</p>
            <p class="mt-1 text-3xl font-extrabold text-gray-900 dark:text-gray-100">{{ $submittedAssessments }}</p>
            <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                @if ($totalAssessments > 0)
                {{ round($submittedAssessments / $totalAssessments * 100) }}% dari total
                @else
                Belum ada data
                @endif
            </p>
        </div>
    </div>

    {{-- Total Findings --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center gap-5 hover:shadow-md transition-shadow duration-200">
        <div class="flex-shrink-0 w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center">
            <svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Temuan</p>
            <p class="mt-1 text-3xl font-extrabold text-gray-900 dark:text-gray-100">{{ $totalFindings }}</p>
            <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">Findings & PTK terdaftar</p>
        </div>
    </div>

</div>