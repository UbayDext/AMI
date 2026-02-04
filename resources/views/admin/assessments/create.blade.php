<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Buat Assessment</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <form method="POST" action="{{ route('admin.assessments.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block mb-1">Tahun Akreditasi</label>
                    <select name="accreditation_year_id" class="w-full border rounded p-2" required>
                        @foreach($years as $y)
                            <option value="{{ $y->id }}">{{ $y->year }}</option>
                        @endforeach
                    </select>
                    @error('accreditation_year_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block mb-1">Asesor</label>
                    <select name="assessor_id" class="w-full border rounded p-2" required>
                        @foreach($assessors as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                    @error('assessor_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block mb-1">Unit</label>
                    <input name="unit_name" class="w-full border rounded p-2" required />
                    @error('unit_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>

                <button class="px-4 py-2 bg-black text-white rounded">Simpan</button>
            </form>
        </div>
    </div>
</x-app-layout>
