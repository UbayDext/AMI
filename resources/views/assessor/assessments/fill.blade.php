<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Fill Assessment') }}
                </h2>
                <div class="text-xs text-gray-500 mt-1">
                    Unit: <span class="font-medium text-gray-700">{{ $assessment->unit_name }}</span> |
                    Status: <span class="font-medium text-gray-700">{{ ucfirst($assessment->status) }}</span>
                </div>
            </div>

            <a href="{{ route('assessor.assessments.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
            <div class="p-4 bg-green-100 border border-green-200 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="p-4 bg-red-100 border border-red-200 text-red-700 rounded-md">
                <b>Validation Failed:</b>
                <ul class="list-disc ml-5 mt-1 text-sm">
                    @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST"
                action="{{ route('assessor.assessments.fill.update', $assessment) }}"
                enctype="multipart/form-data">
                @csrf

                <!-- Questions Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">Assessment Questions</h3>
                    </div>

                    <div class="p-6">
                        @php
                        $areaOptions = $areas->map(fn($a) => ['value' => $a->id, 'label' => $a->name . ' (' . $a->code . ')'])->values()->toArray();
                        $no = 1;
                        @endphp

                        @forelse($groupedQuestions as $categoryId => $items)
                        @php
                        $cat = $items->first()?->category;
                        $catTitle = $cat
                        ? trim(($cat->code ? $cat->code.' - ' : '').$cat->name)
                        : 'Uncategorized';
                        @endphp

                        <!-- Category Header -->
                        <div class="mb-6 mt-8">
                            <h4 class="text-md font-bold text-gray-800 border-b border-gray-200 pb-2 mb-4">
                                {{ $catTitle }}
                            </h4>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">No</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Pertanyaan</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Referensi</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Bukti</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($items as $q)
                                    @php
                                    $id = $q->id;
                                    $ans = $answers[$id] ?? null;

                                    $bukti = old("bukti_$id", $ans?->value_text);
                                    $ket = old("ket_$id", $ans?->status ?? 'sesuai');
                                    $alasan = old("alasan_$id", $ans?->reason);

                                    $ptk = $ptks[$id] ?? null;

                                    $ptkArea = old("ptk_area_$id", $ptk?->audit_area_ids ?? []);
                                    $ptkKondisi = old("ptk_kondisi_$id", $ptk?->condition_desc);
                                    $ptkAkar = old("ptk_akar_$id", $ptk?->root_cause);
                                    $ptkAkibat = old("ptk_akibat_$id", $ptk?->impact);
                                    $ptkRekom = old("ptk_rekom_$id", $ptk?->recommendation);
                                    $ptkKategori= old("ptk_kategori_$id", $ptk?->category);
                                    $ptkRencana = old("ptk_rencana_$id", $ptk?->corrective_plan);
                                    $ptkDue = old("ptk_due_$id", optional($ptk?->due_date)->format('Y-m-d'));
                                    @endphp

                                    <!-- Main Row -->
                                    <tr class="hover:bg-gray-50 transition-colors" data-qid="{{ $id }}">
                                        <td class="px-3 py-4 text-sm font-medium text-gray-900 border-r align-top text-center">{{ $no++ }}</td>

                                        <td class="px-3 py-4 text-sm text-gray-800 border-r align-top">
                                            <div class="font-semibold">{{ $q->label }}</div>
                                            @if($q->standard)
                                            <div class="text-xs text-gray-500 mt-1">{{ $q->standard->code }}</div>
                                            @endif
                                        </td>

                                        <td class="px-3 py-4 text-sm text-gray-600 border-r align-top">
                                            {{ $q->reference ?? '-' }}
                                        </td>

                                        <td class="px-3 py-4 text-sm border-r align-top space-y-2">
                                            <!-- Evidence Input -->
                                            @if ($q->type === 'select' || $q->type === 'radio')
                                            <select name="bukti_{{ $id }}" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-xs">
                                                <option value="">Select option...</option>
                                                @foreach ($q->options->sortBy('sort_order') as $opt)
                                                <option value="{{ $opt->value }}" @selected((string)$bukti===(string)$opt->value)>{{ $opt->label }}</option>
                                                @endforeach
                                            </select>
                                            @elseif ($q->type === 'file')
                                            <input type="file" name="bukti_file_{{ $id }}" class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                            @else
                                            <textarea name="bukti_{{ $id }}" rows="2" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-xs" placeholder="Describe evidence...">{{ $bukti }}</textarea>
                                            <input type="file" name="bukti_file_{{ $id }}" class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
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

                                        <td class="px-3 py-4 text-sm align-top">
                                            <select name="ket_{{ $id }}" class="ket-select block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-xs" data-qid="{{ $id }}">
                                                <option value="sesuai" @selected($ket==='sesuai' )>Sesuai</option>
                                                <option value="sebagian" @selected($ket==='sebagian' )>Sebagian Sesuai</option>
                                                <option value="tidak_bukti_tidak_memadai" @selected($ket==='tidak_bukti_tidak_memadai' )>Tidak Sesuai - Bukti tidak memadai</option>
                                                <option value="tidak_ada_bukti_tidak_dilaksanakan" @selected($ket==='tidak_ada_bukti_tidak_dilaksanakan' )>Tidak Sesuai - Ada bukti - Tidak dilaksanakan</option>
                                                <option value="tidak_bukti_tidak_memadai_tidak_konsisten" @selected($ket==='tidak_bukti_tidak_memadai_tidak_konsisten' )>Tidak Sesuai - Bukti tidak memadai - Tidak konsisten</option>
                                                <option value="tidak_tidak_ada_bukti" @selected($ket==='tidak_tidak_ada_bukti' )>Tidak Sesuai - Tidak ada bukti</option>
                                                <option value="tidak_dilaksanakan_tidak_ada_bukti" @selected($ket==='tidak_dilaksanakan_tidak_ada_bukti' )>Tidak dilaksanakan - Tidak ada bukti</option>
                                            </select>
                                            @error("ket_$id")
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </td>
                                    </tr>

                                    <!-- Expandable PTK/Detail Row -->
                                    <tr id="detail-row-{{ $id }}" class="{{ $ket === 'sesuai' ? 'hidden' : '' }} bg-gray-50">
                                        <td colspan="5" class="px-4 py-4 border-b">
                                            <div class="space-y-4">
                                                <!-- Reason/Notes -->
                                                <div id="alasan-box-{{ $id }}">
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Notes / Reason (Optional)</label>
                                                    <textarea name="alasan_{{ $id }}" rows="2" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-xs" placeholder="Enter notes here...">{{ $alasan }}</textarea>
                                                </div>

                                                <!-- PTK Form -->
                                                <div id="ptk-box-{{ $id }}" class="pt-4 border-t border-gray-200 {{ $ket === 'sesuai' ? 'hidden' : '' }}">
                                                    <h5 class="text-sm font-bold text-red-800 mb-3 flex items-center gap-2">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                        </svg>
                                                        Detail PTK (Temuan)
                                                    </h5>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <!-- Left Column -->
                                                        <div class="space-y-3">
                                                            <div>
                                                                <x-multi-select
                                                                    name="ptk_area_{{ $id }}"
                                                                    label="Audit Area"
                                                                    :options="$areaOptions"
                                                                    :selected="$ptkArea"
                                                                    placeholder="Select Areas..." />
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
                                                                <select name="ptk_kategori_{{ $id }}" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs bg-gray-100 cursor-not-allowed" readonly>
                                                                    <option value="">Auto-detected...</option>
                                                                    <option value="Observasi" @selected($ptkKategori==='Observasi' )>Observasi</option>
                                                                    <option value="KTS Minor" @selected($ptkKategori==='KTS Minor' )>KTS Minor</option>
                                                                    <option value="KTS Mayor" @selected($ptkKategori==='KTS Mayor' )>KTS Mayor</option>
                                                                    <option value="OFI" @selected($ptkKategori==='OFI' )>OFI</option>
                                                                </select>
                                                                <p class="text-[10px] text-gray-500 mt-1">* Automatic based on Keterangan</p>
                                                            </div>
                                                        </div>

                                                        <!-- Right Column -->
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
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @empty
                        <div class="text-center py-10 text-gray-500">
                            No questions found for this assessment.
                        </div>
                        @endforelse
                    </div>

                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-3 sticky bottom-0 z-10 shadow-inner">
                        <button type="submit" name="submit" value="0" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Save Draft
                        </button>
                        <x-primary-button name="submit" value="1" onclick="return confirm('Are you sure you want to submit? This status will change to Submitted.')">
                            Submit Final
                        </x-primary-button>
                    </div>
                </div>
            </form>

            <!-- Additional Findings Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-8">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Additional Findings (Independent)</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('assessor.findings.store', $assessment) }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="standard_id" value="Standard" />
                                <select id="standard_id" name="standard_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Select Standard...</option>
                                    @foreach ($standards as $s)
                                    <option value="{{ $s->id }}">{{ $s->code }} - {{ $s->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('standard_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-multi-select
                                    name="audit_area_ids"
                                    label="Audit Area (Select Multiple)"
                                    :options="$areaOptions"
                                    :selected="[]"
                                    placeholder="Search & Select Areas..." />
                                <x-input-error :messages="$errors->get('audit_area_ids')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="title" value="Finding Title" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" value="Description" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        </div>

                        <div>
                            <x-input-label for="severity" value="Severity" />
                            <select id="severity" name="severity" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="minor">Minor</option>
                                <option value="major">Major</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>

                        <div class="flex justify-end">
                            <x-secondary-button type="submit">Add Finding</x-secondary-button>
                        </div>
                    </form>

                    <div class="mt-8">
                        <h4 class="font-semibold text-gray-800 mb-4">Recorded Independent Findings</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Standard</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Audit Area</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Severity</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($assessment->findings as $f)
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $f->code }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $f->standard->code }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $f->audit_area_names }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $f->title }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $f->severity === 'major' ? 'bg-red-100 text-red-800' : ($f->severity === 'critical' ? 'bg-purple-100 text-purple-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($f->severity) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-2 text-center text-sm text-gray-500">No independent findings recorded yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selects = document.querySelectorAll('.ket-select');

            function toggleRows(qid, val) {
                // Find relative to the row or by ID, but IDs are unique so ID selector is fine.
                // However, since we moved things into a detail row, we need to toggle that row.

                const detailRow = document.getElementById('detail-row-' + qid);
                const ptkBox = document.getElementById('ptk-box-' + qid);
                const selectEl = document.querySelector(`.ket-select[data-qid="${qid}"]`);
                const categorySelect = document.querySelector(`select[name="ptk_kategori_${qid}"]`);

                // Update Select Styling
                selectEl.classList.remove('bg-red-50', 'text-red-700', 'border-red-300', 'bg-yellow-50', 'text-yellow-700', 'border-yellow-300', 'bg-green-50', 'text-green-700', 'border-green-300');

                if (val && val.startsWith('tidak')) {
                    selectEl.classList.add('bg-red-50', 'text-red-700', 'border-red-300');
                } else if (val === 'sebagian') {
                    selectEl.classList.add('bg-yellow-50', 'text-yellow-700', 'border-yellow-300');
                } else {
                    selectEl.classList.add('bg-green-50', 'text-green-700', 'border-green-300');
                }

                // Show/Hide Detail Row based on Compliance
                if (val === 'sesuai') {
                    if (detailRow) detailRow.classList.add('hidden');
                    if (ptkBox) ptkBox.classList.add('hidden');
                } else {
                    if (detailRow) detailRow.classList.remove('hidden');
                    if (ptkBox) ptkBox.classList.remove('hidden');

                    // Auto-set Category Logic
                    if (categorySelect) {
                        if (val === 'sebagian') {
                            categorySelect.value = 'Observasi';
                        } else if (val === 'tidak_tidak_ada_bukti' || val === 'tidak_dilaksanakan_tidak_ada_bukti') {
                            // "Tidak Sesuai - Tidak ada bukti" OR "Tidak dilaksanakan - Tidak ada bukti" -> KTS Mayor
                            categorySelect.value = 'KTS Mayor';
                        } else if (val.startsWith('tidak')) {
                            // Other "Tidak Sesuai" -> KTS Minor
                            categorySelect.value = 'KTS Minor';
                        }
                    }
                }
            }

            selects.forEach(sel => {
                sel.addEventListener('change', e => toggleRows(sel.dataset.qid, e.target.value));
            });
        });
    </script>
</x-app-layout>