<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl">Admin - Kategori Soal</h2>
            <a href="{{ route('admin.question-categories.create') }}" class="px-4 py-2 bg-black text-white rounded">
                Buat
            </a>
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
                        <th class="p-3 text-left">Kode</th>
                        <th class="p-3 text-left">Nama</th>
                        {{-- <th class="p-3 text-left">Parent</th> --}}
                        <th class="p-3 text-left">Urutan</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $render = function ($nodes, $level = 0) use (&$render) {
                            foreach ($nodes as $node) {
                                $c = $node['item'];
                                echo '<tr class="border-b">';
                                echo '<td class="p-3">'.e($c->code ?? '-').'</td>';
                                echo '<td class="p-3">'.str_repeat('&nbsp;&nbsp;&nbsp;', $level).e($c->name).'</td>';
                                // echo '<td class="p-3">'.e($c->parent?->name ?? '-').'</td>';
                                echo '<td class="p-3">'.e($c->sort_order).'</td>';
                                echo '<td class="p-3">'.($c->is_active ? 'Aktif' : 'Nonaktif').'</td>';
                                echo '<td class="p-3 flex gap-3">';
                                echo '<a class="underline" href="'.route('admin.question-categories.edit', $c).'">Edit</a>';
                                echo '<form method="POST" action="'.route('admin.question-categories.destroy', $c).'" onsubmit="return confirm(\'Hapus kategori ini?\')">';
                                echo csrf_field().method_field('DELETE');
                                echo '<button class="underline text-red-600" type="submit">Hapus</button>';
                                echo '</form>';
                                echo '</td>';
                                echo '</tr>';

                                if (!empty($node['children'])) {
                                    $render($node['children'], $level + 1);
                                }
                            }
                        };
                    @endphp

                    @if($tree->isEmpty())
                        <tr><td colspan="6" class="p-6 text-center text-gray-500">Belum ada kategori.</td></tr>
                    @else
                        {!! $render($tree, 0) !!}
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
