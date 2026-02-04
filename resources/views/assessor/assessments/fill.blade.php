<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl">Isi Assessment</h2>
                <div class="text-sm text-gray-600">
                    Unit: <b>{{ $assessment->unit_name }}</b> |
                    Status: <b>{{ $assessment->status }}</b>
                </div>
            </div>

            <a href="{{ route('assessor.assessments.index') }}" class="px-4 py-2 border rounded">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        @if (session('success'))
            <div class="p-3 bg-green-100 rounded">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="p-3 bg-red-100 rounded">
                <b>Validasi gagal:</b>
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM JAWABAN --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-semibold mb-4">Form Jawaban</h3>

            <form method="POST" action="{{ route('assessor.assessments.fill.update', $assessment) }}"
                enctype="multipart/form-data">
                @csrf

                <div class="overflow-x-auto">
                    <table class="w-full text-sm border">
                        <thead class="bg-emerald-300">
                            <tr>
                                <th class="p-3 border text-left w-12">No.</th>
                                <th class="p-3 border text-left">Pertanyaan</th>
                                <th class="p-3 border text-left w-64">Referensi</th>
                                <th class="p-3 border text-left w-80">Bukti (Berupa apa buktinya)</th>
                                <th class="p-3 border text-left w-56">Keterangan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($questions as $q)
                                @php
                                    $id = $q->id;
                                    $ans = $answers[$id] ?? null;

                                    $bukti = old("bukti_$id", $ans?->value_text);
                                    $ket = old("ket_$id", $ans?->status ?? 'sesuai');
                                    $alasan = old("alasan_$id", $ans?->reason);

                                    $ptk = $ptks[$id] ?? null;

                                    $ptkArea = old("ptk_area_$id", $ptk?->audit_area_id);
                                    $ptkKondisi = old("ptk_kondisi_$id", $ptk?->condition_desc);
                                    $ptkAkar = old("ptk_akar_$id", $ptk?->root_cause);
                                    $ptkAkibat = old("ptk_akibat_$id", $ptk?->impact);
                                    $ptkRekom = old("ptk_rekom_$id", $ptk?->recommendation);
                                    $ptkKategori = old("ptk_kategori_$id", $ptk?->category);
                                    $ptkRencana = old("ptk_rencana_$id", $ptk?->corrective_plan);
                                    $ptkDue = old("ptk_due_$id", optional($ptk?->due_date)->format('Y-m-d'));
                                @endphp

                                {{-- ROW UTAMA --}}
                                <tr class="border-t">
                                    <td class="p-3 border align-top">{{ $loop->iteration }}</td>

                                    <td class="p-3 border align-top">
                                        <div class="font-medium">{{ $q->label }}</div>
                                        <div class="text-xs text-gray-600">
                                            {{ $q->standard?->code ? 'Standar: ' . $q->standard->code : '' }}
                                        </div>
                                    </td>

                                    <td class="p-3 border align-top">
                                        <div class="whitespace-pre-line">{{ $q->reference ?? '-' }}</div>
                                    </td>

                                    <td class="p-3 border align-top space-y-2">

                                        {{-- JIKA TIPE SELECT: tampilkan dropdown Ya/Tidak dari options --}}
                                        @if ($q->type === 'select')
                                            <select name="bukti_{{ $id }}" class="w-full border rounded p-2">
                                                <option value="">- pilih -</option>
                                                @foreach ($q->options->sortBy('sort_order') as $opt)
                                                    <option value="{{ $opt->value }}" @selected($bukti == $opt->value)>
                                                        {{ $opt->label }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error("bukti_$id")
                                                <div class="text-red-600 text-xs">{{ $message }}</div>
                                            @enderror

                                            {{-- kalau kamu tetap mau upload bukti juga untuk select, aktifkan ini:
        <input type="file" name="bukti_file_{{ $id }}" class="w-full border rounded p-2">
        --}}

                                            {{-- JIKA TIPE FILE: hanya upload --}}
                                        @elseif ($q->type === 'file')
                                            <input type="file" name="bukti_file_{{ $id }}"
                                                class="w-full border rounded p-2">
                                            @error("bukti_file_$id")
                                                <div class="text-red-600 text-xs">{{ $message }}</div>
                                            @enderror

                                            @if ($ans?->file_path)
                                                <div class="text-xs">
                                                    File tersimpan:
                                                    <a class="underline" target="_blank"
                                                        href="{{ asset('storage/' . $ans->file_path) }}">
                                                        {{ basename($ans->file_path) }}
                                                    </a>
                                                </div>
                                            @endif

                                            {{-- DEFAULT: textarea + optional upload --}}
                                        @else
                                            <textarea name="bukti_{{ $id }}" rows="3" class="w-full border rounded p-2">{{ $bukti }}</textarea>
                                            @error("bukti_$id")
                                                <div class="text-red-600 text-xs">{{ $message }}</div>
                                            @enderror

                                            <input type="file" name="bukti_file_{{ $id }}"
                                                class="w-full border rounded p-2">
                                            @error("bukti_file_$id")
                                                <div class="text-red-600 text-xs">{{ $message }}</div>
                                            @enderror

                                            @if ($ans?->file_path)
                                                <div class="text-xs">
                                                    File tersimpan:
                                                    <a class="underline" target="_blank"
                                                        href="{{ asset('storage/' . $ans->file_path) }}">
                                                        {{ basename($ans->file_path) }}
                                                    </a>
                                                </div>
                                            @endif
                                        @endif

                                    </td>


                                    <td class="p-3 border align-top">
                                        <select name="ket_{{ $id }}"
                                            class="w-full border rounded p-2 ket-select"
                                            data-qid="{{ $id }}">
                                            <option value="sesuai" @selected($ket === 'sesuai')>Sesuai</option>
                                            <option value="sebagian" @selected($ket === 'sebagian')>Sebagian sesuai
                                            </option>
                                            <option value="tidak" @selected($ket === 'tidak')>Tidak sesuai</option>
                                        </select>
                                        @error("ket_$id")
                                            <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>

                                {{-- ROW ALASAN (muncul jika ket != sesuai) --}}
                                <tr id="alasan-row-{{ $id }}" class="border-t bg-emerald-50">
                                    <td class="p-3 border"></td>
                                    <td class="p-3 border font-medium">Alasan / Catatan</td>
                                    <td class="p-3 border" colspan="3">
                                        <textarea name="alasan_{{ $id }}" rows="3" class="w-full border rounded p-2"
                                            placeholder="Isi alasan jika tidak sesuai / sebagian sesuai">{{ $alasan }}</textarea>
                                        @error("alasan_$id")
                                            <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>

                                {{-- ROW PTK (muncul jika ket == tidak) --}}
                                <tr id="ptk-row-{{ $id }}" class="border-t bg-gray-50">
                                    <td class="p-3 border"></td>
                                    <td class="p-3 border font-semibold" colspan="4">
                                        <div class="flex items-center justify-between">
                                            <div>PTK (Diisi jika Keterangan = Tidak sesuai)</div>
                                            @if ($ptk?->code)
                                                <div class="text-sm">Kode: <b>{{ $ptk->code }}</b></div>
                                            @endif
                                        </div>

                                        <div class="mt-3">
                                            <label class="block text-sm mb-1">Area Audit (untuk kode PTK)</label>
                                            <select name="ptk_area_{{ $id }}"
                                                class="w-full border rounded p-2">
                                                <option value="">- pilih -</option>
                                                @foreach ($areas as $a)
                                                    <option value="{{ $a->id }}" @selected((string) $ptkArea === (string) $a->id)>
                                                        {{ $a->name }} ({{ $a->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error("ptk_area_$id")
                                                <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mt-4 overflow-x-auto">
                                            <table class="w-full text-sm border">
                                                <thead class="bg-gray-200">
                                                    <tr>
                                                        <th class="p-2 border">Deskripsi Kondisi</th>
                                                        <th class="p-2 border">Akar Penyebab</th>
                                                        <th class="p-2 border">Akibat</th>
                                                        <th class="p-2 border">Rekomendasi</th>
                                                        <th class="p-2 border">Kategori</th>
                                                        <th class="p-2 border">Rencana Perbaikan (Auditee)</th>
                                                        <th class="p-2 border">Due Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="bg-emerald-50">
                                                        <td class="p-2 border">
                                                            <textarea name="ptk_kondisi_{{ $id }}" rows="3" class="w-full border rounded p-2">{{ $ptkKondisi }}</textarea>
                                                        </td>
                                                        <td class="p-2 border">
                                                            <textarea name="ptk_akar_{{ $id }}" rows="3" class="w-full border rounded p-2">{{ $ptkAkar }}</textarea>
                                                        </td>
                                                        <td class="p-2 border">
                                                            <textarea name="ptk_akibat_{{ $id }}" rows="3" class="w-full border rounded p-2">{{ $ptkAkibat }}</textarea>
                                                        </td>
                                                        <td class="p-2 border">
                                                            <textarea name="ptk_rekom_{{ $id }}" rows="3" class="w-full border rounded p-2">{{ $ptkRekom }}</textarea>
                                                        </td>
                                                        <td class="p-2 border">
                                                            <select name="ptk_kategori_{{ $id }}"
                                                                class="w-full border rounded p-2">
                                                                <option value="">- pilih -</option>
                                                                <option value="Observasi" @selected($ptkKategori === 'Observasi')>
                                                                    Observasi</option>
                                                                <option value="Ketidaksesuaian"
                                                                    @selected($ptkKategori === 'Ketidaksesuaian')>Ketidaksesuaian
                                                                </option>
                                                                <option value="OFI" @selected($ptkKategori === 'OFI')>OFI
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td class="p-2 border">
                                                            <textarea name="ptk_rencana_{{ $id }}" rows="3" class="w-full border rounded p-2">{{ $ptkRencana }}</textarea>
                                                        </td>
                                                        <td class="p-2 border">
                                                            <input type="date" name="ptk_due_{{ $id }}"
                                                                value="{{ $ptkDue }}"
                                                                class="w-full border rounded p-2" />
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            @error("ptk_kondisi_$id")
                                                <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="mt-5 flex gap-3">
                    <button class="px-4 py-2 bg-black text-white rounded" name="submit" value="0">
                        Simpan Draft
                    </button>

                    <button class="px-4 py-2 bg-green-600 text-white rounded" name="submit" value="1"
                        onclick="return confirm('Submit assessment?')">
                        Submit
                    </button>
                </div>
            </form>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const selects = document.querySelectorAll('.ket-select');

                function toggleRows(qid, val) {
                    const alasanRow = document.getElementById('alasan-row-' + qid);
                    const ptkRow = document.getElementById('ptk-row-' + qid);

                    if (alasanRow) alasanRow.style.display = (val !== 'sesuai') ? '' : 'none';
                    if (ptkRow) ptkRow.style.display = (val === 'tidak') ? '' : 'none';
                }

                selects.forEach(sel => {
                    toggleRows(sel.dataset.qid, sel.value);
                    sel.addEventListener('change', e => toggleRows(sel.dataset.qid, e.target.value));
                });
            });
        </script>




        {{-- FORM TEMUAN --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-semibold mb-4">Temuan (PTK)</h3>

            <form method="POST" action="{{ route('assessor.findings.store', $assessment) }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1">Standar</label>
                        <select name="standard_id" class="w-full border rounded p-2" required>
                            <option value="">- pilih -</option>
                            @foreach ($standards as $s)
                                <option value="{{ $s->id }}">{{ $s->code }} - {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('standard_id')
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-1">Area Audit</label>
                        <select name="audit_area_id" class="w-full border rounded p-2" required>
                            <option value="">- pilih -</option>
                            @foreach ($areas as $a)
                                <option value="{{ $a->id }}">{{ $a->name }} ({{ $a->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('audit_area_id')
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block mb-1">Judul Temuan</label>
                    <input name="title" class="w-full border rounded p-2" required />
                    @error('title')
                        <div class="text-red-600 text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full border rounded p-2"></textarea>
                </div>

                <div>
                    <label class="block mb-1">Severity</label>
                    <select name="severity" class="w-full border rounded p-2" required>
                        <option value="minor">minor</option>
                        <option value="major">major</option>
                        <option value="critical">critical</option>
                    </select>
                </div>

                <button class="px-4 py-2 bg-black text-white rounded">
                    Tambah Temuan
                </button>
            </form>

            <hr class="my-6">

            <h4 class="font-semibold mb-2">Daftar Temuan</h4>

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
    </div>
</x-app-layout>
