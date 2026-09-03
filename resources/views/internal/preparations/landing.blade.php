<x-app-layout>
    <x-slot name="header">
        <div data-onboarding-preparations="intro" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
            <h2 class="text-xl font-semibold">Evidence Standar</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Pilih standar yang ditugaskan kepada Anda untuk mengunggah dan memantau evidence.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('restart-preparations-onboarding'))" class="rounded-xl border border-indigo-300 px-4 py-2 text-xs font-bold text-indigo-600 dark:border-indigo-700 dark:text-indigo-400">Lihat Panduan</button>
                @if($prodis->isNotEmpty())
                    <form method="GET" action="{{ route('internal.preparations.index') }}" class="flex items-center gap-2">
                        <label for="preparation-prodi" class="text-xs font-medium text-gray-500 dark:text-gray-400">Prodi:</label>
                        <select id="preparation-prodi" name="prodi" onchange="this.form.submit()" class="rounded-xl border-gray-200 py-2 pr-9 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800">
                            <option value="">— Pilih Prodi —</option>
                            @foreach($prodis as $item)
                                <option value="{{ $item->id }}" @selected($prodi?->id === $item->id)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @if($standards->isEmpty())
            <div data-onboarding-preparations="empty" class="rounded-xl border border-amber-300 bg-amber-50 p-6 text-amber-800 dark:border-amber-800 dark:bg-amber-950/30 dark:text-amber-300">
                <h3 class="font-semibold">Belum ada standar yang ditugaskan</h3>
                <p class="mt-1 text-sm">Ajukan standar melalui menu Pengguna → Ajukan Standar atau hubungi admin.</p>
                <a href="{{ route('role-requests.create') }}" class="mt-4 inline-flex rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white">Ajukan Standar</a>
            </div>
        @else
            @if(!$prodi)
                <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:border-amber-800/60 dark:bg-amber-900/20 dark:text-amber-400">
                    Pilih prodi untuk melihat persentase kesiapan evidence setiap standar.
                </div>
            @endif
            <div data-onboarding-preparations="standard-list" class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($standards as $standard)
                    <a href="{{ route('internal.preparations.show', [$standard, 'prodi' => $prodi?->id]) }}" class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-400 hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-start justify-between gap-3">
                            <span class="inline-flex rounded-xl bg-indigo-100 px-3 py-2 text-sm font-bold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">{{ $standard->code }}</span>
                            @if($prodi)
                                <span class="text-sm font-bold {{ $standard->readiness_percent === 100 ? 'text-emerald-600 dark:text-emerald-400' : 'text-indigo-600 dark:text-indigo-400' }}">{{ $standard->readiness_percent }}%</span>
                            @endif
                        </div>
                        <h3 class="mt-5 font-semibold text-gray-900 dark:text-white">{{ $standard->name }}</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $standard->stage_count }} tahap aktif</p>
                        @if($prodi)
                            <div class="mt-4 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span>Kesiapan evidence</span>
                                <span>{{ $standard->done_tasks }}/{{ $standard->total_tasks }} task terisi</span>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700" role="progressbar" aria-label="Kesiapan evidence {{ $standard->code }}" aria-valuenow="{{ $standard->readiness_percent }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="h-full rounded-full {{ $standard->readiness_percent === 100 ? 'bg-emerald-500' : 'bg-indigo-500' }} transition-all" style="width: {{ $standard->readiness_percent }}%"></div>
                            </div>
                        @endif
                        <div class="mt-5 border-t border-gray-100 pt-4 text-sm font-semibold text-indigo-600 dark:border-gray-700 dark:text-indigo-400">Buka checklist <span aria-hidden="true">→</span></div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
    @push('scripts')
        @include('internal.preparations.partials.onboarding')
    @endpush
</x-app-layout>
