<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight sr-only">Import Soal dari Checklist AMI</h2>
    </x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-gray-100 dark:bg-gray-800/80 px-4 py-10"
        x-data="checklistImport()"
        x-init="init()"
        x-cloak>

        <div class="w-full max-w-4xl mx-auto">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Import dari Checklist AMI</h1>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">Pilih pertanyaan dari bank soal Audit Mutu Internal, lalu import sekaligus.</p>
                </div>
                <a href="{{ route('admin.questions.index') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>

            {{-- Flash message --}}
            @if(session('success'))
            <div class="mb-5 p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-2xl text-sm text-emerald-700 dark:text-emerald-300">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-5 p-4 bg-red-50 border border-red-100 rounded-2xl text-sm text-red-700">
                <ul class="list-disc ml-4 space-y-0.5">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.questions.import-checklist') }}">
                @csrf

                {{-- Step 1: Filter & Settings --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-5">
                    <h2 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                        <span class="flex-shrink-0 w-6 h-6 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center text-xs font-bold">1</span>
                        Filter Standar & Pengaturan
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        {{-- Filter by ST --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Filter Standar (ST)</label>
                            <div class="relative">
                                <select x-model="selectedCode" @change="loadQuestions()"
                                    class="w-full appearance-none bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-2xl pl-4 pr-9 py-2.5 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition-colors">
                                    <option value="">— Semua ST —</option>
                                    @foreach($checklistStandards as $cs)
                                    <option value="{{ $cs->standard_code }}">{{ $cs->standard_code }} — {{ Str::limit($cs->standard_name, 30) }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Target Standard --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Target Standar Akreditasi</label>
                            <div class="relative">
                                <select name="standard_id"
                                    class="w-full appearance-none bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-2xl pl-4 pr-9 py-2.5 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition-colors">
                                    <option value="">— Tanpa Standar —</option>
                                    @foreach($standards as $s)
                                    @php $stdSelected = request('standard_id') == $s->id || old('standard_id') == $s->id; @endphp
                                    <option value="{{ $s->id }}" {{ $stdSelected ? 'selected' : '' }}>
                                        {{ $s->code }} — {{ Str::limit($s->name, 28) }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Category --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Kategori Soal</label>
                            <div class="relative">
                                <select name="category_id"
                                    class="w-full appearance-none bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-2xl pl-4 pr-9 py-2.5 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition-colors">
                                    <option value="">— Tanpa Kategori —</option>
                                    @foreach($categories as $c)
                                    @php $catSelected = request('category_id') == $c->id || old('category_id') == $c->id; @endphp
                                    <option value="{{ $c->id }}" {{ $catSelected ? 'selected' : '' }}>
                                        {{ $c->code ? $c->code.' - ' : '' }}{{ $c->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden fields from Step 1 --}}
                        <input type="hidden" name="type" value="{{ request('type', 'radio') }}">
                        <input type="hidden" name="reference" value="{{ request('reference', '') }}">
                    </div>
                </div>

                {{-- Step 2: Checklist Questions --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                            <span class="flex-shrink-0 w-6 h-6 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center text-xs font-bold">2</span>
                            Pilih Pertanyaan
                            <span x-show="questions.length > 0"
                                class="ml-1 px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 text-xs font-semibold rounded-full"
                                x-text="selectedIds.length + ' / ' + questions.length + ' dipilih'">
                            </span>
                        </h2>

                        {{-- Select all / deselect all --}}
                        <div x-show="questions.length > 0" class="flex items-center gap-2">
                            <button type="button" @click="selectAll()"
                                class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 transition-colors">
                                Pilih Semua
                            </button>
                            <span class="text-gray-300 dark:text-gray-600">|</span>
                            <button type="button" @click="deselectAll()"
                                class="text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                                Batal Semua
                            </button>
                        </div>
                    </div>

                    {{-- Loading state --}}
                    <div x-show="loading" class="flex items-center justify-center py-16 text-gray-400 dark:text-gray-500">
                        <svg class="w-5 h-5 animate-spin mr-2 text-indigo-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span class="text-sm">Memuat pertanyaan...</span>
                    </div>

                    {{-- Empty state --}}
                    <div x-show="!loading && questions.length === 0"
                        class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-500">
                        <svg class="w-10 h-10 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm font-medium">Pilih standar untuk memuat pertanyaan</p>
                        <p class="text-xs mt-0.5">atau biarkan kosong untuk memuat semua ST</p>
                    </div>

                    {{-- Grouped questions --}}
                    <div x-show="!loading && questions.length > 0" class="space-y-6">
                        <template x-for="group in groupedQuestions" :key="group.code">
                            <div>
                                {{-- Group header --}}
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="flex items-center gap-2 flex-1">
                                        <span class="px-2.5 py-1 bg-indigo-600 text-white text-xs font-bold rounded-lg" x-text="group.code"></span>
                                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300" x-text="group.name"></span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500" x-text="'(' + group.bidang + ')'"></span>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <span class="text-xs text-gray-400 dark:text-gray-500" x-text="countSelected(group) + '/' + group.items.length + ' dipilih'"></span>
                                        <button type="button" @click="selectGroup(group)"
                                            class="text-xs font-medium text-indigo-500 hover:text-indigo-700 transition-colors">
                                            Semua
                                        </button>
                                        <button type="button" @click="deselectGroup(group)"
                                            class="text-xs font-medium text-gray-400 hover:text-gray-600 transition-colors">
                                            Hapus
                                        </button>
                                    </div>
                                </div>

                                {{-- Auditi badge --}}
                                <p class="text-xs text-gray-400 dark:text-gray-500 mb-2 ml-1" x-text="'Auditi: ' + group.auditi"></p>

                                {{-- Question list --}}
                                <div class="space-y-1.5">
                                    <template x-for="q in group.items" :key="q.id">
                                        <label class="flex items-start gap-3 p-3 rounded-2xl border border-transparent cursor-pointer transition-all duration-150"
                                            :class="selectedIds.includes(q.id)
                                                ? 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-700'
                                                : 'hover:bg-gray-50 dark:hover:bg-gray-700/30 border-transparent'">
                                            <input type="checkbox"
                                                name="checklist_ids[]"
                                                :value="q.id"
                                                :checked="selectedIds.includes(q.id)"
                                                @change="toggleId(q.id)"
                                                class="mt-0.5 flex-shrink-0 w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                                            <div class="min-w-0 flex-1">
                                                <span class="text-xs font-bold text-indigo-400 dark:text-indigo-500 mr-1.5" x-text="q.question_number + '.'"></span>
                                                <span class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed" x-text="q.question_text"></span>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Submit --}}
                <div x-show="selectedIds.length > 0"
                    class="sticky bottom-4 bg-white dark:bg-gray-800 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-bold text-indigo-600 dark:text-indigo-400" x-text="selectedIds.length"></span>
                        pertanyaan siap diimport dengan tipe <span class="font-semibold">Pilihan Ganda (Ya/Tidak)</span>
                    </p>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-2xl shadow-sm hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import <span x-text="selectedIds.length"></span> Soal
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function checklistImport() {
            return {
                selectedCode: '{{ $preselectedCode ?? "" }}',
                questions: [],
                selectedIds: [],
                loading: false,

                get groupedQuestions() {
                    const groups = {};
                    this.questions.forEach(q => {
                        if (!groups[q.standard_code]) {
                            groups[q.standard_code] = {
                                code: q.standard_code,
                                name: q.standard_name,
                                bidang: q.bidang,
                                auditi: q.auditi,
                                items: [],
                            };
                        }
                        groups[q.standard_code].items.push(q);
                    });
                    return Object.values(groups);
                },

                init() {
                    this.loadQuestions();
                },

                loadQuestions() {
                    this.loading = true;
                    this.questions = [];
                    this.selectedIds = [];
                    const url = '/admin/questions-checklist' + (this.selectedCode ? '?standard_code=' + this.selectedCode : '');
                    fetch(url)
                        .then(r => r.json())
                        .then(data => { this.questions = data; this.loading = false; })
                        .catch(() => { this.loading = false; });
                },

                toggleId(id) {
                    const idx = this.selectedIds.indexOf(id);
                    if (idx === -1) this.selectedIds.push(id);
                    else this.selectedIds.splice(idx, 1);
                },

                selectAll() {
                    this.selectedIds = this.questions.map(q => q.id);
                },

                deselectAll() {
                    this.selectedIds = [];
                },

                selectGroup(group) {
                    group.items.forEach(q => {
                        if (!this.selectedIds.includes(q.id)) this.selectedIds.push(q.id);
                    });
                },

                deselectGroup(group) {
                    const ids = group.items.map(q => q.id);
                    this.selectedIds = this.selectedIds.filter(id => !ids.includes(id));
                },

                countSelected(group) {
                    const ids = group.items.map(q => q.id);
                    return this.selectedIds.filter(id => ids.includes(id)).length;
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
