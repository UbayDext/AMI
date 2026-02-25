@props([
'title' => 'Delete Data',
'warningText' => 'Tindakan ini tidak dapat <span class="text-amber-500 font-medium tracking-wide">dibatalkan</span>.'
])

{{-- Delete Confirmation Modal --}}
<div x-show="showDeleteModal" x-cloak class="relative z-50">
    {{-- Backdrop --}}
    <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            {{-- Modal Panel --}}
            <div x-show="showDeleteModal" @click.away="showDeleteModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-[24px] bg-white dark:bg-[#151b2b] border border-gray-100 dark:border-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-[420px] w-full p-8" style="display: none;">

                {{-- Close X --}}
                <div class="absolute top-4 right-4 relative z-10">
                    <button type="button" @click="showDeleteModal = false" class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-[#2a3042] hover:text-gray-900 dark:hover:text-gray-100 transition-colors focus:outline-none">
                        <span class="sr-only">Close</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="text-center mt-2">
                    {{-- Glowing Trash Icon --}}
                    <div class="flex h-20 w-20 items-center justify-center mx-auto mb-5 relative">
                        <div class="absolute w-2 h-2 text-red-500 dark:text-red-600 left-0 top-1/2 -translate-y-1/2 -ml-2">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6 1.41-1.41z" />
                            </svg>
                        </div>
                        <div class="absolute w-2 h-2 text-red-500 dark:text-red-600 right-0 top-1/2 -translate-y-1/2 -mr-2">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z" />
                            </svg>
                        </div>
                        <div class="absolute inset-0 rounded-full bg-red-200 dark:bg-red-500/20 blur-md"></div>
                        <div class="relative h-14 w-14 rounded-full bg-red-600 dark:bg-[#ab3a3a] border-[3px] border-[#df4d4d] text-white flex items-center justify-center shadow-[0_0_20px_rgba(239,68,68,0.5)]">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                    </div>

                    <h3 class="text-2xl font-bold leading-6 text-gray-900 dark:text-gray-100 mb-2" id="modal-title">{{ $title }}</h3>

                    <div class="text-[14px] leading-relaxed text-gray-500 dark:text-gray-400 mb-6 px-2">
                        {{ $slot }}
                        <br><br>
                        {!! $warningText !!}
                    </div>
                </div>

                {{-- Action Buttons --}}
                <form :action="deleteFormAction" method="POST" class="flex gap-4">
                    @csrf @method('DELETE')
                    <button type="button" @click="showDeleteModal = false" class="w-1/2 inline-flex justify-center items-center rounded-xl bg-gray-100 dark:bg-[#2b3040] px-3 py-3 text-[14px] font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-[#343b4e] transition-colors focus:outline-none border border-transparent dark:border-gray-700/50">
                        Cancel
                    </button>
                    <button type="submit" class="w-1/2 inline-flex justify-center items-center gap-2 rounded-xl bg-red-600 dark:bg-[#ac3a3a] px-3 py-3 text-[14px] font-semibold text-white shadow-sm hover:bg-red-700 dark:hover:bg-[#b94444] transition-colors focus:outline-none">
                        <svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>