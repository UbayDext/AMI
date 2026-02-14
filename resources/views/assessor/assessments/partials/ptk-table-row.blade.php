{{-- PTK Table Row Component --}}

@php
$id = $question->id;
$ans = $answer; // passed from parent
$bukti = old("bukti_$id", $ans?->value_text);
$ket = old("ket_$id", $ans?->status ?? 'sesuai');

$ptkArea = old("ptk_area_$id", $ptk?->audit_area_ids ?? []);
$ptkKondisi = old("ptk_kondisi_$id", $ptk?->condition_desc);
$ptkAkar = old("ptk_akar_$id", $ptk?->root_cause);
$ptkAkibat = old("ptk_akibat_$id", $ptk?->impact);
$ptkRekom = old("ptk_rekom_$id", $ptk?->recommendation);
$ptkKategori = old("ptk_kategori_$id", $ptk?->category);
$ptkRencana = old("ptk_rencana_$id", $ptk?->corrective_plan);
$ptkDue = old("ptk_due_$id", optional($ptk?->due_date)->format('Y-m-d'));

// Auto-detect category from keterangan if not already saved
if (empty($ptkKategori) && !empty($ket)) {
if ($ket === 'sesuai') {
$ptkKategori = 'Sesuai';
} elseif ($ket === 'sebagian_sesuai') {
$ptkKategori = 'Observasi';
} elseif (in_array($ket, ['tidak_sesuai_tidak_ada_bukti', 'tidak_dilaksanakan_tidak_ada_bukti'])) {
$ptkKategori = 'KTS Mayor';
} elseif (str_starts_with($ket, 'tidak')) {
$ptkKategori = 'KTS Minor';
}
}

// Color coding for category
$categoryColors = [
'Sesuai' => 'bg-green-100 text-green-800 border-green-300',
'Observasi' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
'KTS Minor' => 'bg-orange-100 text-orange-800 border-orange-300',
'KTS Mayor' => 'bg-red-500 text-white border-red-700',
'OFI' => 'bg-blue-100 text-blue-800 border-blue-300',
];
$categoryColor = $categoryColors[$ptkKategori ?? ''] ?? 'bg-gray-100 text-gray-800 border-gray-300';
$ketColor = $categoryColor; // Keterangan uses same color as Kategori

// Show PTK row for all statuses (so user can see Observasi/Notes) - only hide if empty?
// Actually, asking user "jika keterangan sesuai maka kategori langsung diteksi observasi" -> implies row must be visible.
// So we show it if $ket is set.
// Only show PTK row when keterangan is NOT sesuai
$showPtk = !empty($ket) && $ket !== 'sesuai';
@endphp

<tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors" data-qid="{{ $id }}">
    {{-- No --}}
    <td class="px-3 py-4 text-sm font-medium text-gray-900 whitespace-nowrap align-top text-center border-r">
        {{ $no }}
    </td>

    {{-- Pertanyaan --}}
    <td class="px-3 py-4 text-sm text-gray-800 align-top border-r min-w-[300px]">
        <div class="font-semibold">{{ $question->label ?? $question->text }}</div>
        @if($question->standard)
        <div class="text-xs text-gray-500 mt-1">
            <span class="font-semibold">{{ $question->standard->code }}</span>
            @if($question->standard->name)
            - {{ $question->standard->name }}
            @endif
        </div>
        @endif
    </td>

    {{-- Referensi --}}
    <td class="px-3 py-4 text-sm text-gray-600 align-top border-r min-w-[100px]">
        {{ $question->reference ?? '-' }}
    </td>

    {{-- Bukti --}}
    <td class="px-3 py-4 text-sm align-top border-r space-y-2 min-w-[200px]">
        <!-- Evidence Input -->
        @if ($question->type === 'select' || $question->type === 'radio')
        <select name="bukti_{{ $id }}" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-xs">
            <option value="">Select option...</option>
            @foreach ($question->options->sortBy('sort_order') as $opt)
            <option value="{{ $opt->value }}" @selected((string)$bukti===(string)$opt->value)>{{ $opt->label }}</option>
            @endforeach
        </select>
        @elseif ($question->type === 'file')
        <input type="file" name="bukti_file_{{ $id }}" class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
        @else
        <textarea name="bukti_{{ $id }}" rows="2" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-xs" placeholder="Describe evidence...">{{ $bukti }}</textarea>
        <div class="mt-1">
            <input type="file" name="bukti_file_{{ $id }}" class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
        </div>
        @endif

        @if ($ans?->file_path)
        <div class="text-xs">
            <a href="{{ asset('storage/' . $ans->file_path) }}" target="_blank" class="text-indigo-600 hover:underline flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
                {{ basename($ans->file_path) }}
            </a>
        </div>
        @endif
        @error("bukti_$id") <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
    </td>

    {{-- Keterangan Dropdown --}}
    <td class="px-3 py-4 align-top min-w-[200px]">
        <select
            name="ket_{{ $id }}"
            class="ket-select block w-full rounded-md shadow-sm sm:text-xs font-semibold {{ $ketColor }}"
            data-qid="{{ $id }}"
            onchange="togglePtkRow({{ $id }}, this.value)">
            <option value="sesuai" @selected($ket==='sesuai' )>Sesuai</option>
            <option value="sebagian_sesuai" @selected($ket==='sebagian_sesuai' )>Sebagian Sesuai</option>
            <option value="tidak_sesuai_bukti_tidak_memadai" @selected($ket==='tidak_sesuai_bukti_tidak_memadai' )>Tidak Sesuai - Bukti tidak memadai</option>
            <option value="tidak_sesuai_ada_bukti_tidak_dilaksanakan" @selected($ket==='tidak_sesuai_ada_bukti_tidak_dilaksanakan' )>Tidak Sesuai - Ada bukti - Tidak dilaksanakan</option>
            <option value="tidak_sesuai_bukti_tidak_memadai_tidak_konsisten" @selected($ket==='tidak_sesuai_bukti_tidak_memadai_tidak_konsisten' )>Tidak Sesuai - Bukti tidak memadai - Tidak konsisten</option>
            <option value="tidak_sesuai_tidak_ada_bukti" @selected($ket==='tidak_sesuai_tidak_ada_bukti' )>Tidak Sesuai - Tidak ada bukti</option>
            <option value="tidak_dilaksanakan_tidak_ada_bukti" @selected($ket==='tidak_dilaksanakan_tidak_ada_bukti' )>Tidak dilaksanakan - Tidak ada bukti</option>
        </select>
        @error("ket_$id")
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </td>
</tr>

