<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Assessments (Updated)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Stats / Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="text-gray-500 text-sm font-medium">Pending Assessments</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $assessments->where('status', 'draft')->count() }}
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 text-sm font-medium">Submitted</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $assessments->where('status', 'submitted')->count() }}
                    </div>
                </div>
                <div class="bg-indigo-50 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-indigo-900 mb-2">Instructions</h3>
                    <p class="text-sm text-indigo-700">
                        Select an assessment below to start evaluating. Ensure all required fields and evidence are valid before submitting.
                    </p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <form method="GET" action="{{ route('assessor.assessments.index') }}" class="flex flex-wrap items-end gap-4">

                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Tahun</label>
                            <select name="year" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">Semua Tahun</option>
                                @foreach ($years as $y)
                                <option value="{{ $y->id }}" @selected((string) request('year')===(string) $y->id)>
                                    {{ $y->year }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</label>
                            <select name="status" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">Semua Status</option>
                                <option value="draft" @selected(request('status')==='draft' )>Draft</option>
                                <option value="submitted" @selected(request('status')==='submitted' )>Submitted</option>
                                <option value="reviewed" @selected(request('status')==='reviewed' )>Reviewed</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Kategori</label>
                            <select name="category_id" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(request('category_id')==$cat->id)>
                                    {{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Standar</label>
                            <select name="standard_id" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">Semua Standar</option>
                                @foreach($standards as $std)
                                <option value="{{ $std->id }}" @selected(request('standard_id')==$std->id)>
                                    {{ $std->code }} - {{ $std->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                Filter
                            </button>
                            @if(request('category_id') || request('standard_id') || request('year') || request('status'))
                            <a href="{{ route('assessor.assessments.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">
                                Reset
                            </a>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="px-6 py-2 bg-gray-50 text-sm text-gray-600">
                    Menampilkan <strong>{{ $assessments->count() }}</strong> assessment
                    @if(request('category_id') || request('standard_id') || request('year') || request('status'))
                    (filtered)
                    @endif
                </div>
            </div>

            <!-- Nested Collapsible: Category → Standard → items -->
            @php $no = 1; @endphp

            @forelse($nestedGroups as $catId => $standardGroups)
            @php
            $cat = $categories->find($catId); // We have $categories available
            $categoryName = $cat ? $cat->name : 'Uncategorized';
            $totalInCategory = $standardGroups->flatten()->count();
            @endphp

            {{-- CATEGORY LEVEL --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-4 overflow-hidden" x-data="{ openCat: true }">
                <button type="button" @click="openCat = !openCat"
                    class="w-full flex items-center justify-between px-6 py-4 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 text-left border-l-4 border-indigo-300">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-indigo-400 transition-transform duration-200" :class="{ 'rotate-90': openCat }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="text-sm font-bold text-gray-700">{{ $categoryName }}</span>
                        <span class="text-xs font-medium text-gray-500 bg-gray-200 px-2.5 py-0.5 rounded-full">{{ $totalInCategory }} assessment(s)</span>
                    </div>
                </button>

                <div x-show="openCat" x-collapse>
                    <div class="p-4 bg-white border-t border-gray-100">

                        @foreach($standardGroups as $stdId => $items)
                        @php
                        $std = $standards->find($stdId);
                        $standardName = $std ? ($std->code . ' - ' . $std->name) : 'No Standard';
                        @endphp

                        {{-- STANDARD LEVEL --}}
                        <div class="mb-4 last:mb-0 border border-gray-100 rounded-lg overflow-hidden" x-data="{ openStd: true }">
                            <button type="button" @click="openStd = !openStd"
                                class="w-full flex items-center justify-between px-4 py-3 bg-indigo-50/30 hover:bg-indigo-50 transition-colors duration-200 text-left">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-300 transition-transform duration-200" :class="{ 'rotate-90': openStd }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-600">{{ $standardName }}</span>
                                    <span class="text-xs text-gray-400">({{ $items->count() }})</span>
                                </div>
                            </button>

                            <div x-show="openStd" x-collapse>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-100">
                                        <thead class="bg-gray-50/50">
                                            <tr>
                                                <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-400 uppercase w-12">No</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-400 uppercase">Unit</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-400 uppercase">Year</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-400 uppercase">Last Finding</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                                                <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-400 uppercase w-28">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 bg-white">
                                            @foreach($items as $a)
                                            <tr class="hover:bg-indigo-50/50 transition-colors">
                                                <td class="px-4 py-3 text-sm text-gray-400 text-center">{{ $no }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $a->unit_name }}</div>
                                                    <div class="text-xs text-gray-500">ID: {{ $a->id }}</div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $a->accreditationYear->year ?? '-' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                    @php
                                                    $lastFinding = $a->last_finding_id ? $lastFindingMap[$a->last_finding_id] ?? null : null;
                                                    @endphp
                                                    @if($lastFinding)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                        {{ $lastFinding->code }}
                                                    </span>
                                                    @else
                                                    <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @php
                                                    $statusClasses = match ($a->status) {
                                                    'submitted' => 'bg-green-100 text-green-800',
                                                    'reviewed' => 'bg-blue-100 text-blue-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                    };
                                                    @endphp
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                                        {{ ucfirst($a->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                                    <div class="flex justify-end items-center gap-3">
                                                        <a href="{{ route('assessor.assessments.report', $a) }}" target="_blank"
                                                            class="text-gray-400 hover:text-gray-600" title="Print Report">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                                            </svg>
                                                        </a>
                                                        <a href="{{ route('assessor.assessments.show', $a) }}"
                                                            class="text-gray-400 hover:text-indigo-600" title="View Details">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                        </a>
                                                        <a href="{{ route('assessor.assessments.fill', ['assessment' => $a->id, 'standard_id' => $stdId]) }}" class="text-indigo-600 hover:text-indigo-900" title="Fill Assessment">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @php $no++; @endphp
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white shadow-sm sm:rounded-lg px-6 py-10 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="mt-2 text-sm font-medium">No assessments assigned yet.</p>
            </div>
            @endforelse

        </div>
    </div>
</x-app-layout>