<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="mb-1 flex items-center gap-2 text-xs font-medium text-slate-400 dark:text-slate-500">
                    <span>Sistem Penjaminan Mutu Internal</span><span>/</span><span class="text-emerald-600 dark:text-emerald-400">Dashboard</span>
                </div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Dashboard AMI</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Ringkasan aktivitas audit dan tindak lanjut mutu institusi.</p>
            </div>
            <div class="inline-flex w-fit items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-500 shadow-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">
                <svg class="h-4 w-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 11h14M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ now()->translatedFormat('l, d F Y') }}
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50/70 py-7 dark:bg-slate-950/30">
        <div class="mx-auto w-full max-w-[1600px] space-y-7 px-4 sm:px-6 lg:px-8">
            @if(auth()->user()->hasRole('admin'))
            @include('dashboard.partials.blocked-users')
            @endif
            @include('dashboard.partials.filters')
            @include('dashboard.partials.stat-cards')
            @include('dashboard.partials.charts')
            @include('dashboard.partials.recent-assessments')
        </div>
    </div>

    @push('scripts')
    @include('dashboard.partials.scripts')
    @endpush
</x-app-layout>
