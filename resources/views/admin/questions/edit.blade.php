<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Soal</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
        @if(session('success'))
            <div class="p-3 bg-green-100 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white p-6 rounded shadow">
            {{-- FORM UPDATE --}}
            <form method="POST" action="{{ route('admin.questions.update', $question) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block mb-1">Standar</label>
                    <select name="standard_id" class="w-full border rounded p-2">
                        <option value="">-</option>
                        @foreach($standards as $s)
                            <option value="{{ $s->id }}" @selected((int)$question->standard_id === (int)$s->id)>
                                {{ $s->code }} - {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('standard_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block mb-1">Label</label>
                    <input name="label" class="w-full border rounded p-2" value="{{ old('label', $question->label) }}" required />
                    @error('label')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block mb-1">Tipe</label>
                    <select name="type" class="w-full border rounded p-2" required>
                        @foreach($types as $t)
                            <option value="{{ $t }}" @selected(old('type', $question->type) === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                    @error('type')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>

                <div class="flex gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_required" value="1" @checked(old('is_required', $question->is_required)) />
                        Required
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $question->is_active)) />
                        Aktif
                    </label>
                </div>

                <div>
                    <label class="block mb-1">Urutan</label>
                    <input type="number" name="sort_order" class="w-full border rounded p-2"
                           value="{{ old('sort_order', $question->sort_order) }}" />
                    @error('sort_order')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block mb-1">Referensi</label>
                    <textarea name="reference" rows="3" class="w-full border rounded p-2">{{ old('reference', $question->reference ?? '') }}</textarea>
                    @error('reference')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="px-4 py-2 bg-black text-white rounded">
                        Update
                    </button>

                    {{-- tombol delete, tapi FORM-nya di luar --}}
                    <button type="submit"
                            form="delete-question-form"
                            class="px-4 py-2 bg-red-600 text-white rounded"
                            onclick="return confirm('Hapus soal ini?')">
                        Hapus
                    </button>
                </div>
            </form>

            {{-- FORM DELETE (di luar form update) --}}
            <form id="delete-question-form" method="POST" action="{{ route('admin.questions.destroy', $question) }}">
                @csrf
                @method('DELETE')
            </form>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-semibold mb-2">Catatan</h3>
            <p class="text-sm text-gray-600">
                Jika tipe <b>select</b>, sistem akan otomatis membuat opsi <b>Ya / Tidak</b>.
            </p>
        </div>
    </div>
</x-app-layout>
