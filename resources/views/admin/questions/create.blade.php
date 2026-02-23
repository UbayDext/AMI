<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight sr-only">Tambah Soal Baru</h2>
    </x-slot>

    {{-- Full-screen centered card on gray bg --}}
    <div class="min-h-[calc(100vh-4rem)] bg-gray-100 dark:bg-gray-800/80 flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-xl bg-white dark:bg-gray-800 rounded-3xl shadow-lg p-8 relative">

            {{-- Close / Back button --}}
            <a href="{{ route('admin.questions.index') }}"
                class="absolute top-5 right-5 w-8 h-8 flex items-center justify-center rounded-full text-gray-400 dark:text-gray-500 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>

            {{-- Header --}}
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Tambah Soal Baru</h1>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Isi detail pertanyaan untuk bank soal akreditasi.</p>
            </div>

            {{-- Validation errors --}}
            @if($errors->any())
            <div class="mb-5 p-4 bg-red-50 border border-red-100 rounded-2xl text-sm text-red-700">
                <ul class="list-disc ml-4 space-y-0.5">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.questions.store') }}" class="space-y-5">
                @csrf

                {{-- Kategori Soal --}}
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Kategori Soal
                    </label>
                    <div class="relative">
                        <select id="category_id" name="category_id"
                            class="w-full appearance-none bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl pl-4 pr-10 py-3 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition-colors">
                            <option value="" class="dark:bg-gray-800">Pilih Kategori (misal: PBA)</option>
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}" class="dark:bg-gray-800" {{ old('category_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->code ? $c->code.' - ' : '' }}{{ $c->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Standar + Tipe row --}}
                <div class="grid grid-cols-2 gap-4">
                    {{-- Standar Akreditasi --}}
                    <div>
                        <label for="standard_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Standar Akreditasi
                        </label>
                        <div class="relative">
                            <select id="standard_id" name="standard_id"
                                class="w-full appearance-none bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl pl-4 pr-10 py-3 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition-colors">
                                <option value="" class="dark:bg-gray-800">Pilih Standar</option>
                                @foreach($standards as $s)
                                <option value="{{ $s->id }}" class="dark:bg-gray-800" {{ old('standard_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->code }} - {{ Str::limit($s->name, 30) }}
                                </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Tipe Soal --}}
                    <div>
                        <label for="type" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Tipe Soal
                        </label>
                        <div class="relative">
                            <select id="type" name="type" required
                                class="w-full appearance-none border rounded-2xl pl-4 pr-10 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition-colors
                                    {{ $errors->has('type') ? 'bg-red-50 border-red-300 text-red-500' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200' }}">
                                <option value="" class="dark:bg-gray-800">Pilih Tipe</option>
                                @foreach($types as $t)
                                <option value="{{ $t }}" class="dark:bg-gray-800" {{ old('type') == $t ? 'selected' : '' }}>
                                    {{ ucfirst($t) }}
                                </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 {{ $errors->has('type') ? 'text-red-400' : 'text-gray-400 dark:text-gray-500' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        @error('type')
                        <p class="mt-1 text-xs text-red-500 font-medium">Wajib dipilih</p>
                        @enderror
                    </div>
                </div>

                {{-- Isi Pertanyaan --}}
                <div>
                    <label for="label" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Isi Pertanyaan
                    </label>
                    <input id="label" name="label" type="text" required
                        value="{{ old('label') }}"
                        placeholder="Tulis pertanyaan singkat disini..."
                        class="w-full bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-3 text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition-colors" />
                    @error('label')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="reference" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Deskripsi
                    </label>
                    <textarea id="reference" name="reference" rows="5"
                        placeholder="Tulis pertanyaan lengkap atau deskripsi kebutuhan dokumen disini..."
                        class="w-full bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-3 text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 resize-none transition-colors">{{ old('reference') }}</textarea>
                    @error('reference')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Toggles: Required & Active (compact) --}}
                <div class="flex items-center gap-6 pt-1">
                    <label for="is_required" class="flex items-center gap-2 cursor-pointer select-none">
                        <input id="is_required" type="checkbox" name="is_required" value="1"
                            class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500"
                            {{ old('is_required') ? 'checked' : '' }}>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Pertanyaan wajib</span>
                    </label>

                    <label for="is_active" class="flex items-center gap-2 cursor-pointer select-none">
                        <input id="is_active" type="checkbox" name="is_active" value="1"
                            class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500"
                            {{ old('is_active', true) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Aktif</span>
                    </label>
                </div>

                {{-- Action buttons --}}
                <div class="flex items-center justify-end gap-4 pt-3 border-t border-gray-100 dark:border-gray-700 mt-2">
                    <a href="{{ route('admin.questions.index') }}"
                        class="px-5 py-2.5 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-2xl shadow-sm hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>