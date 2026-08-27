<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-700">
        <div class="flex items-center gap-3">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 01.8 1.6L14 13.667V19a1 1 0 01-.553.894l-4 2A1 1 0 018 21v-7.333L3.2 4.6A1 1 0 013 4z"/></svg>
            </span>
            <div><h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Filter Dashboard</h3><p class="text-xs text-slate-400">Sesuaikan ringkasan berdasarkan periode dan unit.</p></div>
        </div>
        @if(filled($filters['year_id'] ?? null) || filled($filters['unit'] ?? null))
        <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-rose-600 hover:text-rose-700 dark:text-rose-400">Reset filter</a>
        @endif
    </div>
    <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2 xl:grid-cols-12">
        <label class="xl:col-span-5"><span class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tahun Akreditasi</span>
            <select name="year_id" class="w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">
                <option value="">Semua tahun</option>
                @foreach($years as $year)<option value="{{ $year->id }}" @selected((string) ($filters['year_id'] ?? '') === (string) $year->id)>{{ $year->year }}</option>@endforeach
            </select>
        </label>
        <label class="xl:col-span-5"><span class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Unit / Program Studi</span>
            <select name="unit" class="w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">
                <option value="">Semua unit</option>
                @foreach($units as $unit)<option value="{{ $unit }}" @selected(($filters['unit'] ?? '') === $unit)>{{ $unit }}</option>@endforeach
            </select>
        </label>
        <div class="flex items-end xl:col-span-2"><button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-500/20">Terapkan <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button></div>
    </form>
</section>
