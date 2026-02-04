<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Asesor - Daftar Assessment</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left">Tahun Akreditasi</th>
                        <th class="p-3 text-left">Unit</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assessments as $a)
                        <tr class="border-t">
                            <td class="p-3">{{ $a->accreditationYear->year ?? '-' }}</td>
                            <td class="p-3">{{ $a->unit_name }}</td>
                            <td class="p-3">
                                @if($a->status === 'submitted')
                                    <span class="px-2 py-1 text-xs rounded bg-green-100">submitted</span>
                                @elseif($a->status === 'reviewed')
                                    <span class="px-2 py-1 text-xs rounded bg-blue-100">reviewed</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded bg-gray-100">draft</span>
                                @endif
                            </td>
                           <td class="p-3">
    <div class="flex items-center gap-3">
        {{-- Detail --}}
        <a href="{{ route('assessor.assessments.show', $a) }}"
           class="inline-flex items-center justify-center w-9 h-9 rounded border hover:bg-gray-50"
           title="Detail">
            {{-- icon eye --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
        </a>

        {{-- Isi --}}
        <a href="{{ route('assessor.assessments.fill', $a) }}"
           class="inline-flex items-center justify-center w-9 h-9 rounded border hover:bg-gray-50"
           title="Isi Assessment">
            {{-- icon pencil --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 20h9"/>
                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
            </svg>
        </a>
    </div>
</td>

                        </tr>
                    @empty
                        <tr class="border-t">
                            <td class="p-3" colspan="4">Belum ada assessment untuk kamu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $assessments->links() }}</div>
    </div>
</x-app-layout>
