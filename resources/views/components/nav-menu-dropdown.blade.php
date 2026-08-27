@props(['label', 'active' => false])

<div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @click.outside="open = false" class="relative">
    <button @click="open = !open" type="button"
        class="{{ $active ? 'relative flex h-14 items-center gap-1.5 px-3 text-sm font-bold text-white after:absolute after:inset-x-2 after:bottom-0 after:h-0.5 after:rounded-full after:bg-white' : 'flex h-14 items-center gap-1.5 px-3 text-sm font-medium text-blue-100 transition hover:text-white' }}">
        {{ $label }}
        <svg class="h-3.5 w-3.5 transition" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" x-transition.origin.top.left class="absolute left-0 top-full z-50 w-64 pt-2" style="display:none">
        <div class="space-y-1 rounded-xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-900/15 dark:border-slate-700 dark:bg-slate-800">
            {{ $slot }}
        </div>
    </div>
</div>
