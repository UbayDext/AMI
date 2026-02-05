<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Buat Soal</h2></x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <form method="POST" action="{{ route('admin.questions.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block mb-1">Standar</label>
                    <select name="standard_id" class="w-full border rounded p-2">
                        <option value="">-</option>
                        @foreach($standards as $s)
                            <option value="{{ $s->id }}">{{ $s->code }} - {{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-1">Label</label>
                    <input name="label" class="w-full border rounded p-2" required />
                </div>

                <div>
                    <label class="block mb-1">Tipe</label>
                    <select name="type" class="w-full border rounded p-2" required>
                        @foreach($types as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_required" value="1" />
                        Required
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" checked />
                        Aktif
                    </label>
                </div>

                <div>
    <label class="block mb-1">Kategori</label>
    <select name="category_id" class="w-full border rounded p-2">
        <option value="">-</option>
        @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected(old('category_id', $question->category_id ?? null) == $c->id)>
                {{ $c->code ? $c->code.' - ' : '' }}{{ $c->name }}
            </option>
        @endforeach
    </select>
</div>


                <div>
                    <label class="block mb-1">Urutan</label>
                    <input type="number" name="sort_order" class="w-full border rounded p-2" value="0" />
                </div>
                <div>
    <label class="block mb-1">Referensi</label>
    <textarea name="reference" rows="2" class="w-full border rounded p-2">{{ old('reference', $question->reference ?? '') }}</textarea>
    @error('reference')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
</div>


                <button class="px-4 py-2 bg-black text-white rounded">Simpan</button>
            </form>
        </div>
    </div>
</x-app-layout>
