<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Fill Assessment') }}
                </h2>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Unit: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $assessment->unit_name }}</span> |
                    Status: <span class="font-medium text-gray-700 dark:text-gray-300">{{ ucfirst($assessment->status) }}</span>
                </div>
            </div>

            <a href="{{ route('assessor.assessments.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="p-4 bg-green-100 border border-green-200 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="p-4 bg-red-100 border border-red-200 text-red-700 rounded-md">
                <b>Validation Failed:</b>
                <ul class="list-disc ml-5 mt-1 text-sm">
                    @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (session('error'))
            <div class="p-4 bg-red-100 border border-red-200 text-red-700 rounded-md">
                {{ session('error') }}
            </div>
            @endif

            @if ($assessment->status === 'submitted')
            <div class="p-4 bg-amber-50 border border-amber-300 text-amber-800 rounded-md flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0-10v4m-7.07 9.07A10 10 0 1121.07 5.93 10 10 0 015.93 19.07z"></path>
                </svg>
                <div>
                    <p class="font-semibold text-sm">Assessment Sudah Disubmit</p>
                    <p class="text-sm mt-0.5">Assessment ini sudah berstatus <strong>Submitted</strong>. Jawaban tidak dapat diubah lagi. Halaman ini hanya untuk melihat data.</p>
                </div>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex flex-wrap items-center justify-between gap-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Filter Pertanyaan</h3>
                    <form method="GET" action="{{ route('assessor.assessments.fill', $assessment) }}" class="flex items-center gap-2">
                        <select name="standard_id" class="text-sm border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" onchange="this.form.submit()">
                            <option value="">Semua Standar</option>
                            @foreach($standards as $std)
                            <option value="{{ $std->id }}" {{ request('standard_id') == $std->id ? 'selected' : '' }}>
                                {{ $std->code }} - {{ $std->name }}
                            </option>
                            @endforeach
                        </select>
                        @if(request('standard_id'))
                        <a href="{{ route('assessor.assessments.fill', $assessment) }}" class="text-sm text-red-600 hover:text-red-900 underline">Reset</a>
                        @endif
                    </form>
                </div>
            </div>

            <form id="assessmentForm" method="POST"
                action="{{ route('assessor.assessments.fill.update', $assessment) }}"
                enctype="multipart/form-data">
                @csrf
                {{-- Carry the currently-filtered standard so the submit is scoped to it --}}
                <input type="hidden" name="submit_standard_id" value="{{ request('standard_id') }}">

                <!-- Questions Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Assessment Questions</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Isi keterangan untuk setiap pertanyaan. Scroll horizontal untuk melihat detail PTK.</p>
                    </div>

                    @php
                    $no = 1;
                    @endphp

                    @forelse($groupedQuestions as $categoryId => $items)
                    @php
                    $cat = $items->first()?->category;
                    $catTitle = $cat ? $cat->name : 'Uncategorized';
                    @endphp

                    <!-- Category Section -->
                    <div class="border-b border-gray-300 dark:border-gray-600 bg-gradient-to-r from-indigo-50 to-white px-6 py-3">
                        <h4 class="text-sm font-bold text-indigo-900 dark:text-indigo-200 uppercase tracking-wide">
                            {{ $catTitle }}
                        </h4>
                    </div>

                    <!-- Group by Standard -->
                    @php
                    $standardGroups = $items->groupBy(fn($q) => $q->standard_id ?? 0);
                    @endphp

                    @foreach($standardGroups as $standardId => $standardQuestions)
                    @php
                    $std = $standardQuestions->first()->standard;
                    $standardLabel = $std ? $std->code : 'No Standard';
                    @endphp

                    <div x-data="{ open: true }" class="border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between px-6 py-3 bg-gray-50 dark:bg-gray-900/50 hover:bg-gray-100 transition-colors duration-200 text-left border-l-4 border-indigo-300">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-indigo-400 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $standardLabel }}</span>
                                @if($std && $std->name)
                                <span class="text-xs text-gray-500 dark:text-gray-400 hidden sm:inline">— {{ Str::limit($std->name, 60) }}</span>
                                @endif
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-200 dark:bg-gray-700 px-2 py-0.5 rounded-full">{{ $standardQuestions->count() }} Pertanyaan</span>
                            </div>
                        </button>

                        <div x-show="open" x-collapse>
                            <!-- Table with horizontal scroll -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-white dark:bg-gray-800 sticky top-0 z-10">
                                        <tr>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">No</th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider" style="min-width: 250px;">Pertanyaan</th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider" style="min-width: 100px;">Referensi</th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider" style="min-width: 200px;">Bukti</th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider" style="min-width: 150px;">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                        @foreach ($standardQuestions as $q)
                                        @php
                                        $ans = $answers[$q->id] ?? null;
                                        $ptk = $ptks[$q->id] ?? null;
                                        @endphp
                                        @include('assessor.assessments.partials.ptk-table-row', [
                                        'question' => $q,
                                        'answer' => $ans,
                                        'ptk' => $ptk,
                                        'no' => $no,
                                        'areas' => $areas,
                                        'assessment' => $assessment
                                        ])
                                        @php $no++; @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @empty
                    <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <p>No questions available for this assessment.</p>
                    </div>
                    @endforelse
                </div>

                @php
                $filteredStdId = request('standard_id');
                $submittedStds = $assessment->submitted_standards ?? [];
                $thisStdAlreadySubmitted = $filteredStdId && in_array((int)$filteredStdId, $submittedStds);
                @endphp

                @if ($assessment->status !== 'submitted')

                @if($thisStdAlreadySubmitted)
                {{-- Banner: this standard was already submitted --}}
                <div class="mt-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-semibold text-sm">Standar Ini Sudah Disubmit</p>
                        <p class="text-sm mt-0.5">Jawaban untuk standar ini sudah disubmit dan tidak dapat diubah lagi.</p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-4 mt-4">
                    <a href="{{ route('assessor.assessments.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 underline">Kembali</a>
                </div>
                @else
                <div class="flex items-center justify-end gap-4 mt-6">
                    <a href="{{ route('assessor.assessments.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 underline">Cancel</a>

                    {{-- Save Draft --}}
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Assessment
                    </button>

                    {{-- Submit this standard only --}}
                    <button type="button" onclick="document.getElementById('submitModal').classList.remove('hidden')" class="inline-flex items-center px-6 py-3 bg-emerald-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        @if($filteredStdId)
                        Submit Standar Ini
                        @else
                        Submit Assessment
                        @endif
                    </button>
                </div>
                @endif

                @include('assessor.assessments.partials.submit-modal')
                @else
                <div class="flex items-center justify-end gap-4 mt-6">
                    <a href="{{ route('assessor.assessments.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                        Kembali ke Daftar
                    </a>
                </div>
                @endif

            </form>
        </div>
    </div>

    @include('assessor.assessments.partials.scripts')
</x-app-layout>