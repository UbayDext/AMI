@props(['icon' => 'folder', 'title' => 'No Data Found', 'subtitle' => 'Get started by creating a new entry.'])

<div class="flex flex-col items-center justify-center py-10 text-center text-gray-500 dark:text-gray-400">
    <div class="flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full mb-4">
        @if($icon === 'folder')
        <svg class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
        </svg>
        @elseif($icon === 'document')
        <svg class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        @else
        {{ $slot }}
        @endif
    </div>

    <span class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">{{ $title }}</span>

    @if($subtitle)
    <p class="text-sm text-gray-500 mt-1">{{ $subtitle }}</p>
    @endif

    @if(isset($action))
    <div class="mt-4">
        {{ $action }}
    </div>
    @endif
</div>