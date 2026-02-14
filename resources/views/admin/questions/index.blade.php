<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Questions Bank') }}
            </h2>
            <a href="{{ route('admin.questions.create') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Create New') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded-r shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <form method="GET" action="{{ route('admin.questions.index') }}" class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[180px]">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Kategori</label>
                            <select name="category_id" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(request('category_id')==$cat->id)>
                                    {{ $cat->code ? $cat->code.' - ' : '' }}{{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 min-w-[180px]">
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
                            @if(request('category_id') || request('standard_id'))
                            <a href="{{ route('admin.questions.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">
                                Reset
                            </a>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="px-6 py-2 bg-gray-50 text-sm text-gray-600">
                    Menampilkan <strong>{{ $questions->total() }}</strong> soal
                    @if(request('category_id') || request('standard_id'))
                    (filtered)
                    @endif
                </div>
            </div>

            <!-- Nested Collapsible: Category → Standard → Questions -->
            @php $no = ($questions->currentPage() - 1) * $questions->perPage() + 1; @endphp

            @forelse($nestedGroups as $catId => $standardGroups)
            @php
            $firstQ = $standardGroups->first()->first();
            $cat = $firstQ?->category;
            $categoryName = $cat ? ($cat->code ? $cat->code.' - ' : '').$cat->name : 'Uncategorized';
            $totalInCategory = $standardGroups->flatten()->count();
            @endphp

            {{-- CATEGORY LEVEL --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-4 overflow-hidden" x-data="{ openCat: false }">
                <button type="button" @click="openCat = !openCat"
                    class="w-full flex items-center justify-between px-6 py-4 bg-indigo-600 hover:bg-indigo-700 transition-colors duration-200 text-left">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-white transition-transform duration-200" :class="{ 'rotate-90': openCat }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="text-sm font-bold text-white">{{ $categoryName }}</span>
                        <span class="text-xs font-medium text-indigo-200 bg-indigo-800 px-2.5 py-0.5 rounded-full">{{ $totalInCategory }} soal</span>
                    </div>
                </button>

                <div x-show="openCat" x-collapse>
                    @foreach($standardGroups as $stdId => $items)
                    @php
                    $std = $items->first()?->standard;
                    $standardLabel = $std ? $std->code : 'No Standard';
                    @endphp

                    {{-- STANDARD LEVEL --}}
                    <div x-data="{ openStd: false }" class="border-b border-gray-100 last:border-b-0">
                        <button type="button" @click="openStd = !openStd"
                            class="w-full flex items-center justify-between px-8 py-3 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 text-left border-l-4 border-indigo-300">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-indigo-400 transition-transform duration-200" :class="{ 'rotate-90': openStd }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <span class="text-sm font-semibold text-gray-700">{{ $standardLabel }}</span>
                                @if($std && $std->name)
                                <span class="text-xs text-gray-500">— {{ Str::limit($std->name, 60) }}</span>
                                @endif
                                <span class="text-xs font-medium text-gray-500 bg-gray-200 px-2 py-0.5 rounded-full">{{ $items->count() }} soal</span>
                            </div>
                        </button>

                        <div x-show="openStd" x-collapse>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-white">
                                        <tr>
                                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-400 uppercase w-12">No</th>
                                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-400 uppercase">Question Label</th>
                                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-400 uppercase w-24">Type</th>
                                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-400 uppercase w-24">Status</th>
                                            <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-400 uppercase w-28">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($items as $q)
                                        <tr class="hover:bg-indigo-50/50 transition-colors">
                                            <td class="px-4 py-3 text-sm text-gray-400 text-center">{{ $no }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                <div class="line-clamp-2" title="{{ $q->label }}">{{ $q->label }}</div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ ucfirst($q->type) }}</span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if($q->is_active)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                                @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                                <div class="flex justify-end items-center gap-3">
                                                    <a href="{{ route('admin.questions.edit', $q) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.questions.destroy', $q) }}" onsubmit="return confirm('Hapus soal ini?')" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Delete">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
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
            @empty
            <div class="bg-white shadow-sm sm:rounded-lg px-6 py-10 text-center text-gray-500">
                No questions found.
            </div>
            @endforelse

            {{-- Pagination --}}
            @if($questions->hasPages())
            <div class="mt-4">
                {{ $questions->links() }}
            </div>
            @endif

        </div>
    </div>
</x-app-layout>