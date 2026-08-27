<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">AMI — Submission Bukti</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Lampirkan link dokumen bukti per pertanyaan untuk diverifikasi auditor.</p>
            </div>
            @if($prodis->isNotEmpty())
            <form method="GET" action="{{ route('internal.ami.index') }}" class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">Prodi:</span>

                {{-- Pilih Prodi (kosong) --}}
                <label class="inline-flex items-center gap-1.5 cursor-pointer select-none group">
                    <span class="relative flex items-center justify-center w-4 h-4">
                        <input type="radio" name="prodi" value=""
                            {{ !$prodi ? 'checked' : '' }}
                            onchange="this.form.submit()"
                            class="peer sr-only">
                        <span class="w-4 h-4 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                            peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-colors"></span>
                        <svg class="absolute w-2.5 h-2.5 text-white pointer-events-none opacity-0 peer-checked:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <span class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors {{ !$prodi ? 'font-semibold text-indigo-600 dark:text-indigo-400' : '' }}">
                        Semua
                    </span>
                </label>

                <span class="text-gray-200 dark:text-gray-700 text-xs">|</span>

                @foreach($prodis as $p)
                <label class="inline-flex items-center gap-1.5 cursor-pointer select-none group">
                    <span class="relative flex items-center justify-center w-4 h-4">
                        <input type="radio" name="prodi" value="{{ $p->id }}"
                            {{ $prodi?->id === $p->id ? 'checked' : '' }}
                            onchange="this.form.submit()"
                            class="peer sr-only">
                        <span class="w-4 h-4 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                            peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-colors"></span>
                        <svg class="absolute w-2.5 h-2.5 text-white pointer-events-none opacity-0 peer-checked:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <span class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors {{ $prodi?->id === $p->id ? 'font-semibold text-indigo-600 dark:text-indigo-400' : '' }}">
                        {{ $p->name }}
                    </span>
                </label>
                @endforeach
            </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8" x-data="{ showAdd: false }">
        <x-alert-success :message="session('success')" />
        @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-xl text-sm">{{ session('error') }}</div>
        @endif

        @if(!$prodi)
        <x-card class="mb-6 text-center py-4 text-sm text-gray-500">
            Menampilkan submission seluruh prodi untuk standar yang ditugaskan kepada Anda.
        </x-card>
        @endif

        {{-- Form Tambah Submission --}}
        @if($prodi && $activeCycles->isNotEmpty())
        <x-card padding="p-5" class="mb-6">
            <button @click="showAdd = !showAdd"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                <svg class="w-4 h-4 transition-transform" :class="showAdd ? 'rotate-45' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Submission Standar
            </button>

            <form x-show="showAdd" x-transition method="POST"
                action="{{ route('internal.ami.store-submission') }}"
                class="mt-4 flex flex-wrap gap-4 items-end">
                @csrf
                <input type="hidden" name="prodi_id" value="{{ $prodi->id }}">

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Siklus AMI *</label>
                    <select name="cycle_id" required
                        class="border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm dark:bg-gray-800 focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                        @foreach($activeCycles as $c)
                        <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Standar *</label>
                    <select name="standard_id" required
                        class="min-w-[280px] border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm dark:bg-gray-800 focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                        <option value="">— Pilih Standar —</option>
                        @foreach($standards as $s)
                        <option value="{{ $s->id }}">{{ $s->code }}: {{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                    class="px-4 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Tambah
                </button>
            </form>
        </x-card>
        @endif
        @forelse($submissions as $cycleId => $cycleSubmissions)
        @php $cycle = $cycleSubmissions->first()->cycle; @endphp
        <div class="mb-8">
            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-3">
                {{ $cycle->title }}
                @if($cycle->period_start)
                <span class="font-normal normal-case">· {{ $cycle->period_start->format('d M Y') }} – {{ $cycle->period_end?->format('d M Y') }}</span>
                @endif
            </h3>
            <div class="space-y-3">
                @foreach($cycleSubmissions as $sub)
                @php
                    $statusBadge = match($sub->status) {
                        'submitted'    => 'bg-blue-100 text-blue-700',
                        'under_review' => 'bg-amber-100 text-amber-700',
                        'accepted'     => 'bg-emerald-100 text-emerald-700',
                        'revision'     => 'bg-red-100 text-red-700',
                        default        => 'bg-gray-100 text-gray-600',
                    };
                    $statusLabel = ['draft'=>'Draft','submitted'=>'Submitted','under_review'=>'Dalam Review','accepted'=>'Diterima','revision'=>'Perlu Revisi'][$sub->status] ?? $sub->status;
                @endphp
                <x-card padding="p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-medium text-sm text-gray-800 dark:text-gray-200">
                                    {{ $sub->standard?->code }}: {{ $sub->standard?->name }}
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $statusBadge }}">{{ $statusLabel }}</span>
                                <span class="text-xs text-gray-400">{{ $sub->evidences->count() }} link</span>
                            </div>
                        </div>
                        <a href="{{ route('internal.ami.show', $sub) }}"
                            class="shrink-0 text-xs px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            {{ $sub->owner_id === auth()->id() || auth()->user()->hasRole('admin') ? 'Kelola' : 'Lihat' }}
                        </a>
                    </div>
                </x-card>
                @endforeach
            </div>
        </div>
        @empty
        <x-card class="text-center py-10 text-sm text-gray-400">
            @if($prodi)
                Belum ada submission untuk prodi <strong>{{ $prodi->name }}</strong> pada siklus AMI aktif.
            @else
                Belum ada submission untuk standar yang ditugaskan kepada Anda.
            @endif
        </x-card>
        @endforelse
    </div>
</x-app-layout>
