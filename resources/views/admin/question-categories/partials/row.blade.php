@props(['item', 'children' => [], 'level' => 0])

<tr class="hover:bg-indigo-50/50 dark:hover:bg-indigo-900/20 transition-colors duration-150 group">
    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 w-full">
        <div style="padding-left: {{ $level * 32 }}px" class="flex items-center gap-3">
            @if($level > 0)
            <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-gray-50 dark:bg-gray-900/50 text-gray-400 dark:text-gray-500">
                <svg class="w-4 h-4 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            @else
            <div class="flex items-center justify-center w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-900/60 text-indigo-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
            </div>
            @endif

            <span class="{{ $level === 0 ? 'font-semibold text-gray-900 dark:text-gray-100' : 'text-gray-700 dark:text-gray-300' }}">
                {{ $item->code ? $item->code . ' - ' : '' }}{{ $item->name }}
            </span>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.question-categories.edit', $item) }}"
                class="p-1.5 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/60 rounded-lg transition-colors" title="Edit">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>
            <button type="button"
                @click.prevent="deleteTargetName = '{{ addslashes($item->name) }}'; deleteFormAction = '{{ route('admin.question-categories.destroy', $item) }}'; showDeleteModal = true;"
                class="p-1.5 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/40 rounded-lg transition-colors" title="Hapus">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </td>
</tr>

@if(!empty($children))
@foreach($children as $child)
@include('admin.question-categories.partials.row', ['item' => $child['item'], 'children' => $child['children'], 'level' => $level + 1])
@endforeach
@endif