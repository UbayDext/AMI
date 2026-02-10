{{-- PTK Table Row Component --}}

@php
$id = $question->id;
$ket = old("ket_$id", $answer?->status ?? 'sesuai');
$ptkArea = old("ptk_area_$id", $ptk?->audit_area_ids ?? []);
$ptkKondisi = old("ptk_kondisi_$id", $ptk?->condition_desc);
$ptkAkar = old("ptk_akar_$id", $ptk?->root_cause);
$ptkAkibat = old("ptk_akibat_$id", $ptk?->impact);
$ptkRekom = old("ptk_rekom_$id", $ptk?->recommendation);
$ptkKategori = old("ptk_kategori_$id", $ptk?->category);

// Color coding for category
$categoryColors = [
'observasi' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
'ketidaksesuaian' => 'bg-red-100 text-red-800 border-red-300',
'ofi' => 'bg-blue-100 text-blue-800 border-blue-300',
];
$categoryColor = $categoryColors[strtolower($ptkKategori ?? '')] ?? 'bg-gray-100 text-gray-800 border-gray-300';

// Show PTK row only if status is not "sesuai"
$showPtk = $ket !== 'sesuai';
@endphp

<tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors" data-qid="{{ $id }}">
    {{-- No --}}
    <td class="px-3 py-3 text-sm font-medium text-gray-900 whitespace-nowrap align-top">
        {{ $no }}
    </td>

    {{-- Pertanyaan --}}
    <td class="px-3 py-3 text-sm text-gray-700 align-top min-w-[300px]">
        <div class="font-medium mb-1">{{ $question->text }}</div>
        @if($question->standard)
        <div class="text-xs text-gray-500">
            <span class="font-semibold">{{ $question->standard->code }}</span>
            @if($question->standard->name)
            - {{ $question->standard->name }}
            @endif
        </div>
        @endif
    </td>

    {{-- Keterangan Dropdown --}}
    <td class="px-3 py-3 align-top min-w-[200px]">
        <select
            name="ket_{{ $id }}"
            class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 keterangan-select"
            data-qid="{{ $id }}"
            onchange="togglePtkRow({{ $id }}, this.value)">
            <option value="sesuai" {{ $ket === 'sesuai' ? 'selected' : '' }}>Sesuai</option>
            <option value="sebagian_sesuai" {{ $ket === 'sebagian_sesuai' ? 'selected' : '' }}>Sebagian sesuai</option>
            <option value="tidak_sesuai_bukti_tidak_memadai" {{ $ket === 'tidak_sesuai_bukti_tidak_memadai' ? 'selected' : '' }}>Tidak sesuai – Bukti tidak memadai</option>
            <option value="tidak_sesuai_ada_bukti_tidak_dilaksanakan" {{ $ket === 'tidak_sesuai_ada_bukti_tidak_dilaksanakan' ? 'selected' : '' }}>Tidak sesuai – Ada bukti – Tidak dilaksanakan</option>
            <option value="tidak_sesuai_bukti_tidak_memadai_tidak_konsisten" {{ $ket === 'tidak_sesuai_bukti_tidak_memadai_tidak_konsisten' ? 'selected' : '' }}>Tidak sesuai – Bukti tidak memadai – Tidak konsisten</option>
            <option value="tidak_sesuai_tidak_ada_bukti" {{ $ket === 'tidak_sesuai_tidak_ada_bukti' ? 'selected' : '' }}>Tidak sesuai – Tidak ada bukti</option>
            <option value="tidak_dilaksanakan_tidak_ada_bukti" {{ $ket === 'tidak_dilaksanakan_tidak_ada_bukti' ? 'selected' : '' }}>Tidak dilaksanakan – Tidak ada bukti</option>
        </select>
    </td>
</tr>

{{-- PTK Detail Row (shown only when status != sesuai) --}}
<tr class="ptk-row border-b border-gray-300 bg-gray-50 {{ $showPtk ? '' : 'hidden' }}" id="ptk-row-{{ $id }}">
    <td colspan="3" class="px-3 py-4">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h4 class="text-xs font-semibold text-gray-700 mb-3 uppercase tracking-wide">Detail PTK</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Deskripsi Kondisi --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Deskripsi Kondisi</label>
                    <textarea
                        name="ptk_kondisi_{{ $id }}"
                        rows="2"
                        class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Jelaskan kondisi yang ditemukan...">{{ $ptkKondisi }}</textarea>
                </div>

                {{-- Akar Penyebab --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Akar Penyebab</label>
                    <textarea
                        name="ptk_akar_{{ $id }}"
                        rows="2"
                        class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Jelaskan akar penyebab...">{{ $ptkAkar }}</textarea>
                </div>

                {{-- Akibat --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Akibat</label>
                    <textarea
                        name="ptk_akibat_{{ $id }}"
                        rows="2"
                        class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Jelaskan dampak/akibat...">{{ $ptkAkibat }}</textarea>
                </div>

                {{-- Rekomendasi --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Rekomendasi</label>
                    <textarea
                        name="ptk_rekom_{{ $id }}"
                        rows="2"
                        class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Jelaskan rekomendasi perbaikan...">{{ $ptkRekom }}</textarea>
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kategori</label>
                    <select
                        name="ptk_kategori_{{ $id }}"
                        class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $categoryColor }}">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="observasi" {{ strtolower($ptkKategori ?? '') === 'observasi' ? 'selected' : '' }}>Observasi</option>
                        <option value="ketidaksesuaian" {{ strtolower($ptkKategori ?? '') === 'ketidaksesuaian' ? 'selected' : '' }}>Ketidaksesuaian</option>
                        <option value="ofi" {{ strtolower($ptkKategori ?? '') === 'ofi' ? 'selected' : '' }}>OFI (Opportunity for Improvement)</option>
                    </select>
                </div>

                {{-- Area Audit (multi-select) --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Area Audit</label>
                    <select
                        name="ptk_area_{{ $id }}[]"
                        multiple
                        class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        style="height: 60px;">
                        @foreach($areas ?? [] as $area)
                        <option value="{{ $area->id }}" {{ in_array($area->id, $ptkArea) ? 'selected' : '' }}>
                            {{ $area->name }} ({{ $area->code }})
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd untuk pilih multiple</p>
                </div>
            </div>
        </div>
    </td>
</tr>