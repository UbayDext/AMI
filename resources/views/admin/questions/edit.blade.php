<x-app-layout>
    <x-slot name="header"><span class="sr-only">Edit Soal</span></x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-gray-100 dark:bg-gray-800/80 flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-xl bg-white dark:bg-gray-800 rounded-3xl shadow-lg p-8 relative">

            {{-- Close --}}
            <a href="{{ route('admin.questions.index') }}"
                class="absolute top-5 right-5 w-8 h-8 flex items-center justify-center rounded-full text-gray-400 dark:text-gray-500 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-7">
                <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 dark:bg-indigo-900/60 rounded-2xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Edit Soal #{{ $question->id }}</h1>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Diperbarui {{ $question->updated_at->diffForHumans() }}</p>
                </div>
            </div>

            {{-- Flash --}}
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                class="mb-5 flex items-center gap-2.5 p-3 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-5 p-3 bg-red-50 border border-red-100 rounded-2xl text-xs text-red-600">
                <ul class="list-disc ml-3 space-y-0.5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('admin.questions.update', $question) }}" class="space-y-5"
                x-data="{
                    selectedStandard: '{{ old('standard_id', $question->standard_id) }}',
                    tasks: [],
                    loadingTasks: false,
                    fetchTasks(id) {
                        if (!id) { this.tasks = []; return; }
                        this.loadingTasks = true;
                        fetch('/admin/questions-preparation-tasks?standard_id=' + id)
                            .then(r => r.json())
                            .then(d => { this.tasks = d; this.loadingTasks = false; })
                            .catch(() => { this.tasks = []; this.loadingTasks = false; });
                    }
                }"
                x-init="if (selectedStandard) fetchTasks(selectedStandard); $watch('selectedStandard', val => fetchTasks(val))">
                @csrf
                @method('PUT')

                {{-- Pertanyaan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pertanyaan</label>
                    <textarea name="label" rows="3" required
                        class="w-full bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-3 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition-colors resize-none"
                        placeholder="Isi teks pertanyaan...">{{ old('label', $question->label) }}</textarea>
                    @error('label')<p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Kategori Soal --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Kategori Soal</label>
                    <div class="relative">
                        <select name="category_id"
                            class="w-full appearance-none bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl pl-4 pr-10 py-3 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition-colors">
                            <option value="">— Pilih Kategori —</option>
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}" @selected(old('category_id', $question->category_id) == $c->id)>
                                {{ ($c->code ? $c->code.' - ' : '').$c->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Standar Akreditasi + Referensi --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Standar Akreditasi</label>
                    <div class="relative">
                        <select name="standard_id" x-model="selectedStandard"
                            class="w-full appearance-none bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl pl-4 pr-10 py-3 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition-colors">
                            <option value="">— Pilih Standar —</option>
                            @foreach($standards as $s)
                            <option value="{{ $s->id }}" @selected(old('standard_id', $question->standard_id) == $s->id)>
                                {{ $s->code }} - {{ Str::limit($s->name, 30) }}
                            </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                    {{-- Referensi --}}
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Referensi <span class="font-normal text-gray-400">(Opsional)</span>
                        </label>
                        <div class="relative">
                            <div x-show="loadingTasks" class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-indigo-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </div>
                            <select x-show="tasks.length > 0" name="reference"
                                class="w-full appearance-none bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-3 text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition-colors">
                                <option value="">— Pilih Referensi —</option>
                                <template x-for="task in tasks" :key="task.id">
                                    <option :value="task.value"
                                        :selected="task.value === '{{ old("reference", $question->reference ?? "") }}'"
                                        x-text="task.label"></option>
                                </template>
                            </select>
                            <div x-show="!loadingTasks && tasks.length === 0"
                                class="w-full bg-gray-50 dark:bg-gray-900/50 border border-dashed border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-3 text-sm text-gray-400 dark:text-gray-500">
                                <span x-show="!selectedStandard">Pilih standar terlebih dahulu untuk melihat referensi.</span>
                                <span x-show="selectedStandard && !loadingTasks">Tidak ada dokumen persiapan untuk standar ini.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tipe Jawaban --}}
                <div x-data="{ selected: '{{ old('type', $question->type) }}' }">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Tipe Jawaban</label>
                    @php
                    $typeCards = [
                        'text'     => ['label'=>'Teks Singkat',    'desc'=>'Jawaban satu baris teks pendek',   'icon'=>'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5', 'color'=>'indigo'],
                        'textarea' => ['label'=>'Essay / Paragraf','desc'=>'Jawaban panjang berupa paragraf',   'icon'=>'M4 6h16M4 12h16M4 18h8',                               'color'=>'violet'],
                        'radio'    => ['label'=>'Pilihan Ganda',   'desc'=>'Pilih satu jawaban dari opsi',     'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',        'color'=>'emerald'],
                        'file'     => ['label'=>'Upload File',     'desc'=>'Unggah dokumen / gambar / PDF',    'icon'=>'M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13','color'=>'orange'],
                    ];
                    $colorMap = [
                        'indigo'  => ['bg'=>'bg-indigo-100 dark:bg-indigo-900/40',  'text'=>'text-indigo-600 dark:text-indigo-400',  'active_bg'=>'bg-indigo-50 dark:bg-indigo-900/20',  'border'=>'border-indigo-300 dark:border-indigo-600'],
                        'violet'  => ['bg'=>'bg-violet-100 dark:bg-violet-900/40',  'text'=>'text-violet-600 dark:text-violet-400',  'active_bg'=>'bg-violet-50 dark:bg-violet-900/20',  'border'=>'border-violet-300 dark:border-violet-600'],
                        'emerald' => ['bg'=>'bg-emerald-100 dark:bg-emerald-900/40','text'=>'text-emerald-600 dark:text-emerald-400','active_bg'=>'bg-emerald-50 dark:bg-emerald-900/20','border'=>'border-emerald-300 dark:border-emerald-600'],
                        'orange'  => ['bg'=>'bg-orange-100 dark:bg-orange-900/40',  'text'=>'text-orange-600 dark:text-orange-400',  'active_bg'=>'bg-orange-50 dark:bg-orange-900/20',  'border'=>'border-orange-300 dark:border-orange-600'],
                    ];
                    @endphp
                    <div class="grid grid-cols-2 gap-2.5">
                        @foreach($typeCards as $val => $card)
                        @php
                            $c = $colorMap[$card['color']];
                            $activeClass = $c['border'] . ' ' . $c['active_bg'] . ' shadow-sm';
                            $checkClass  = $c['bg'] . ' scale-100 opacity-100';
                        @endphp
                        <label class="cursor-pointer group">
                            <input type="radio" name="type" value="{{ $val }}" class="sr-only"
                                {{ old('type', $question->type) === $val ? 'checked' : '' }}
                                x-model="selected">
                            <div class="relative flex items-start gap-3 p-3.5 rounded-2xl border-2 transition-all duration-200"
                                :class="selected === '{{ $val }}' ? '{{ $activeClass }}' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 hover:border-gray-300'">
                                <div class="flex-shrink-0 w-9 h-9 rounded-xl flex items-center justify-center {{ $c['bg'] }}">
                                    <svg class="w-5 h-5 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 leading-tight">{{ $card['label'] }}</p>
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5 leading-snug">{{ $card['desc'] }}</p>
                                </div>
                                <div class="absolute top-2.5 right-2.5 w-5 h-5 rounded-full flex items-center justify-center transition-all"
                                    :class="selected === '{{ $val }}' ? '{{ $checkClass }}' : 'scale-0 opacity-0'">
                                    <svg class="w-3 h-3 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('type')<p class="mt-1.5 text-xs text-red-500 font-medium">Wajib dipilih</p>@enderror
                </div>

                {{-- Status Aktif --}}
                <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-2xl px-5 py-4 flex items-center justify-between"
                    x-data="{ on: {{ old('is_active', $question->is_active) ? 'true' : 'false' }} }">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Status Aktif</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Soal ini akan muncul di audit.</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only" x-model="on">
                        <div class="w-11 h-6 rounded-full transition-colors duration-200" :class="on ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600'">
                            <div class="w-5 h-5 bg-white rounded-full shadow mt-0.5 transition-transform duration-200"
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

                <input type="hidden" name="sort_order" value="{{ old('sort_order', $question->sort_order) }}">

                {{-- Meta --}}
                <p class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1.5 pt-1 border-t border-gray-100 dark:border-gray-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Dibuat {{ $question->created_at->format('d M Y') }} · Diperbarui {{ $question->updated_at->diffForHumans() }}
                </p>

                {{-- Action buttons --}}
                <div class="flex flex-col gap-2.5 pt-1">
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-2xl shadow-sm hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Simpan Perubahan
                    </button>
                    <button type="button" @click="$dispatch('open-delete-modal')"
                        class="w-full flex items-center justify-center gap-2 px-6 py-2.5 text-red-500 text-sm font-medium rounded-2xl hover:bg-red-50 dark:hover:bg-red-900/20 border border-transparent hover:border-red-100 dark:hover:border-red-800 transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Soal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-data="{ show: false }" @open-delete-modal.window="show = true" x-show="show"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
        <div class="absolute inset-0 bg-gray-900/40 dark:bg-gray-900/60 backdrop-blur-sm" @click="show = false"></div>
        <div class="relative z-10 bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-8 max-w-xs w-full mx-4 text-center">
            <div class="flex justify-center mb-5">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/40 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">Hapus Soal ini?</h2>
            <p class="text-sm text-gray-400 dark:text-gray-500 mb-7">Tindakan ini tidak bisa dibatalkan dan data akan hilang selamanya.</p>
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
            <button type="button" @click="show = false" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">Batal</button>
        </div>
    </div>

</x-app-layout>
