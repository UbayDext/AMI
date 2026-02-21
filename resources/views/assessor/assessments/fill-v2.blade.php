<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Fill Assessment') }}
                </h2>
                <div class="text-xs text-gray-500 mt-1">
                    Unit: <span class="font-medium text-gray-700">{{ $assessment->unit_name }}</span> |
                    Status: <span class="font-medium text-gray-700">{{ ucfirst($assessment->status) }}</span>
                </div>
            </div>

            <a href="{{ route('assessor.assessments.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
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

            <form id="assessmentForm" method="POST"
                action="{{ route('assessor.assessments.fill.update', $assessment) }}"
                enctype="multipart/form-data">
                @csrf

                <!-- Instructions Card -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r shadow-sm mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Petunjuk:</strong> Pilih keterangan untuk setiap pertanyaan. Jika status <strong>"Tidak Sesuai"</strong>, detail PTK akan muncul otomatis untuk diisi.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Questions Filter -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex flex-wrap items-center justify-between gap-4">
                        <h3 class="text-lg font-medium text-gray-900">Filter Pertanyaan</h3>
                        <form method="GET" action="{{ route('assessor.assessments.fill', $assessment) }}" class="flex items-center gap-2">
                            <select name="standard_id" class="text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" onchange="this.form.submit()">
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

                <!-- Questions Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">Assessment Questions</h3>
                        <p class="text-sm text-gray-500 mt-1">Isi keterangan untuk setiap pertanyaan. Scroll horizontal untuk melihat detail PTK.</p>
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
                    <div class="border-b border-gray-300 bg-gradient-to-r from-indigo-50 to-white px-6 py-3">
                        <h4 class="text-sm font-bold text-indigo-900 uppercase tracking-wide">
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

                    <div x-data="{ open: true }" class="border-b border-gray-100 last:border-b-0">
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between px-6 py-3 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 text-left border-l-4 border-indigo-300">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-indigo-400 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <span class="text-sm font-semibold text-gray-700">{{ $standardLabel }}</span>
                                @if($std && $std->name)
                                <span class="text-xs text-gray-500 hidden sm:inline">— {{ Str::limit($std->name, 60) }}</span>
                                @endif
                                <span class="text-xs font-medium text-gray-500 bg-gray-200 px-2 py-0.5 rounded-full">{{ $standardQuestions->count() }} Pertanyaan</span>
                            </div>
                        </button>

                        <div x-show="open" x-collapse>
                            <!-- Table with horizontal scroll -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-white sticky top-0 z-10">
                                        <tr>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">No</th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider" style="min-width: 250px;">Pertanyaan</th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider" style="min-width: 100px;">Referensi</th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider" style="min-width: 200px;">Bukti</th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider" style="min-width: 150px;">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
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
                    <div class="px-6 py-12 text-center text-gray-500">
                        <p>No questions available for this assessment.</p>
                    </div>
                    @endforelse
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-4 mt-6">
                    <a href="{{ route('assessor.assessments.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Cancel</a>

                    {{-- Save Draft --}}
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Assessment
                    </button>

                    {{-- Submit Final --}}
                    <button type="button" onclick="document.getElementById('submitModal').classList.remove('hidden')" class="inline-flex items-center px-6 py-3 bg-emerald-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Submit Assessment
                    </button>
                </div>

                @include('assessor.assessments.partials.submit-modal')
            </form>
        </div>
    </div>

    @include('assessor.assessments.partials.scripts')
</x-app-layout>