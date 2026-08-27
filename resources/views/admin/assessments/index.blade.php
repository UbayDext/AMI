<x-app-layout>
    <div class="mx-auto w-full max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8"
        x-data="{ showDeleteModal: false, deleteTargetName: '', deleteFormAction: '' }">
        <nav class="mb-5 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Dashboard</a>
            <span>/</span><span class="font-medium text-slate-800 dark:text-slate-200">Assessment</span>
        </nav>

        <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-blue-600 dark:text-blue-400">Audit Mutu Internal</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 dark:text-white sm:text-3xl">Daftar Assessment</h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Pantau penugasan, progres, dan hasil assessment setiap program studi.</p>
            </div>
            <a href="{{ route('admin.assessments.create') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Assessment
            </a>
        </div>

        <x-alert-success :message="session('success')" />

        @php
            $cards = [
                ['label' => 'Total Assessment', 'value' => $stats['total'], 'class' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400'],
                ['label' => 'Masih Draf', 'value' => $stats['draft'], 'class' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400'],
                ['label' => 'Sudah Dikirim', 'value' => $stats['submitted'], 'class' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400'],
                ['label' => 'Sudah Ditinjau', 'value' => $stats['reviewed'], 'class' => 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400'],
            ];
        @endphp
        <div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-5">
            @foreach($cards as $card)
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div><p class="text-xs font-semibold text-slate-500 dark:text-slate-400 sm:text-sm">{{ $card['label'] }}</p><p class="mt-2 text-2xl font-bold text-slate-950 dark:text-white sm:text-3xl">{{ number_format($card['value']) }}</p></div>
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $card['class'] }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                    </div>
                </div>
            @endforeach
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <form method="GET" class="grid gap-3 border-b border-slate-200 p-4 dark:border-slate-700 sm:grid-cols-2 lg:grid-cols-[minmax(240px,1fr)_200px_200px_auto] lg:p-5">
                <label class="relative block"><span class="sr-only">Cari</span><svg class="absolute left-3.5 top-3 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg><input name="search" value="{{ $filters['search'] ?? '' }}" maxlength="100" placeholder="Cari prodi atau assessor..." class="h-11 w-full rounded-xl border-slate-300 bg-white pl-11 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-white"></label>
                <select name="status" class="h-11 rounded-xl border-slate-300 bg-white text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-white"><option value="">Semua status</option><option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Draf</option><option value="submitted" @selected(($filters['status'] ?? '') === 'submitted')>Sudah dikirim</option><option value="reviewed" @selected(($filters['status'] ?? '') === 'reviewed')>Sudah ditinjau</option></select>
                <select name="year" class="h-11 rounded-xl border-slate-300 bg-white text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-white"><option value="">Semua tahun</option>@foreach($years as $year)<option value="{{ $year->id }}" @selected((string)($filters['year'] ?? '') === (string)$year->id)>{{ $year->year }}</option>@endforeach</select>
                <div class="flex gap-2"><button class="h-11 flex-1 rounded-xl bg-slate-900 px-5 text-sm font-bold text-white dark:bg-blue-600">Terapkan</button>@if(array_filter($filters))<a href="{{ route('admin.assessments.index') }}" class="inline-flex h-11 items-center rounded-xl border border-slate-300 px-4 text-sm font-semibold dark:border-slate-600">Reset</a>@endif</div>
            </form>

            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full">
                    <thead class="bg-slate-50 dark:bg-slate-900/50"><tr class="text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"><th class="px-6 py-3.5">Program Studi</th><th class="px-6 py-3.5">Tahun</th><th class="px-6 py-3.5">Assessor</th><th class="px-6 py-3.5">Status</th><th class="px-6 py-3.5">Dibuat</th><th class="px-6 py-3.5 text-right">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($assessments as $assessment)
                            @php
                                $status = match($assessment->status) {
                                    'submitted' => ['Sudah dikirim', 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'],
                                    'reviewed' => ['Sudah ditinjau', 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400'],
                                    'draft' => ['Draf', 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400'],
                                    default => [ucfirst($assessment->status), 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300'],
                                };
                            @endphp
                            <tr class="transition hover:bg-blue-50/40 dark:hover:bg-slate-700/30">
                                <td class="px-6 py-4"><a href="{{ route('admin.assessments.show', $assessment) }}" class="font-bold text-slate-900 hover:text-blue-600 dark:text-white">{{ $assessment->unit_name }}</a><p class="mt-1 text-xs text-slate-400">#ASM-{{ str_pad($assessment->id, 4, '0', STR_PAD_LEFT) }}</p></td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $assessment->accreditationYear->year ?? '-' }}</td>
                                <td class="px-6 py-4"><div class="flex items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700 dark:bg-blue-500/15 dark:text-blue-300">{{ strtoupper(substr($assessment->assessor->name ?? '?', 0, 2)) }}</span><div class="min-w-0"><p class="max-w-52 truncate text-sm font-semibold">{{ $assessment->assessor->name ?? 'Belum ditentukan' }}</p><p class="max-w-52 truncate text-xs text-slate-400">{{ $assessment->assessor->email ?? '-' }}</p></div></div></td>
                                <td class="px-6 py-4"><span class="whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-bold {{ $status[1] }}">{{ $status[0] }}</span></td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500 dark:text-slate-400">{{ $assessment->created_at->translatedFormat('d M Y') }}<p class="mt-1 text-xs">{{ $assessment->created_at->format('H:i') }} WIB</p></td>
                                <td class="px-6 py-4 text-right"><div class="inline-flex gap-1"><a href="{{ route('admin.assessments.show', $assessment) }}" title="Detail" class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-blue-100 hover:text-blue-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.5 12C3.7 8 7.5 5 12 5s8.3 3 9.5 7c-1.2 4-5 7-9.5 7s-8.3-3-9.5-7z"/></svg></a><button @click.prevent="deleteTargetName = '{{ addslashes($assessment->unit_name) }}'; deleteFormAction = '{{ route('admin.assessments.destroy', $assessment) }}'; showDeleteModal = true" title="Hapus" class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-1 12a2 2 0 01-2 2H8a2 2 0 01-2-2L5 7m5 4v6m4-6v6M4 7h16m-7 0V4h-2v3"/></svg></button></div></td>
                            </tr>
                        @empty<tr><td colspan="6" class="px-6 py-16 text-center"><p class="font-bold">Assessment tidak ditemukan</p><p class="mt-1 text-sm text-slate-500">Ubah filter atau buat assessment baru.</p></td></tr>@endforelse
                    </tbody>
                </table>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-700 md:hidden">
                @forelse($assessments as $assessment)
                    <article class="p-4"><div class="flex justify-between gap-3"><div><a href="{{ route('admin.assessments.show', $assessment) }}" class="font-bold">{{ $assessment->unit_name }}</a><p class="mt-1 text-xs text-slate-400">{{ $assessment->accreditationYear->year ?? '-' }} · {{ $assessment->created_at->translatedFormat('d M Y') }}</p></div><span class="h-fit rounded-full bg-slate-100 px-2 py-1 text-xs dark:bg-slate-700">{{ ucfirst($assessment->status) }}</span></div><div class="mt-4 flex items-end justify-between gap-3"><div class="min-w-0"><p class="text-xs text-slate-400">Assessor</p><p class="truncate text-sm font-semibold">{{ $assessment->assessor->name ?? '-' }}</p></div><div class="flex gap-2"><a href="{{ route('admin.assessments.show', $assessment) }}" class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-bold text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">Detail</a><button type="button" @click.prevent="deleteTargetName = '{{ addslashes($assessment->unit_name) }}'; deleteFormAction = '{{ route('admin.assessments.destroy', $assessment) }}'; showDeleteModal = true" class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-bold text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">Hapus</button></div></div></article>
                @empty<div class="p-10 text-center text-sm text-slate-500">Assessment tidak ditemukan.</div>@endforelse
            </div>

            @if($assessments->hasPages())<div class="border-t border-slate-200 px-4 py-4 dark:border-slate-700 sm:px-6">{{ $assessments->links() }}</div>@endif
        </section>

        <x-delete-modal title="Hapus Assessment">Assessment <span class="font-semibold text-white" x-text="deleteTargetName"></span> beserta data terkait akan dihapus permanen.</x-delete-modal>
    </div>
</x-app-layout>
