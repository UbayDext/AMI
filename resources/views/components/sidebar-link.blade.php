@props(['active'])

@php
$classes = ($active ?? false)
? 'flex items-center px-2 py-2 text-sm font-medium rounded-md bg-gray-100 text-gray-900 group'
: 'flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 group';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>