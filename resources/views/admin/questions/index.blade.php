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
            @forelse($nestedGroups as $catId => $standardGroups)
            @php
            $firstQ = $standardGroups->first()->first();
            $cat = $firstQ?->category;
            $categoryName = $cat ? ($cat->code ? $cat->code.' - ' : '').$cat->name : 'Uncategorized';
            $totalInCategory = $standardGroups->flatten()->count();
            @endphp

            @include('admin.questions.partials.category-group', [
            'categoryName' => $categoryName,
            'totalInCategory' => $totalInCategory,
            'standardGroups' => $standardGroups,
            ])
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