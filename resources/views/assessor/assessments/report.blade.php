<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Assessment Report - {{ $assessment->unit_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                size: landscape;
                margin: 10mm;
            }
        }

        .sheet-table th,
        .sheet-table td {
            border: 1px solid black;
            padding: 4px;
        }
    </style>
</head>

<body class="bg-white text-black p-4 text-xs font-sans">

    <!-- Action Bar -->
    <div class="no-print fixed top-4 right-4 flex gap-2">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 font-bold">
            Print / Save as PDF
        </button>
        <a href="{{ route('assessor.assessments.fill', $assessment) }}" class="bg-gray-600 text-white px-4 py-2 rounded shadow hover:bg-gray-700">
            Back
        </a>
    </div>

    <!-- Header Section -->
    <div class="border border-black mb-1">
        <div class="grid grid-cols-12">
            <!-- Logo -->
            <div class="col-span-2 border-r border-black p-2 flex items-center justify-center">
                <img src="https://stit-hidayatunnajah.ac.id/wp-content/uploads/2022/09/Logo-STIT-Hidayatunnajah-Bekasi.png" alt="Logo" class="h-16">
            </div>
            <!-- Title -->
            <div class="col-span-8 border-r border-black text-center p-2">
                <h1 class="font-bold text-lg uppercase tracking-wide">Sekolah Tinggi Ilmu Tarbiyah Hidayatunnajah Bekasi</h1>
                <p class="text-[10px] mt-1">Laman: https://www.stithidayatunnajah.ac.id | Alamat: Jl. Raya Pebayuran KM. 08, Kertasari, Pebayuran, Bekasi, Jawa Barat 17710</p>
                <div class="border-t border-black mt-2 pt-2">
                    <h2 class="font-bold text-xl uppercase">Sistem Penjaminan Mutu Internal (SPMI)</h2>
                    <h3 class="font-bold text-lg mt-1">Audit Mutu Internal (AMI) Tahun Akademik {{ $assessment->accreditationYear->year }}</h3>
                </div>
            </div>
            <!-- Doc Info -->
            <div class="col-span-2 p-2 text-[10px]">
                <div class="grid grid-cols-2 gap-1">
                    <span class="font-bold">Kode Dokumen:</span>
                    <span>FM-AMI/02/00</span>
                    <span class="font-bold">Tanggal Audit:</span>
                    <span>{{ $assessment->created_at->format('d-m-Y') }}</span>
                    <span class="font-bold">Unit Kerja:</span>
                    <span>{{ $assessment->unit_name }}</span>
                    <span class="font-bold">Auditor:</span>
                    <span>{{ $assessment->assessor->name }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Table -->
    <table class="w-full border-collapse sheet-table text-[10px]">
        <thead>
            <tr class="bg-gray-200 text-center font-bold">
                <th rowspan="2" class="w-8">No</th>
                <th rowspan="2" class="w-16">Standar</th>
                <th rowspan="2" class="w-48">Pertanyaan</th>
                <th rowspan="2" class="w-24">Bukti / Referensi</th>
                <th rowspan="2" class="w-16">Hasil (Kesesuaian)</th>
                <th rowspan="2" class="w-32">Keterangan / Temuan</th>
                <th colspan="7">Audit Tindak Lanjut (PTK)</th>
            </tr>
            <tr class="bg-gray-200 text-center font-bold">
                <th class="w-24">Akar Penyebab</th>
                <th class="w-24">Akibat</th>
                <th class="w-24">Rekomendasi</th>
                <th class="w-16">Kategori</th>
                <th class="w-24">Rencana Perbaikan</th>
                <th class="w-16">Due Date</th>
                <th class="w-16">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($groupedQuestions as $categoryId => $questions)
            <!-- Category Row -->
            <tr class="bg-gray-100">
                <td colspan="13" class="font-bold px-2 py-1">
                    {{ $questions->first()->category->code }} - {{ $questions->first()->category->name }}
                </td>
            </tr>

            @foreach($questions as $q)
            @php
            $ans = $answers[$q->id] ?? null;
            $ptk = $ptks[$q->id] ?? null;
            $bgClass = '';
            $statusLabel = '-';

            if ($ans) {
            if ($ans->status === 'sesuai') {
            $statusLabel = 'Sesuai';
            } elseif ($ans->status === 'sebagian') {
            $bgClass = 'bg-yellow-50';
            $statusLabel = 'Sebagian Sesuai';
            } elseif (str_starts_with($ans->status, 'tidak')) {
            $bgClass = 'bg-red-50';
            $statusLabel = ucwords(str_replace('_', ' ', str_replace('tidak_', 'Tidak ', $ans->status)));
            // Special case for readability
            if ($ans->status == 'tidak_bukti_tidak_memadai') $statusLabel = 'Tidak Sesuai - Bukti tidak memadai';
            if ($ans->status == 'tidak_ada_bukti_tidak_dilaksanakan') $statusLabel = 'Tidak Sesuai - Ada bukti - Tidak dilaksanakan';
            if ($ans->status == 'tidak_bukti_tidak_memadai_tidak_konsisten') $statusLabel = 'Tidak Sesuai - Bukti tidak memadai - Tidak konsisten';
            if ($ans->status == 'tidak_tidak_ada_bukti') $statusLabel = 'Tidak Sesuai - Tidak ada bukti';
            if ($ans->status == 'tidak_dilaksanakan_tidak_ada_bukti') $statusLabel = 'Tidak dilaksanakan - Tidak ada bukti';
            }
            }
            @endphp
            <tr class="{{ $bgClass }}">
                <td class="text-center">{{ $no++ }}</td>
                <td class="text-center">{{ $q->standard->code ?? '-' }}</td>
                <td>{{ $q->label }}</td>
                <td>
                    @if($ans?->value_text)
                    <div class="italic">{{ $ans->value_text }}</div>
                    @endif
                    @if($q->reference)
                    <div class="text-[9px] text-gray-500 mt-1">Ref: {{ $q->reference }}</div>
                    @endif
                </td>
                <td class="text-center font-bold text-[10px] uppercase leading-tight">
                    {{ $statusLabel }}
                </td>
                <td>{{ $ans?->reason }}</td>

                <!-- PTK Columns -->
                <td>{{ $ptk?->root_cause }}</td>
                <td>{{ $ptk?->impact }}</td>
                <td>{{ $ptk?->recommendation }}</td>
                <td class="text-center">{{ $ptk?->category }}</td>
                <td>{{ $ptk?->corrective_plan }}</td>
                <td class="text-center">{{ $ptk?->due_date ? date('d-m-Y', strtotime($ptk->due_date)) : '' }}</td>
                <td class="text-center">{{ $ptk?->status_resolved ? 'Close' : 'Open' }}</td>
            </tr>
            @endforeach
            @endforeach

            <!-- Independent Findings -->
            @if($independentFindings->count() > 0)
            <tr class="bg-gray-100">
                <td colspan="13" class="font-bold px-2 py-1 text-center">Temuan Tambahan (Independent Findings)</td>
            </tr>
            @foreach($independentFindings as $f)
            <tr class="bg-red-50">
                <td class="text-center">-</td>
                <td class="text-center">{{ $f->standard->code }}</td>
                <td><strong>{{ $f->title }}</strong><br>{{ $f->description }}</td>
                <td>{{ $f->audit_area_names }}</td>
                <td class="text-center font-bold text-red-600">TEMUAN</td>
                <td>Severity: {{ ucfirst($f->severity) }}</td>
                <td colspan="7" class="bg-gray-100 text-center text-gray-400 italic">Independent finding - No structered PTK</td>
            </tr>
            @endforeach
            @endif

        </tbody>
    </table>

    <div class="mt-4 grid grid-cols-2 gap-8 no-print">
        <div class="text-[10px] text-gray-500">
            Generated by System at {{ date('Y-m-d H:i:s') }}
        </div>
    </div>

</body>

</html>