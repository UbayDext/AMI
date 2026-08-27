@props([
    'task' => null,
    'prodis',
    'currentProdi' => null,
    'stage' => null,
])

@php
    $selectedCategory = old('category', $task?->category);
    $selectedProdiIds = collect(old('prodi_ids', $task
        ? ($task->prodis->isNotEmpty() ? $task->prodis->pluck('id') : collect([$task->prodi_id])->filter())
        : collect([$currentProdi?->id ?? $stage?->prodi_id])->filter()
    ))->map(fn ($id) => (int) $id);

    $categories = [
        'kebijakan' => ['label' => 'Kebijakan', 'hint' => 'SK, pedoman, SOP', 'icon' => 'clipboard'],
        'pelaksanaan' => ['label' => 'Pelaksanaan', 'hint' => 'Jadwal, absensi, notulen', 'icon' => 'document'],
        'evaluasi' => ['label' => 'Evaluasi', 'hint' => 'Monitoring, RTL, evaluasi', 'icon' => 'chart'],
        'pendukung_digital' => ['label' => 'Pendukung Digital', 'hint' => 'LMS, cloud, rekaman', 'icon' => 'link'],
    ];
@endphp

<div class="space-y-6">
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
            Judul Dokumen <span class="text-rose-500">*</span>
        </label>
        <input type="text" name="title" value="{{ old('title', $task?->title) }}" required
            placeholder="Contoh: SK Rektor"
            class="block w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 shadow-sm transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500" />
    </div>

    <fieldset>
        <legend class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
            Kategori Jenis Dokumen <span class="text-rose-500">*</span>
        </legend>
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
            @foreach($categories as $value => $category)
            <label class="group relative cursor-pointer">
                <input type="radio" name="category" value="{{ $value }}" required
                    @checked($selectedCategory === $value) class="peer sr-only">
                <span class="flex min-h-16 items-center gap-3 rounded-xl border border-slate-200 bg-white p-3 text-left transition hover:border-emerald-300 hover:bg-emerald-50/60 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:ring-2 peer-checked:ring-emerald-500/15 dark:border-slate-700 dark:bg-slate-900/70 dark:hover:border-emerald-700 dark:hover:bg-emerald-950/30 dark:peer-checked:border-emerald-500 dark:peer-checked:bg-emerald-950/40">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 transition group-hover:text-emerald-600 peer-checked:bg-emerald-100 dark:bg-slate-800 dark:text-slate-400 dark:peer-checked:bg-emerald-900/60">
                        @if($category['icon'] === 'clipboard')
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        @elseif($category['icon'] === 'chart')
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m4 6V7m4 10v-3M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        @elseif($category['icon'] === 'link')
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.102m-.758-4.898a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        @else
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        @endif
                    </span>
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold text-slate-700 peer-checked:text-emerald-700 dark:text-slate-200 dark:peer-checked:text-emerald-300">{{ $category['label'] }}</span>
                        <span class="block truncate text-xs text-slate-500 dark:text-slate-400">{{ $category['hint'] }}</span>
                    </span>
                    <svg class="ml-auto hidden h-5 w-5 shrink-0 text-emerald-600 peer-checked:block dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                </span>
            </label>
            @endforeach
        </div>
    </fieldset>

    <fieldset x-data="{ search: '' }">
        <div class="mb-2 flex items-end justify-between gap-3">
            <legend class="text-sm font-semibold text-slate-700 dark:text-slate-200">Prodi Tujuan</legend>
            <span class="text-xs text-slate-400">Kosong = semua prodi</span>
        </div>
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900/70">
            <div class="relative border-b border-slate-200 dark:border-slate-700">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="search" x-model="search" placeholder="Cari prodi..."
                    class="w-full border-0 bg-transparent py-2.5 pl-10 pr-4 text-sm text-slate-800 placeholder:text-slate-400 focus:ring-0 dark:text-slate-100">
            </div>
            <div class="max-h-40 space-y-1 overflow-y-auto p-2">
                @foreach($prodis as $optionProdi)
                <label x-show="{{ Illuminate\Support\Js::from(mb_strtolower($optionProdi->name)) }}.includes(search.toLowerCase())"
                    class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-slate-50 dark:hover:bg-slate-800">
                    <input type="checkbox" name="prodi_ids[]" value="{{ $optionProdi->id }}"
                        @checked($selectedProdiIds->contains($optionProdi->id))
                        class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-800 dark:checked:bg-emerald-600">
                    <span class="text-sm text-slate-700 dark:text-slate-200">{{ $optionProdi->name }}</span>
                    @if($optionProdi->code)
                    <span class="ml-auto rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-500 dark:bg-slate-800 dark:text-slate-400">{{ $optionProdi->code }}</span>
                    @endif
                </label>
                @endforeach
            </div>
        </div>
    </fieldset>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Deskripsi <span class="text-rose-500">*</span></label>
        <textarea name="description" required rows="3" placeholder="Jelaskan secara singkat..."
            class="block w-full resize-y rounded-xl border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 shadow-sm transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500">{{ old('description', $task?->description) }}</textarea>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Link Referensi <span class="text-rose-500">*</span></label>
        <div class="relative">
            <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.102m-.758-4.898a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            <input type="url" name="link" value="{{ old('link', $task?->link) }}" required placeholder="https://..."
                class="block w-full rounded-xl border-slate-300 bg-white py-3 pl-11 pr-4 text-sm text-slate-900 placeholder:text-slate-400 shadow-sm transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500" />
        </div>
    </div>
</div>
