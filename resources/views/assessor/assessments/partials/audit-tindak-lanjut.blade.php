{{-- Audit Tindak Lanjut (FM-AMI/06) --}}
<div class="bg-white dark:bg-gray-800 rounded-lg border border-teal-200 p-4 shadow-sm mt-4">
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
        <table class="min-w-full border border-gray-300 dark:border-gray-600 text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900/50">
                    <th colspan="3" class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase border-b border-gray-300 dark:border-gray-600"></th>
                    <th colspan="3" class="px-3 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase border-b border-l border-gray-300 dark:border-gray-600">Kode Auto Input</th>
                </tr>
                <tr class="bg-gray-100 dark:bg-gray-800/80">
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase border border-gray-300 dark:border-gray-600 w-1/4">Realisasi</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase border border-gray-300 dark:border-gray-600 w-28">Efektifitas</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase border border-gray-300 dark:border-gray-600 w-24">Status</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-600 dark:text-gray-400 uppercase border border-gray-300 dark:border-gray-600 w-24">Kode Standar</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-600 dark:text-gray-400 uppercase border border-gray-300 dark:border-gray-600 w-28">Kode Area</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-600 dark:text-gray-400 uppercase border border-gray-300 dark:border-gray-600 w-32">Kode PTK</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="px-2 py-2 border border-gray-300 dark:border-gray-600 align-top">
                        <textarea name="ptk_realisasi_{{ $id }}" rows="2" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm text-xs" placeholder="Realisasi tindak lanjut...">{{ $ptkRealisasi }}</textarea>
                    </td>
                    <td class="px-2 py-2 border border-gray-300 dark:border-gray-600 align-top">
                        <select name="ptk_efektifitas_{{ $id }}" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm text-xs">
                            <option class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" value="">-- Pilih --</option>
                            <option class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" value="Efektif" @selected($ptkEfektifitas==='Efektif' )>Efektif</option>
                            <option class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" value="Kurang" @selected($ptkEfektifitas==='Kurang' )>Kurang</option>
                            <option class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" value="Tidak Efektif" @selected($ptkEfektifitas==='Tidak Efektif' )>Tidak Efektif</option>
                        </select>
                    </td>
                    <td class="px-2 py-2 border border-gray-300 dark:border-gray-600 align-top">
                        <select name="ptk_tl_status_{{ $id }}" class="ptk-tl-status block w-full rounded-md shadow-sm text-xs font-semibold
                            {{ $ptkTlStatus === 'Close' ? 'bg-teal-600 text-white border-teal-700 dark:bg-teal-900/40 dark:text-teal-300 dark:border-teal-800' : ($ptkTlStatus === 'On Progress' ? 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-800' : ($ptkTlStatus === 'Open' ? 'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-900/40 dark:text-yellow-300 dark:border-yellow-800' : ($ptkTlStatus === 'Toleran' ? 'bg-orange-100 text-orange-800 border-orange-300 dark:bg-orange-900/40 dark:text-orange-300 dark:border-orange-800' : 'border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300'))) }}"
                            onchange="styleTlStatus(this)">
                            <option class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-normal" value="">-- Pilih --</option>
                            <option class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-normal" value="Open" @selected($ptkTlStatus==='Open' )>Open</option>
                            <option class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-normal" value="On Progress" @selected($ptkTlStatus==='On Progress' )>On Progress</option>
                            <option class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-normal" value="Close" @selected($ptkTlStatus==='Close' )>Close</option>
                            <option class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-normal" value="Toleran" @selected($ptkTlStatus==='Toleran' )>Toleran</option>
                        </select>
                    </td>
                    <td class="px-2 py-2 border border-gray-300 dark:border-gray-600 text-center align-middle">
                        <span class="text-xs font-mono font-semibold text-gray-700 dark:text-gray-300">{{ $kodeStandar }}</span>
                    </td>
                    <td class="px-2 py-2 border border-gray-300 dark:border-gray-600 text-center align-middle">
                        <span class="text-xs font-mono font-semibold text-gray-700 dark:text-gray-300">{{ $kodeAreaFull }}</span>
                    </td>
                    <td class="px-2 py-2 border border-gray-300 dark:border-gray-600 text-center align-middle">
                        <span class="text-xs font-mono font-semibold text-indigo-700 dark:text-indigo-400">{{ $kodePtk }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>