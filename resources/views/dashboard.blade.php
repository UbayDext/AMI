<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Dashboard</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Selamat datang — ringkasan sistem AMI</p>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            @include('dashboard.partials.stat-cards')
            @include('dashboard.partials.charts')
            @include('dashboard.partials.recent-assessments')
        </div>
    </div>

    @push('scripts')
    @include('dashboard.partials.scripts')
    @endpush
</x-app-layout>