{{-- PTK Detail Row (shown only when status != sesuai) --}}
<tr class="ptk-row border-b border-gray-300 bg-gray-50 {{ $showPtk ? '' : 'hidden' }}" id="ptk-row-{{ $id }}">
    <td colspan="5" class="px-4 py-4">
        <div class="space-y-4">
            <!-- Reason/Notes -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Notes / Reason (Optional)</label>
                <textarea name="alasan_{{ $id }}" rows="2" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-xs" placeholder="Enter notes here...">{{ old("alasan_$id", $ans?->reason) }}</textarea>
            </div>

            <!-- PTK Form -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                <h5 class="text-sm font-bold text-red-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Detail PTK (Temuan)
                </h5>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Left Column --}}
                    <div class="space-y-3">
                        {{-- Area Audit (Using native select/checkboxes or x-multi-select if transparent) --}}
                        {{-- Since I don't see x-multi-select defined in the snippet, I'll stick to the previous implementation loop --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Area Audit</label>
                            <div class="grid grid-cols-2 gap-2 max-h-32 overflow-y-auto border border-gray-200 rounded-md p-2 bg-gray-50">
                                @foreach($areas ?? [] as $area)
                                <label class="flex items-start gap-2 text-xs hover:bg-white p-1 rounded cursor-pointer transition">
                                    <input
                                        type="checkbox"
                                        name="ptk_area_{{ $id }}[]"
                                        value="{{ $area->id }}"
                                        {{ in_array($area->id, $ptkArea) ? 'checked' : '' }}
                                        class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="flex-1">{{ $area->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Deskripsi Kondisi</label>
                            <textarea name="ptk_kondisi_{{ $id }}" rows="2" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs">{{ $ptkKondisi }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Akibat</label>
                            <textarea name="ptk_akibat_{{ $id }}" rows="2" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs">{{ $ptkAkibat }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Kategori</label>
                            {{-- Readonly text input for display - auto-detected from Keterangan --}}
                            <input
                                type="text"
                                id="ptk_kategori_display_{{ $id }}"
                                value="{{ $ptkKategori ?? '' }}"
                                readonly
                                class="block w-full rounded-md shadow-sm text-xs font-semibold cursor-not-allowed border {{ $categoryColor }}" />
                            {{-- Hidden input for actual form submission --}}
                            <input type="hidden" name="ptk_kategori_{{ $id }}" id="ptk_kategori_{{ $id }}" value="{{ $ptkKategori }}">
                            <p class="text-[10px] text-gray-500 mt-1">* Otomatis berdasarkan Keterangan</p>
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Akar Penyebab</label>
                            <textarea name="ptk_akar_{{ $id }}" rows="2" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs">{{ $ptkAkar }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Rekomendasi</label>
                            <textarea name="ptk_rekom_{{ $id }}" rows="2" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs">{{ $ptkRekom }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Rencana Perbaikan (Auditee)</label>
                            <textarea name="ptk_rencana_{{ $id }}" rows="2" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs">{{ $ptkRencana }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Due Date</label>
                            <input type="date" name="ptk_due_{{ $id }}" value="{{ $ptkDue }}" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs" />
                        </div>
                    </div>
                </div>
            </div>
    </td>
</tr>