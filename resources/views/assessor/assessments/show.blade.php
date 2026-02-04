<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl">Detail Assessment</h2>
                <div class="text-sm text-gray-600">
                    Tahun: <b>{{ $assessment->accreditationYear->year ?? '-' }}</b> |
                    Unit: <b>{{ $assessment->unit_name }}</b> |
                    Status: <b>{{ $assessment->status }}</b>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('assessor.assessments.fill', $assessment) }}"
                   class="px-4 py-2 bg-black text-white rounded">
                    Isi
                </a>
                <a href="{{ route('assessor.assessments.index') }}"
                   class="px-4 py-2 border rounded">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-semibold mb-3">Ringkasan Temuan</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-3 text-left">Kode</th>
                            <th class="p-3 text-left">Standar</th>
                            <th class="p-3 text-left">Area</th>
                            <th class="p-3 text-left">Judul</th>
                            <th class="p-3 text-left">Severity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assessment->findings as $f)
                            <tr class="border-t">
                                <td class="p-3">{{ $f->code }}</td>
                                <td class="p-3">{{ $f->standard->code }}</td>
                                <td class="p-3">{{ $f->auditArea->code }}</td>
                                <td class="p-3">{{ $f->title }}</td>
                                <td class="p-3">{{ $f->severity }}</td>
                            </tr>
                        @empty
                            <tr class="border-t">
                                <td class="p-3" colspan="5">Belum ada temuan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-semibold mb-3">Jawaban (Ringkas)</h3>
            <div class="space-y-3">
                @forelse($assessment->answers as $ans)
    @php
        $q = $ans->question;
        $ptk = $ptks[$ans->question_id] ?? null;

        // untuk select/radio tampil label option
        $displayValue = $ans->value_text;
        if ($q && in_array($q->type, ['select','radio'], true) && $ans->value_text) {
            $displayValue = optional($q->options->firstWhere('value', $ans->value_text))->label ?? $ans->value_text;
        }
    @endphp

    <div class="border rounded p-3">
        <div class="font-medium">{{ $q->label ?? '-' }}</div>

        <div class="mt-1 text-xs text-gray-600">
            Keterangan: <b>{{ $ans->status ?? '-' }}</b>
        </div>

        {{-- bukti/jawaban --}}
        <div class="mt-2">
            @if($ans->file_path)
                <a class="underline text-sm" target="_blank" href="{{ asset('storage/'.$ans->file_path) }}">
                    {{ basename($ans->file_path) }}
                </a>
            @elseif($displayValue)
                <div class="text-sm text-gray-700 whitespace-pre-line">{{ $displayValue }}</div>
            @elseif($ans->value_json)
                <div class="text-sm text-gray-700">{{ json_encode($ans->value_json) }}</div>
            @else
                <div class="text-sm text-gray-400">-</div>
            @endif
        </div>

        {{-- alasan --}}
        @if(in_array($ans->status, ['sebagian','tidak'], true))
            <div class="mt-3 bg-emerald-50 border rounded p-3">
                <div class="text-sm font-semibold mb-1">Alasan / Catatan</div>
                <div class="text-sm text-gray-700 whitespace-pre-line">{{ $ans->reason ?: '-' }}</div>
            </div>
        @endif

        {{-- PTK --}}
        @if($ans->status === 'tidak')
            <div class="mt-3 bg-gray-50 border rounded p-3">
                <div class="text-sm font-semibold mb-2">PTK</div>

                @if($ptk)
                    <div class="text-sm mb-2">
                        Area Audit: <b>{{ $ptk->auditArea->name ?? $ptk->audit_area_id }}</b>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div><b>Deskripsi Kondisi:</b><div class="whitespace-pre-line">{{ $ptk->condition_desc }}</div></div>
                        <div><b>Akar Penyebab:</b><div class="whitespace-pre-line">{{ $ptk->root_cause }}</div></div>
                        <div><b>Akibat:</b><div class="whitespace-pre-line">{{ $ptk->impact }}</div></div>
                        <div><b>Rekomendasi:</b><div class="whitespace-pre-line">{{ $ptk->recommendation }}</div></div>
                        <div><b>Kategori:</b> {{ $ptk->category }}</div>
                        <div><b>Due Date:</b> {{ optional($ptk->due_date)->format('d/m/Y') ?? '-' }}</div>
                        <div class="md:col-span-2"><b>Rencana Perbaikan:</b><div class="whitespace-pre-line">{{ $ptk->corrective_plan }}</div></div>
                    </div>
                @else
                    <div class="text-sm text-gray-600">Belum ada PTK untuk jawaban ini.</div>
                @endif
            </div>
        @endif
    </div>
@empty
    <div class="text-sm text-gray-500">Belum ada jawaban.</div>
@endforelse

            </div>
        </div>
    </div>
</x-app-layout>
