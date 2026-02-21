<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Assessment Details') }}
            </h2>
            <a href="{{ route('admin.assessments.index') }}" class="mt-4 sm:mt-0 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm font-medium transition">
                &larr; {{ __('Back to List') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Details Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Information
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Basic details about the assessment.
                    </p>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Accreditation Year
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $assessment->accreditationYear->year ?? '-' }}
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Unit Name
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $assessment->unit_name }}
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Assessor
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $assessment->assessor->name ?? 'Unknown' }}
                                <span class="text-gray-500 text-xs">({{ $assessment->assessor->email ?? '' }})</span>
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Status
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $assessment->status === 'submitted' ? 'bg-green-100 text-green-800' : 
                                      ($assessment->status === 'draft' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($assessment->status) }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Findings Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Findings (Temuan)
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            List of findings recorded for this assessment.
                        </p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Standard</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Severity</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($assessment->findings as $f)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $f->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $f->standard->code ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $f->auditAreaNames }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $f->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $f->severity }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No findings recorded.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Detailed Results (Answers & PTK) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Detailed Assessment Results
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Full record of questions, assessor answers, and corrective actions (PTK).
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider w-10">No</th>
                                <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider w-20">Std</th>
                                <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider w-64">Question</th>
                                <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider w-40">Proof / Ref</th>
                                <th class="px-3 py-3 text-center font-medium text-gray-500 uppercase tracking-wider w-24">Result</th>
                                <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider w-48">Reason / PTK Root Cause</th>
                                <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">PTK Plan & Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $no = 1; @endphp
                            @foreach($groupedQuestions as $categoryId => $questions)
                            <!-- Category Header -->
                            @php
                            $cat = $questions->first()->category;
                            $catName = $cat ? ($cat->code ? $cat->code . ' - ' : '') . $cat->name : 'Uncategorized';
                            @endphp
                            <tr class="bg-indigo-50">
                                <td colspan="7" class="px-4 py-2 font-bold text-indigo-700">
                                    {{ $catName }}
                                </td>
                            </tr>

                            @foreach($questions as $q)
                            @php
                            $ans = $answers[$q->id] ?? null;
                            $ptk = $ptks[$q->id] ?? null;
                            $bgClass = '';
                            $statusLabel = '-';
                            $statusColor = 'text-gray-400';

                            if ($ans) {
                            if ($ans->status === 'sesuai') {
                            $statusLabel = 'Sesuai';
                            $statusColor = 'text-green-600';
                            } elseif ($ans->status === 'sebagian') {
                            $bgClass = 'bg-yellow-50/50';
                            $statusLabel = 'Sebagian';
                            $statusColor = 'text-yellow-600';
                            } elseif (str_starts_with($ans->status, 'tidak')) {
                            $bgClass = 'bg-red-50/50';
                            $statusLabel = 'Tidak Sesuai';
                            $statusColor = 'text-red-600';
                            }
                            }
                            @endphp
                            <tr class="hover:bg-gray-50 {{ $bgClass }}">
                                <td class="px-3 py-3 text-gray-500 text-center sticky left-0 bg-inherit">{{ $no++ }}</td>
                                <td class="px-3 py-3 text-gray-500">{{ $q->standard->code ?? '-' }}</td>
                                <td class="px-3 py-3 text-gray-900">
                                    <div class="line-clamp-3 hover:line-clamp-none transition-all duration-200" title="{{ $q->label }}">
                                        {{ $q->label }}
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-gray-600">
                                    @if($ans?->value_text)
                                    <div class="italic mb-1">{{ Str::limit($ans->value_text, 50) }}</div>
                                    @endif
                                    @if($ans?->file_path)
                                    <a href="{{ Storage::url($ans->file_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 underline flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        File
                                    </a>
                                    @endif
                                    @if($q->reference)
                                    <div class="text-[10px] text-gray-400 mt-1">Ref: {{ Str::limit($q->reference, 30) }}</div>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center font-bold text-[10px] uppercase {{ $statusColor }}">
                                    {{ $statusLabel }}
                                </td>
                                <td class="px-3 py-3 text-gray-700">
                                    @if($ans?->reason)
                                    <div class="mb-2"><span class="font-semibold text-[10px] text-gray-400">ALASAN:</span><br>{{ $ans->reason }}</div>
                                    @endif
                                    @if($ptk)
                                    <div class="text-xs">
                                        <span class="font-semibold text-[10px] text-red-400">ROOT CAUSE:</span><br>
                                        {{ $ptk->root_cause ?? '-' }}
                                    </div>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-gray-700">
                                    @if($ptk)
                                    <div class="mb-1"><span class="font-semibold text-[10px] text-gray-400">PLAN:</span> {{ $ptk->corrective_plan ?? '-' }}</div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 border border-gray-200">
                                            Due: {{ $ptk->due_date ? date('d/m/y', strtotime($ptk->due_date)) : '-' }}
                                        </span>
                                        @php
                                        $tlColor = match($ptk->tl_status) {
                                        'Close' => 'text-green-600 bg-green-50 border-green-200',
                                        'Open' => 'text-red-600 bg-red-50 border-red-200',
                                        default => 'text-orange-600 bg-orange-50 border-orange-200'
                                        };
                                        @endphp
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $tlColor }}">
                                            {{ $ptk->tl_status ?? 'Open' }}
                                        </span>
                                    </div>
                                    @else
                                    <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>