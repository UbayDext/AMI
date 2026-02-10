@props(['item', 'level' => 0])

<tr class="bg-white border-b hover:bg-gray-50 transition-colors duration-200">
    <td class="px-6 py-4 text-sm text-gray-900">
        <div style="padding-left: {{ $level * 20 }}px" class="flex items-center">
            @if($level > 0)
            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            @endif
            <span class="{{ $level === 0 ? 'font-semibold' : '' }}">
                {{ $item->name }}
            </span>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.question-categories.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
            <form method="POST" action="{{ route('admin.question-categories.destroy', $item) }}" onsubmit="return confirm('Hapus kategori ini? Sub-kategori (jika ada) mungkin akan error jika tidak dipindahkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
            </form>
        </div>
    </td>
</tr>

@if(!empty($children))
@foreach($children as $child)
@include('admin.question-categories.partials.row', ['item' => $child['item'], 'children' => $child['children'], 'level' => $level + 1])
@endforeach
@endif