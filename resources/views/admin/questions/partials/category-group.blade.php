@props(['categoryName', 'totalInCategory', 'standardGroups'])

{{-- CATEGORY LEVEL --}}
<div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-4 overflow-hidden" x-data="{ openCat: false }">
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
        @php $currentNo = 1; @endphp
        @php
        $std = $items->first()?->standard;
        $standardLabel = $std ? $std->code : 'No Standard';
        @endphp

        {{-- STANDARD LEVEL --}}
        <div x-data="{ openStd: false }" class="border-b border-gray-100 dark:border-gray-700 last:border-b-0">
            <button type="button" @click="openStd = !openStd"
                class="w-full flex items-center justify-between px-8 py-3 bg-gray-50 dark:bg-gray-900/50 hover:bg-gray-100 transition-colors duration-200 text-left border-l-4 border-indigo-300">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-indigo-400 transition-transform duration-200" :class="{ 'rotate-90': openStd }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $standardLabel }}</span>
                    @if($std && $std->name)
                    <span class="text-xs text-gray-500 dark:text-gray-400">— {{ Str::limit($std->name, 60) }}</span>
                    @endif
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-200 dark:bg-gray-700 px-2 py-0.5 rounded-full">{{ $items->count() }} soal</span>
                </div>
            </button>

            <div x-show="openStd" x-collapse>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-400 dark:text-gray-500 uppercase w-12">No</th>
                                <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-400 dark:text-gray-500 uppercase">Question Label</th>
                                <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-400 dark:text-gray-500 uppercase w-24">Type</th>
                                <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-400 dark:text-gray-500 uppercase w-24">Status</th>
                                <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-400 dark:text-gray-500 uppercase w-28">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($items as $q)
                            <tr class="hover:bg-indigo-50/50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-400 dark:text-gray-500 text-center">{{ $currentNo }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    <div class="line-clamp-2" title="{{ $q->label }}">{{ $q->label }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ ucfirst($q->type) }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($q->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-gray-800/80 text-gray-800 dark:text-gray-200">Inactive</span>
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
                            @php $currentNo++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>