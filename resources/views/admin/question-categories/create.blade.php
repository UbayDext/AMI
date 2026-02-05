<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Buat Kategori Soal</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white p-6 rounded shadow">
            <form method="POST" action="{{ route('admin.question-categories.store') }}" class="space-y-4">
                @csrf

                {{-- <div>
                    <label class="block mb-1">Parent (opsional)</label>
                    <select name="parent_id" class="w-full border rounded p-2">
                        <option value="">-</option>
                        @foreach($parents as $p)
                            <option value="{{ $p->id }}" @selected(old('parent_id') == $p->id)>
                                {{ ($p->code ? $p->code.' - ' : '') . $p->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div> --}}

                <div>
                    <label class="block mb-1">Kode (opsional)</label>
                    <input name="code" class="w-full border rounded p-2" value="{{ old('code') }}" placeholder="Contoh: C / C.2 / C.2.1" />
                    @error('code')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block mb-1">Nama</label>
                    <input name="name" class="w-full border rounded p-2" value="{{ old('name') }}" required />
                    @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block mb-1">Urutan</label>
                    <input type="number" name="sort_order" class="w-full border rounded p-2" value="{{ old('sort_order', 0) }}" />
                    @error('sort_order')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>

                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) />
                    Aktif
                </label>

                <button class="px-4 py-2 bg-black text-white rounded" type="submit">Simpan</button>
            </form>
        </div>
    </div>
</x-app-layout>
