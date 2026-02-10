<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl">Unggah SK dan Tambahkan Data Auditor</h2>
                <p class="text-sm text-gray-600">Upload dokumen SK Auditor dan kelola periode pelaksanaan.</p>
            </div>

            <a href="{{ route('admin.auditor-decrees.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded">
                + Tambah Data
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
        @if(session('success'))
            <div class="p-3 bg-green-100 rounded">{{ session('success') }}</div>
        @endif

        {{-- Filter bar --}}
        <div class="bg-white rounded shadow p-4 flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
            <form method="GET" class="flex flex-col md:flex-row gap-3 md:items-center w-full">
                <input name="search" value="{{ request('search') }}"
                       placeholder="Cari data..."
                       class="w-full md:w-64 border rounded p-2" />

                <select name="year" class="w-full md:w-56 border rounded p-2">
                    <option value="">Semua Periode</option>
                    @foreach($years as $y)
                        <option value="{{ $y->id }}" @selected((string)request('year') === (string)$y->id)>
                            {{ $y->year }} {{ $y->label ? ' - '.$y->label : '' }}
                        </option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <button class="px-4 py-2 bg-black text-white rounded">Terapkan</button>
                    <a href="{{ route('admin.auditor-decrees.index') }}" class="px-4 py-2 border rounded">Reset</a>
                </div>
            </form>
        </div>

        {{-- Info box --}}
        <div class="bg-sky-50 border border-sky-200 rounded p-4 text-sm text-sky-900 flex items-start gap-2">
            <div class="mt-0.5">ℹ️</div>
            <div>
                Unggah dokumen Surat Keputusan (SK) Auditor serta tambahkan data auditor yang bertugas.
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left">Periode AMI</th>
                        <th class="p-3 text-left">Nomor Surat Keputusan</th>
                        <th class="p-3 text-left">Tanggal SK</th>
                        <th class="p-3 text-left">Periode Surat Keputusan</th>
                        <th class="p-3 text-left">Berkas Surat Keputusan</th>
                        <th class="p-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $row)
                        <tr class="border-t">
                            <td class="p-3">
                                {{ $row->year?->year ?? '-' }}
                                @if($row->period_label)
                                    <div class="text-xs text-gray-500">{{ $row->period_label }}</div>
                                @endif
                            </td>

                            <td class="p-3">{{ $row->decree_number ?? '-' }}</td>

                            <td class="p-3">
                                {{ $row->decree_date ? $row->decree_date->format('d M Y') : '-' }}
                            </td>

                            <td class="p-3">
                                @if($row->period_start || $row->period_end)
                                    {{ $row->period_start?->format('d M Y') ?? '-' }}
                                    -
                                    {{ $row->period_end?->format('d M Y') ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="p-3">
                                @if($row->file_path)
                                    <a class="underline"
                                       target="_blank"
                                       href="{{ asset('storage/'.$row->file_path) }}">
                                        {{ $row->original_name ?? 'File' }}
                                    </a>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>

                            <td class="p-3">
                                <div class="flex items-center gap-2">
                                    @if($row->file_path)
                                        <a class="px-2 py-1 border rounded"
                                           target="_blank"
                                           href="{{ asset('storage/'.$row->file_path) }}">
                                            👁
                                        </a>
                                    @endif

                                    <a class="px-2 py-1 border rounded"
                                       href="{{ route('admin.auditor-decrees.edit', $row) }}">
                                        ✏️
                                    </a>

                                    <button type="submit"
                                            form="delete-{{ $row->id }}"
                                            class="px-2 py-1 border rounded text-red-600"
                                            onclick="return confirm('Hapus data ini?')">
                                        🗑
                                    </button>

                                    <form id="delete-{{ $row->id }}" method="POST"
                                          action="{{ route('admin.auditor-decrees.destroy', $row) }}">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-6 text-center text-gray-500" colspan="6">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $items->links() }}</div>
    </div>
</x-app-layout>
