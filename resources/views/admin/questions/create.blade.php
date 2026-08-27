<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight sr-only">Tambah Soal Baru</h2>
    </x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-gray-100 dark:bg-gray-800/80 flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-xl bg-white dark:bg-gray-800 rounded-3xl shadow-lg p-8 relative">

            <a href="{{ route('admin.questions.index') }}"
                class="absolute top-5 right-5 w-8 h-8 flex items-center justify-center rounded-full text-gray-400 dark:text-gray-500 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>

            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Tambah Soal Baru</h1>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Atur pengaturan soal, lalu pilih pertanyaan dari checklist AMI.</p>
            </div>

            {{-- Step indicator --}}
            <div class="flex items-center gap-3 mb-7">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center">1</span>
                    <span class="text-sm font-semibold text-indigo-700 dark:text-indigo-400">Pengaturan Soal</span>
                </div>
                <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700"></div>
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-400 text-xs font-bold flex items-center justify-center">2</span>
                    <span class="text-sm text-gray-400 dark:text-gray-500">Pilih Pertanyaan</span>
                </div>
            </div>

            {{-- GET form: all named fields become query params on submit --}}
            <form method="GET" action="{{ route('admin.questions.import-checklist') }}" class="space-y-5"
                x-data="{ selectedStandard: '', tasks: [], loadingTasks: false,
                    fetchTasks(id) {
                        if (!id) { this.tasks = []; return; }
                        this.loadingTasks = true;
                        fetch('/admin/questions-preparation-tasks?standard_id=' + id)
                            .then(r => r.json())
                            .then(d => { this.tasks = d; this.loadingTasks = false; })
                            .catch(() => { this.tasks = []; this.loadingTasks = false; });
                    }
                }"
                x-init="$watch('selectedStandard', val => fetchTasks(val))">

                {{-- Kategori Soal --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Kategori Soal</label>
                    <div class="relative">
                        <select name="category_id"
                            class="w-full appearance-none bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl pl-4 pr-10 py-3 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition-colors">
                            <option value="">Pilih Kategori (misal: PBA)</option>
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->code ? $c->code.' - ' : '' }}{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Standar Akreditasi --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Standar Akreditasi</label>
                    <div class="relative">
                        <select name="standard_id" x-model="selectedStandard"
                            class="w-full appearance-none bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl pl-4 pr-10 py-3 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition-colors">
                            <option value="">Pilih Standar</option>
                            @foreach($standards as $s)
                            <option value="{{ $s->id }}">{{ $s->code }} - {{ Str::limit($s->name, 30) }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                    {{-- Referensi --}}
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Referensi</label>
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
                                    <option :value="task.value" x-text="task.label"></option>
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

                {{-- Tipe Soal --}}
                <div x-data="{ selected: 'radio' }">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Tipe Soal</label>
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
                            {{-- Native radio — form submission reads this directly, no Alpine binding needed --}}
                            <input type="radio" name="type" value="{{ $val }}" class="sr-only"
                                {{ $val === 'radio' ? 'checked' : '' }}
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
                </div>

                {{-- Action buttons --}}
                <div class="flex items-center justify-end gap-4 pt-3 border-t border-gray-100 dark:border-gray-700 mt-2">
                    <a href="{{ route('admin.questions.index') }}"
                        class="px-5 py-2.5 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-2xl shadow-sm hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150">
                        Lanjut ke Pilih Pertanyaan
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
