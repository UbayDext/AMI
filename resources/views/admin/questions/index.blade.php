<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl">Admin - Soal</h2>
            <a href="{{ route('admin.questions.create') }}" class="px-4 py-2 bg-black text-white rounded">Buat</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left">Standar</th>
                        <th class="p-3 text-left">Label</th>
                        <th class="p-3 text-left">Tipe</th>
                        <th class="p-3 text-left">Required</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left"></th>
                    </tr>
                </thead>
               <tbody>
        @foreach($grouped as $categoryId => $items)
            @php
                $cat = $categoryId ? $items->first()->category : null;
            @endphp

            {{-- Header Kategori --}}
            <tr class="bg-gray-100">
                <td colspan="6" class="px-4 py-2 font-semibold">
                    {{ $cat ? trim(($cat->code ? $cat->code.' - ' : '').$cat->name) : 'Tanpa Kategori' }}
                </td>
            </tr>

            {{-- List Soal di kategori tersebut --}}
            @foreach($items as $q)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $q->standard?->code }}</td>
                    <td class="px-4 py-2">{{ $q->label }}</td>
                    <td class="px-4 py-2">{{ $q->type }}</td>
                    <td class="px-4 py-2">{{ $q->is_required ? 'Ya' : '-' }}</td>
                    <td class="px-4 py-2">{{ $q->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                    <td class="px-4 py-2">
                        <a class="underline" href="{{ route('admin.questions.edit', $q) }}">Edit</a>
                    </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
            </table>
        </div>

        
    </div>
</x-app-layout>
