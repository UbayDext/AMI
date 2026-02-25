<div {{ $attributes->merge(['class' => 'bg-white dark:bg-[#151b2b] border border-gray-100 dark:border-gray-800 shadow-sm sm:rounded-[24px] overflow-hidden']) }}>
    @if(isset($header))
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
        {{ $header }}
    </div>
    @endif

    <div class="{{ $padding ?? 'p-6 sm:p-8' }}">
        {{ $slot }}
    </div>

    @if(isset($footer))
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-[#1f2636]">
        {{ $footer }}
    </div>
    @endif
</div>