<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Detail Assessment</h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white p-6 rounded shadow">
            <div><b>Tahun:</b> {{ $assessment->accreditationYear->year }}</div>
            <div><b>Unit:</b> {{ $assessment->unit_name }}</div>
            <div><b>Asesor:</b> {{ $assessment->assessor->name }}</div>
            <div><b>Status:</b> {{ $assessment->status }}</div>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-semibold mb-3">Temuan</h3>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-2 text-left">Kode</th>
                        <th class="p-2 text-left">Standar</th>
                        <th class="p-2 text-left">Area</th>
                        <th class="p-2 text-left">Judul</th>
                        <th class="p-2 text-left">Severity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assessment->findings as $f)
                        <tr class="border-t">
                            <td class="p-2">{{ $f->code }}</td>
                            <td class="p-2">{{ $f->standard->code }}</td>
                            <td class="p-2">{{ $f->auditArea->code }}</td>
                            <td class="p-2">{{ $f->title }}</td>
                            <td class="p-2">{{ $f->severity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
