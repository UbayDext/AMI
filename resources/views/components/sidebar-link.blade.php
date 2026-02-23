@props(['active'])

@php
$classes = ($active ?? false)
? 'flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 group relative transition-colors'
: 'flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/60 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 group relative transition-colors duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($active ?? false)
    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-indigo-600 rounded-r-full"></span>
    @endif
    {{ $slot }}
</a>