<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl">Admin - Assessments</h2>
            <a href="{{ route('admin.assessments.create') }}" class="px-4 py-2 bg-black text-white rounded">Buat</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left">Tahun</th>
                        <th class="p-3 text-left">Unit</th>
                        <th class="p-3 text-left">Asesor</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($assessments as $a)
                        <tr class="border-t">
                            <td class="p-3">{{ $a->accreditationYear->year }}</td>
                            <td class="p-3">{{ $a->unit_name }}</td>
                            <td class="p-3">{{ $a->assessor->name }} ({{ $a->assessor->email }})</td>
                            <td class="p-3">{{ $a->status }}</td>

                            <td class="p-3 flex gap-3">
                                <a class="underline" href="{{ route('admin.assessments.show', $a) }}">Detail</a>

                                <form method="POST" action="{{ route('admin.assessments.destroy', $a) }}"
                                    onsubmit="return confirm('Yakin hapus assessment ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

        <div class="mt-4">{{ $assessments->links() }}</div>
    </div>
</x-app-layout>
