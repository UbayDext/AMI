<x-app-layout>
    <x-slot name="header"><span class="sr-only">Edit Soal</span></x-slot>

    {{-- Split layout: blurred list backdrop on left, panel on right --}}
    <div class="flex min-h-[calc(100vh-4rem)]">

        {{-- LEFT: blurred back-drop showing Bank Soal context --}}
        <div class="hidden lg:flex flex-1 items-start justify-center pt-16 px-10 bg-gray-100 dark:bg-gray-800/80 relative overflow-hidden select-none pointer-events-none">
            <div class="w-full max-w-xl opacity-40 blur-[2px] space-y-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-700 dark:text-gray-300">Bank Soal</h2>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Kelola database pertanyaan untuk akreditasi.</p>
                </div>
                {{-- Fake card skeletons --}}
                @for($i = 0; $i < 3; $i++)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 space-y-3">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded-full w-3/4"></div>
                    <div class="h-3 bg-gray-100 dark:bg-gray-800/80 rounded-full w-1/2"></div>
                    <div class="h-3 bg-gray-100 dark:bg-gray-800/80 rounded-full w-2/3"></div>
            </div>
            @endfor
        </div>
    </div>

    {{-- RIGHT: Edit panel --}}
    <div class="w-full lg:w-[480px] xl:w-[520px] flex-shrink-0 bg-white dark:bg-gray-800 shadow-2xl flex flex-col min-h-full">

        {{-- Panel header --}}
        <div class="flex items-center gap-3 px-6 pt-6 pb-4 border-b border-gray-100 dark:border-gray-700">
            <div class="flex-shrink-0 w-9 h-9 bg-indigo-100 dark:bg-indigo-900/60 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-base font-bold text-gray-900 dark:text-gray-100">Edit Soal #{{ $question->id }}</h1>
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    @if($question->updated_at->diffInMinutes(now()) < 2)
                        Last edited just now
                        @else
                        Last edited {{ $question->updated_at->diffForHumans() }}
                        @endif
                        </p>
            </div>
            <a href="{{ route('admin.questions.index') }}"
                class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full text-gray-400 dark:text-gray-500 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>

        {{-- Scrollable form body --}}
        <div class="flex-1 overflow-y-auto px-6 py-5">

            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition
                x-init="setTimeout(() => show = false, 3000)"
                class="mb-5 flex items-center gap-2.5 p-3 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-5 p-3 bg-red-50 border border-red-100 rounded-xl text-xs text-red-600">
                <ul class="list-disc ml-3 space-y-0.5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form id="edit-form" method="POST"
                action="{{ route('admin.questions.update', $question) }}"
                class="space-y-5 pb-8">
                @csrf
                @method('PUT')

                {{-- Pertanyaan --}}
                <div>
                    <label for="label" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pertanyaan</label>
                    <div class="relative">
                        <textarea id="label" name="label" rows="5" required
                            class="w-full bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-2xl px-4 pt-3 pb-8 text-sm text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 resize-none transition-colors">{{ old('label', $question->label) }}</textarea>
                        <span class="absolute bottom-3 right-4 text-[10px] text-gray-400 dark:text-gray-500 pointer-events-none">Markdown enabled</span>
                    </div>
                    @error('label')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kategori + Standar --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="category_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Kategori Soal</label>
                        <div class="relative">
                            <select id="category_id" name="category_id"
                                class="w-full appearance-none bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-2xl pl-4 pr-8 py-2.5 text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition-colors">
                                <option value="">— Pilih —</option>
                                @foreach($categories as $c)
                                <option value="{{ $c->id }}"
                                    @selected(old('category_id', $question->category_id) == $c->id)>
                                    {{ ($c->code ? $c->code.' - ' : '').$c->name }}
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
                    <div>
                        <label for="standard_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Standar Akreditasi</label>
                        <div class="relative">
                            <select id="standard_id" name="standard_id"
                                class="w-full appearance-none bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-2xl pl-4 pr-8 py-2.5 text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition-colors">
                                <option value="">— Pilih —</option>
                                @foreach($standards as $s)
                                <option value="{{ $s->id }}"
                                    @selected(old('standard_id', $question->standard_id) == $s->id)>
                                    {{ $s->code }} - {{ Str::limit($s->name, 25) }}
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
                </div>

                {{-- Tipe Jawaban — pill toggle buttons --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tipe Jawaban</label>
                    @php
                    $typeLabels = [
                    'textarea' => ['Essay', 'M 4 6h16M4 12h16M4 18h8'],
                    'text' => ['Teks Singkat', 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5'],
                    'number' => ['Angka', 'M7 20l4-16m2 16l4-16M6 9h14M4 15h14'],
                    'select' => ['Pilihan Ganda', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                    'radio' => ['Radio', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'checkbox' => ['Checklist', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                    'file' => ['File Upload', 'M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13'],
                    ];
                    $currentType = old('type', $question->type);
                    @endphp

                    <div class="flex flex-wrap gap-2" x-data="{ selected: '{{ $currentType }}' }">
                        @foreach($typeLabels as $val => [$label, $iconPath])
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="{{ $val }}"
                                x-model="selected"
                                class="sr-only" {{ $currentType === $val ? 'checked' : '' }}>
                            <span
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-full border-2 transition-all duration-150"
                                :class="selected === '{{ $val }}'
                                        ? 'bg-amber-50 border-amber-300 text-amber-700'
                                        : 'bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-indigo-300 hover:text-indigo-600'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}" />
                                </svg>
                                {{ $label }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                    @error('type')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status Aktif toggle --}}
                <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-2xl px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Status Aktif</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Soal ini akan muncul di audit.</p>
                        </div>
                    </div>
                    {{-- Toggle switch --}}
                    <label class="relative inline-flex items-center cursor-pointer" x-data="{ on: {{ old('is_active', $question->is_active) ? 'true' : 'false' }} }">
                        <input type="checkbox" name="is_active" value="1" class="sr-only" x-model="on"
                            {{ old('is_active', $question->is_active) ? 'checked' : '' }}>
                        <div class="w-11 h-6 rounded-full transition-colors duration-200"
                            :class="on ? 'bg-emerald-500' : 'bg-gray-300'">
                            <div class="w-5 h-5 bg-white dark:bg-gray-800 rounded-full shadow mt-0.5 transition-transform duration-200"
                                :class="on ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'"></div>
                        </div>
                    </label>
                </div>

                {{-- Pertanyaan Wajib --}}
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_required" value="1"
                        class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500"
                        {{ old('is_required', $question->is_required) ? 'checked' : '' }}>
                    <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">Pertanyaan wajib diisi</span>
                </label>

                {{-- Deskripsi / Catatan Auditor --}}
                <div>
                    <label for="reference" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Catatan Auditor
                        <span class="font-normal text-gray-400 dark:text-gray-500">(Optional)</span>
                    </label>
                    <textarea id="reference" name="reference" rows="4"
                        placeholder="Tambahkan catatan khusus untuk auditor..."
                        class="w-full bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-3 text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 resize-none transition-colors">{{ old('reference', $question->reference ?? '') }}</textarea>
                </div>

                {{-- Sort Order (hidden, keep existing) --}}
                <input type="hidden" name="sort_order" value="{{ old('sort_order', $question->sort_order) }}">

                {{-- Meta info --}}
                <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500 pt-1 border-t border-gray-100 dark:border-gray-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Dibuat pada {{ $question->created_at->format('d M Y') }}
                    · Diperbarui {{ $question->updated_at->diffForHumans() }}
                </div>

            </form>
        </div>

        {{-- Footer --}}
        <div class="border-t border-gray-100 dark:border-gray-700 px-6 py-4 space-y-2.5 bg-white dark:bg-gray-800">
            <button form="edit-form" type="submit"
                class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-2xl hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                Update Changes
            </button>

            {{-- Danger zone --}}
            <button type="button"
                @click="$dispatch('open-delete-modal')"
                class="w-full flex items-center justify-center gap-2 px-6 py-2.5 text-red-500 text-sm font-medium rounded-2xl hover:bg-red-50 border border-transparent hover:border-red-100 transition-all duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Hapus Soal
            </button>
        </div>
    </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div
        x-data="{ show: false }"
        @open-delete-modal.window="show = true"
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center"
        style="display: none;">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-gray-200 dark:bg-gray-700/60 backdrop-blur-sm" @click="show = false"></div>

        {{-- Card --}}
        <div class="relative z-10 bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-8 max-w-xs w-full mx-4 text-center">

            {{-- Pink trash icon --}}
            <div class="flex justify-center mb-5">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
            </div>

            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">Hapus Soal ini?</h2>
            <p class="text-sm text-gray-400 dark:text-gray-500 mb-7">
                Tindakan ini tidak bisa dibatalkan dan data<br>akan hilang selamanya.
            </p>

            <form method="POST" action="{{ route('admin.questions.destroy', $question) }}">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-red-400 hover:bg-red-500 text-white text-sm font-semibold rounded-full shadow-sm transition-colors mb-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Ya, Hapus
                </button>
            </form>

            <button type="button" @click="show = false"
                class="text-sm text-gray-400 dark:text-gray-500 hover:text-gray-600 transition-colors">
                Batal
            </button>
        </div>
    </div>

</x-app-layout>