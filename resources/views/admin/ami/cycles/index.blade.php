<x-app-layout>
    <div class="mx-auto w-full max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8"
        x-data="{
            showForm: {{ $errors->any() ? 'true' : 'false' }},
            deleteModal: false,
            deleteAction: '',
            deleteName: '',
            openDelete(action, name) { this.deleteAction = action; this.deleteName = name; this.deleteModal = true },
            confirmDelete() { this.$refs.deleteForm.submit() }
        }">
        @php
            $activeCount = $cycles->where('status', 'active')->count();
            $draftCount = $cycles->where('status', 'draft')->count();
            $closedCount = $cycles->where('status', 'closed')->count();
            $submissionCount = $cycles->sum('submissions_count');
        @endphp

        <nav class="mb-5 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Dashboard</a>
            <span>/</span><span class="font-medium text-slate-800 dark:text-slate-200">Audit Mutu Internal</span>
        </nav>

        <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-blue-600 dark:text-blue-400">Sistem Penjaminan Mutu</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 dark:text-white sm:text-3xl">Siklus Audit Mutu Internal</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-500 dark:text-slate-400">Kelola periode audit, submission program studi, dan penugasan auditor dalam satu tempat.</p>
            </div>
            <button type="button" @click="showForm = !showForm" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
                <svg class="h-5 w-5 transition" :class="showForm && 'rotate-45'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Siklus Baru
            </button>
        </div>

        <x-alert-success :message="session('success')" />

        @if($errors->any())
            <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 dark:border-rose-800 dark:bg-rose-500/10 dark:text-rose-400">
                <p class="font-bold">Siklus belum dapat disimpan.</p>
                <ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div x-show="showForm" x-collapse class="mb-6">
            <form method="POST" action="{{ route('admin.ami.cycles.store') }}" class="overflow-hidden rounded-2xl border border-blue-200 bg-white shadow-lg shadow-blue-900/5 dark:border-blue-900 dark:bg-slate-800">
                @csrf
                <div class="flex items-center gap-3 border-b border-slate-200 bg-blue-50/60 px-5 py-4 dark:border-slate-700 dark:bg-blue-500/5">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span>
                    <div><h2 class="font-bold text-slate-900 dark:text-white">Buat Siklus AMI</h2><p class="text-xs text-slate-500 dark:text-slate-400">Tentukan nama dan periode pelaksanaan audit.</p></div>
                </div>
                <div class="grid gap-4 p-5 md:grid-cols-2">
                    <label class="md:col-span-2"><span class="mb-1.5 block text-sm font-semibold">Judul Siklus <span class="text-rose-500">*</span></span><input name="title" value="{{ old('title') }}" required maxlength="255" placeholder="Contoh: AMI Tahun Akademik 2026/2027" class="h-11 w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white"></label>
                    <label><span class="mb-1.5 block text-sm font-semibold">Tanggal Mulai</span><input type="date" name="period_start" value="{{ old('period_start') }}" class="h-11 w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white"></label>
                    <label><span class="mb-1.5 block text-sm font-semibold">Tanggal Selesai</span><input type="date" name="period_end" value="{{ old('period_end') }}" class="h-11 w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white"></label>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-200 px-5 py-4 dark:border-slate-700"><button type="button" @click="showForm = false" class="h-10 rounded-xl border border-slate-300 px-4 text-sm font-semibold dark:border-slate-600">Batal</button><button type="submit" class="h-10 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white hover:bg-blue-700">Simpan Siklus</button></div>
            </form>
        </div>

        <div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-5">
            @foreach([
                ['label' => 'Total Siklus', 'value' => $cycles->count(), 'class' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400'],
                ['label' => 'Siklus Aktif', 'value' => $activeCount, 'class' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400'],
                ['label' => 'Siklus Selesai', 'value' => $closedCount, 'class' => 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400'],
                ['label' => 'Total Submission', 'value' => $submissionCount, 'class' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400'],
            ] as $card)
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-5"><div class="flex justify-between gap-3"><div><p class="text-xs font-semibold text-slate-500 dark:text-slate-400 sm:text-sm">{{ $card['label'] }}</p><p class="mt-2 text-2xl font-bold text-slate-950 dark:text-white sm:text-3xl">{{ number_format($card['value']) }}</p></div><span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $card['class'] }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span></div></div>
            @endforeach
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-700"><div><h2 class="font-bold text-slate-900 dark:text-white">Riwayat Siklus</h2><p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $draftCount }} siklus masih berstatus draf</p></div><span class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600 dark:bg-slate-700 dark:text-slate-300">{{ $cycles->count() }} data</span></div>
            <div class="grid gap-4 p-4 md:grid-cols-2 xl:grid-cols-3 lg:p-5">
                @forelse($cycles as $cycle)
                    @php
                        [$statusLabel, $statusClass, $dotClass] = match($cycle->status) {
                            'active' => ['Aktif', 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400', 'bg-emerald-500'],
                            'closed' => ['Selesai', 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300', 'bg-slate-400'],
                            default => ['Draf', 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400', 'bg-amber-500'],
                        };
                    @endphp
                    <article class="group flex min-h-60 flex-col rounded-2xl border border-slate-200 p-5 transition hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-lg dark:border-slate-700 dark:hover:border-blue-600">
                        <div class="flex items-start justify-between gap-3"><span class="inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClass }}"><span class="h-2 w-2 rounded-full {{ $dotClass }}"></span>{{ $statusLabel }}</span><button type="button" @click="openDelete('{{ route('admin.ami.cycles.destroy', $cycle) }}', '{{ addslashes($cycle->title) }}')" title="Hapus siklus" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-1 12a2 2 0 01-2 2H8a2 2 0 01-2-2L5 7m-1 0h16m-7 0V4h-2v3m-1 4v6m4-6v6"/></svg></button></div>
                        <h3 class="mt-4 line-clamp-2 text-lg font-bold text-slate-900 dark:text-white">{{ $cycle->title }}</h3>
                        <div class="mt-3 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg><span>{{ $cycle->period_start?->translatedFormat('d M Y') ?? 'Belum ditentukan' }} — {{ $cycle->period_end?->translatedFormat('d M Y') ?? '...' }}</span></div>
                        <div class="mt-auto flex items-end justify-between gap-3 pt-6"><div><p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $cycle->submissions_count }}</p><p class="text-xs text-slate-500 dark:text-slate-400">Submission</p></div><a href="{{ route('admin.ami.cycles.show', $cycle) }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-50 px-4 text-sm font-bold text-blue-700 transition group-hover:bg-blue-600 group-hover:text-white dark:bg-blue-500/10 dark:text-blue-400 dark:group-hover:bg-blue-600 dark:group-hover:text-white">Kelola <span>→</span></a></div>
                    </article>
                @empty
                    <div class="col-span-full py-16 text-center"><span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-700"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span><p class="mt-4 font-bold">Belum ada siklus AMI</p><p class="mt-1 text-sm text-slate-500">Buat siklus pertama untuk memulai audit.</p></div>
                @endforelse
            </div>
        </section>

        <form x-ref="deleteForm" method="POST" :action="deleteAction" class="hidden">@csrf @method('DELETE')</form>
        <div x-show="deleteModal" x-cloak @keydown.escape.window="deleteModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm">
            <div x-show="deleteModal" x-transition @click.outside="deleteModal = false" class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-2xl dark:border-slate-700 dark:bg-slate-800"><span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-rose-100 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.1 19h13.8a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.37 16a2 2 0 001.73 3z"/></svg></span><h3 class="mt-4 text-xl font-bold">Hapus Siklus AMI?</h3><p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400"><span class="font-semibold" x-text="deleteName"></span> beserta seluruh submission dan data terkait akan dihapus permanen.</p><div class="mt-6 flex gap-3"><button @click="deleteModal = false" type="button" class="h-11 flex-1 rounded-xl bg-slate-100 text-sm font-bold dark:bg-slate-700">Batal</button><button @click="confirmDelete" type="button" class="h-11 flex-1 rounded-xl bg-rose-600 text-sm font-bold text-white hover:bg-rose-700">Ya, Hapus</button></div></div>
        </div>
    </div>
</x-app-layout>
