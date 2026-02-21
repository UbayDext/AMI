{{-- Audit Tindak Lanjut (FM-AMI/06) --}}
<div class="bg-white rounded-lg border border-teal-200 p-4 shadow-sm mt-4">
    <h5 class="text-sm font-bold text-teal-800 mb-3 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
        </svg>
        Audit Tindak Lanjut (FM-AMI/06)
    </h5>

    @php
    $ptkRealisasi = old("ptk_realisasi_$id", $ptk?->realisasi);
    $ptkEfektifitas = old("ptk_efektifitas_$id", $ptk?->efektifitas);
    $ptkTlStatus = old("ptk_tl_status_$id", $ptk?->tl_status);

    // Auto-generate codes
    $kodeStandar = $question->standard?->code ?? '-';
    $kodeArea = $assessment->unit_name ?? '-';

    // Build area code from first selected audit area
    $firstAreaName = '';
    if (!empty($ptkArea)) {
    $firstArea = collect($areas ?? [])->firstWhere('id', $ptkArea[0] ?? null);
    $firstAreaName = $firstArea?->code ?? $firstArea?->name ?? '';
    }
    $kodeAreaFull = $kodeArea . ($firstAreaName ? '-' . $firstAreaName : '');
    $kodePtk = $kodeStandar . '/' . $kodeAreaFull;
    @endphp

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th colspan="3" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase border-b border-gray-300"></th>
                    <th colspan="3" class="px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase border-b border-l border-gray-300">Kode Auto Input</th>
                </tr>
                <tr class="bg-gray-100">
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase border border-gray-300 w-1/4">Realisasi</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase border border-gray-300 w-28">Efektifitas</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase border border-gray-300 w-24">Status</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-600 uppercase border border-gray-300 w-24">Kode Standar</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-600 uppercase border border-gray-300 w-28">Kode Area</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-600 uppercase border border-gray-300 w-32">Kode PTK</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="px-2 py-2 border border-gray-300 align-top">
                        <textarea name="ptk_realisasi_{{ $id }}" rows="2" class="block w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm text-xs" placeholder="Realisasi tindak lanjut...">{{ $ptkRealisasi }}</textarea>
                    </td>
                    <td class="px-2 py-2 border border-gray-300 align-top">
                        <select name="ptk_efektifitas_{{ $id }}" class="block w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm text-xs">
                            <option value="">-- Pilih --</option>
                            <option value="Efektif" @selected($ptkEfektifitas==='Efektif' )>Efektif</option>
                            <option value="Kurang" @selected($ptkEfektifitas==='Kurang' )>Kurang</option>
                            <option value="Tidak Efektif" @selected($ptkEfektifitas==='Tidak Efektif' )>Tidak Efektif</option>
                        </select>
                    </td>
                    <td class="px-2 py-2 border border-gray-300 align-top">
                        <select name="ptk_tl_status_{{ $id }}" class="ptk-tl-status block w-full rounded-md shadow-sm text-xs font-semibold
                            {{ $ptkTlStatus === 'Close' ? 'bg-teal-600 text-white border-teal-700' : ($ptkTlStatus === 'Open' ? 'bg-yellow-100 text-yellow-800 border-yellow-300' : ($ptkTlStatus === 'Toleran' ? 'bg-orange-100 text-orange-800 border-orange-300' : 'border-gray-300')) }}"
                            onchange="styleTlStatus(this)">
                            <option value="">-- Pilih --</option>
                            <option value="Close" @selected($ptkTlStatus==='Close' )>Close</option>
                            <option value="Open" @selected($ptkTlStatus==='Open' )>Open</option>
                            <option value="Toleran" @selected($ptkTlStatus==='Toleran' )>Toleran</option>
                        </select>
                    </td>
                    <td class="px-2 py-2 border border-gray-300 text-center align-middle">
                        <span class="text-xs font-mono font-semibold text-gray-700">{{ $kodeStandar }}</span>
                    </td>
                    <td class="px-2 py-2 border border-gray-300 text-center align-middle">
                        <span class="text-xs font-mono font-semibold text-gray-700">{{ $kodeAreaFull }}</span>
                    </td>
                    <td class="px-2 py-2 border border-gray-300 text-center align-middle">
                        <span class="text-xs font-mono font-semibold text-indigo-700">{{ $kodePtk }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>