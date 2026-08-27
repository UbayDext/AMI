<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Bank Soal</h1>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">Kelola database pertanyaan untuk akreditasi dengan mudah.</p>
            </div>
            <div class="flex items-center gap-2">
                {{-- <a href="{{ route('admin.questions.import-checklist.form') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 text-sm font-semibold rounded-xl shadow-sm border border-indigo-200 dark:border-indigo-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all duration-150">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Import Checklist AMI
                </a> --}}
                <a href="{{ route('admin.questions.create') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Soal Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ showDeleteModal: false, deleteTargetName: '', deleteFormAction: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm">
                <svg class="h-5 w-5 text-emerald-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
            @endif

            <!-- Filter Bar -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4">
                <form method="GET" action="{{ route('admin.questions.index') }}"
                    class="flex flex-wrap items-center gap-3">

                    {{-- Category filter --}}
                    <div class="relative">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </div>
                        <select name="category_id"
                            class="pl-9 pr-8 py-2 text-sm border border-gray-200 dark:border-gray-800 rounded-xl bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 focus:outline-none appearance-none cursor-pointer">
                            <option value="" class="dark:bg-gray-900">Semua Prodi</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" class="dark:bg-gray-900" @selected(request('category_id')==$cat->id)>
                                {{ $cat->code ? $cat->code.' - ' : '' }}{{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    {{-- Standard filter --}}
                    <div class="relative">
                        <select name="standard_id"
                            class="pl-4 pr-8 py-2 text-sm border border-gray-200 dark:border-gray-800 rounded-xl bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 focus:outline-none appearance-none cursor-pointer">
                            <option value="" class="dark:bg-gray-900">Semua Standar</option>
                            @foreach($standards as $std)
                            <option value="{{ $std->id }}" class="dark:bg-gray-900" @selected(request('standard_id')==$std->id)>
                                {{ $std->code }} - {{ $std->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    {{-- Search --}}
                    <div class="flex-1 min-w-[200px] relative">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari soal..."
                            class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 dark:border-gray-800 rounded-xl bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 focus:outline-none" />
                    </div>

                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors">
                        Terapkan
                    </button>
                    @if(request('category_id') || request('standard_id') || request('search'))
                    <a href="{{ route('admin.questions.index') }}"
                        class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800/80 rounded-xl hover:bg-gray-200 transition-colors">
                        Reset
                    </a>
                    @endif

                    <span class="ml-auto text-xs text-gray-400 dark:text-gray-500">
                        <strong class="text-gray-600 dark:text-gray-400">{{ $questions->total() }}</strong> soal
                        @if(request('category_id') || request('standard_id')) (difilter) @endif
                    </span>
                </form>
            </div>

            <!-- Category Groups -->
            <div class="space-y-4">
                @forelse($nestedGroups as $catId => $standardGroups)
                @php
                $firstQ = $standardGroups->first()->first();
                $cat = $firstQ?->category;
                $categoryName = $cat ? (($cat->code ? $cat->code.' - ' : '').$cat->name) : 'Uncategorized';
                $totalInCategory = $standardGroups->flatten()->count();
                $stdCount = $standardGroups->count();
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-all"
                    x-data="{ openCat: {{ request('category_id') == $catId ? 'true' : 'false' }} }">

                    <!-- Category Header -->
                    <div @click="openCat = !openCat"
                        class="w-full flex items-center justify-between px-6 py-4 transition-colors text-left cursor-pointer transition-all rounded-t-2xl"
                        :class="openCat ? 'bg-indigo-50 dark:bg-indigo-900/40' : 'rounded-b-2xl hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                        <div class="flex items-center gap-4">
                            <!-- Category icon / avatar -->
                            <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center"
                                :class="openCat ? 'bg-indigo-100 dark:bg-indigo-900/60' : 'bg-gray-100 dark:bg-gray-800/80'">
                                <svg class="w-5 h-5 transition-colors" :class="openCat ? 'text-indigo-600' : 'text-gray-400 dark:text-gray-500'"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <div class="font-semibold text-gray-900 dark:text-gray-100 text-sm">{{ $categoryName }}</div>
                                <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $stdCount }} Standar • {{ $totalInCategory }} Pertanyaan</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- 3-dot menu -->
                            <div class="relative" x-data="{ menuOpen: false }" @click.stop>
                                <button type="button" @click="menuOpen = !menuOpen" @click.outside="menuOpen = false"
                                    class="w-7 h-7 flex items-center justify-center text-gray-400 dark:text-gray-500 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </button>

                                <div x-show="menuOpen" x-transition.opacity.duration.200ms
                                    class="absolute right-0 mt-2 w-36 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 py-1 z-10"
                                    style="display: none;">
                                    @if($cat)
                                    <a href="{{ route('admin.question-categories.edit', $catId) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50">Edit Kategori</a>
                                    <button type="button"
                                        @click="deleteTargetName = 'Kategori: {{ addslashes($categoryName) }}'; deleteFormAction = '{{ route('admin.question-categories.destroy', $catId) }}'; showDeleteModal = true; menuOpen = false;"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">Hapus Kategori</button>
                                    @else
                                    <div class="px-4 py-2 text-xs text-gray-400 dark:text-gray-500 italic">Uncategorized</div>
                                    @endif
                                </div>
                            </div>
                            <!-- Chevron -->
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-transform duration-200"
                                :class="{ 'rotate-180': openCat }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    <!-- Category Content (Standards + Questions) -->
                    <div x-show="openCat" x-collapse class="border-t border-gray-100 dark:border-gray-700 rounded-b-2xl overflow-hidden">
                        @foreach($standardGroups as $stdId => $items)
                        @php
                        $std = $items->first()?->standard;
                        $standardLabel = $std ? $std->code : 'No Standard';
                        $standardName = $std?->name ?? '';
                        @endphp

                        <div class="border-b border-gray-50 last:border-b-0"
                            x-data="{ openStd: {{ request('standard_id') == $stdId ? 'true' : 'false' }} }">

                            <!-- Standard sub-header -->
                            <button type="button" @click="openStd = !openStd"
                                class="w-full flex items-center justify-between px-6 py-3 pl-[4.5rem] transition-colors text-left"
                                :class="openStd ? 'bg-indigo-50/50 dark:bg-indigo-900/20' : 'hover:bg-gray-50/70 dark:hover:bg-gray-700/30'">
                                <div class="flex items-center gap-3">
                                    <span class="w-2 h-2 rounded-full flex-shrink-0"
                                        :class="openStd ? 'bg-indigo-500' : 'bg-gray-300'"></span>
                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $standardLabel }}</span>
                                    @if($standardName)
                                    <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide font-medium">{{ Str::limit($standardName, 40) }}</span>
                                    @endif
                                </div>
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200 mr-1"
                                    :class="{ 'rotate-180': openStd }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Questions list -->
                            <div x-show="openStd" x-collapse>
                                <div class="pl-[4.5rem] pr-6 pb-3 space-y-2">
                                    @foreach($items as $index => $q)
                                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-3.5 hover:border-indigo-200 dark:hover:border-indigo-500/50 hover:shadow-sm transition-all group">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                                <span class="flex-shrink-0 text-xs font-bold text-gray-400 dark:text-gray-500 w-6 text-right">{{ $index + 1 }}.</span>
                                                <a href="{{ route('admin.questions.import-checklist.form') }}"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-semibold rounded-lg border border-indigo-100 dark:border-indigo-700 hover:bg-indigo-100 dark:hover:bg-indigo-800/40 transition-colors flex-shrink-0">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                    </svg>
                                                    Import
                                                </a>
                                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed truncate" title="{{ $q->label }}">{{ $q->label }}</p>
                                            </div>
                                            <!-- Actions -->
                                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <a href="{{ route('admin.questions.edit', $q) }}"
                                                    class="p-1.5 text-gray-400 dark:text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                                    title="Edit">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <button type="button"
                                                    @click.prevent="deleteTargetName = 'Soal: {{ addslashes(Str::limit($q->label, 50)) }}'; deleteFormAction = '{{ route('admin.questions.destroy', $q) }}'; showDeleteModal = true;"
                                                    class="p-1.5 text-gray-400 dark:text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Hapus">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm px-6 py-16 text-center">
                    <div class="mx-auto w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-800/80 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada soal ditemukan.</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Coba ubah filter atau buat soal baru.</p>
                    <a href="{{ route('admin.questions.create') }}"
                        class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Soal Pertama
                    </a>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($questions->hasPages())
            <div class="mt-4">
                {{ $questions->links() }}
            </div>
            @endif

        </div>
        {{-- Delete Confirmation Modal --}}
        <x-delete-modal title="Konfirmasi Hapus?">
            Anda yakin ingin menghapus <span class="text-white font-medium" x-text="deleteTargetName"></span> secara permanen?
        </x-delete-modal>
    </div>

</x-app-layout>
