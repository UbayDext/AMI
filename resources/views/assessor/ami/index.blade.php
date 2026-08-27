<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">AMI — Review Saya</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Daftar review yang ditugaskan kepada Anda.</p>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <x-alert-success :message="session('success')" />

        <div class="space-y-3">
            @forelse($reviews as $r)
            @php
                $badgeReview = match($r->status) {
                    'in_progress' => 'bg-amber-100 text-amber-700',
                    'completed'   => 'bg-emerald-100 text-emerald-700',
                    default       => 'bg-gray-100 text-gray-600',
                };
            @endphp
            <x-card padding="p-4">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-medium text-sm text-gray-800 dark:text-gray-200">
                                {{ $r->submission?->standard?->code }}: {{ $r->submission?->standard?->name }}
                            </span>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $badgeReview }}">
                                {{ ['pending'=>'Belum mulai','in_progress'=>'Sedang berjalan','completed'=>'Selesai'][$r->status] ?? $r->status }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Prodi: <strong>{{ $r->submission?->prodi?->name }}</strong>
                            · {{ $r->submission?->cycle?->title }}
                        </div>
                    </div>
                    <a href="{{ route('assessor.ami.show', $r) }}"
                        class="shrink-0 text-xs px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        {{ $r->status === 'completed' ? 'Lihat' : 'Mulai Review' }}
                    </a>
                </div>
            </x-card>
            @empty
            <x-card class="text-center py-10 text-sm text-gray-400">Tidak ada review yang ditugaskan.</x-card>
            @endforelse
        </div>
    </div>
</x-app-layout>
