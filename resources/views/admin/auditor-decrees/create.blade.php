<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Tambah SK Auditor</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <form method="POST" action="{{ route('admin.auditor-decrees.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block mb-1">Periode (Tahun Akreditasi)</label>
                    <select name="accreditation_year_id" class="w-full border rounded p-2">
                        <option value="">-</option>
                        @foreach($years as $y)
                            <option value="{{ $y->id }}" @selected(old('accreditation_year_id') == $y->id)>{{ $y->year }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-1">Nomor SK</label>
                    <input name="decree_number" class="w-full border rounded p-2" value="{{ old('decree_number') }}" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block mb-1">Tanggal SK</label>
                        <input type="date" name="decree_date" class="w-full border rounded p-2" value="{{ old('decree_date') }}" />
                    </div>
                    <div>
                        <label class="block mb-1">Mulai</label>
                        <input type="date" name="period_start" class="w-full border rounded p-2" value="{{ old('period_start') }}" />
                    </div>
                    <div>
                        <label class="block mb-1">Selesai</label>
                        <input type="date" name="period_end" class="w-full border rounded p-2" value="{{ old('period_end') }}" />
                    </div>
                </div>

                <div>
                    <label class="block mb-1">Berkas SK (PDF)</label>
                    <input type="file" name="file" class="w-full border rounded p-2" />
                </div>

                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked />
                    Aktif
                </label>

                <div class="flex gap-2">
                    <button class="px-4 py-2 bg-black text-white rounded">Simpan</button>
                    <a class="px-4 py-2 border rounded" href="{{ route('admin.auditor-decrees.index') }}">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
