{{-- ── Recent Assessments ───────────────────────────────── --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Assessment Terbaru</h3>
        @can('manage assessments')
        <a href="{{ route('admin.assessments.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">Lihat semua →</a>
        @endcan
    </div>

    @if ($recentAssessments->isEmpty())
    <div class="px-6 py-12 text-center">
        <svg class="mx-auto w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p class="text-sm text-gray-400 dark:text-gray-500">Belum ada assessment.</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                    <th class="px-6 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Study</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Tahun</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Assessor</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Create-at</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                @foreach ($recentAssessments as $a)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-100">
                    <td class="px-6 py-3.5 font-medium text-gray-800 dark:text-gray-200">{{ $a->unit_name }}</td>
                    <td class="px-6 py-3.5 text-gray-500 dark:text-gray-400">{{ $a->accreditationYear?->year ?? '-' }}</td>
                    <td class="px-6 py-3.5 text-gray-500 dark:text-gray-400">{{ $a->assessor?->name ?? '-' }}</td>
                    <td class="px-6 py-3.5">
                        @php
                        $badge = match($a->status) {
                        'submitted' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                        'reviewed' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                        default => 'bg-gray-100 text-gray-600 dark:bg-gray-800/80 dark:text-gray-400',
                        };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                            {{ ucfirst($a->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-3.5 text-gray-400 dark:text-gray-500 text-xs">{{ $a->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
