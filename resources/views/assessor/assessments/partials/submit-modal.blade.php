<div id="submitModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('submitModal').classList.add('hidden')"></div>

        {{-- Modal --}}
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6 z-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Konfirmasi Submit</h3>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Apakah Anda yakin ingin <strong>submit</strong> assessment ini?</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Setelah di-submit, assessment tidak dapat diedit lagi.</p>

            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('submitModal').classList.add('hidden')" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" form="assessmentForm" name="submit" value="1" onclick="document.getElementById('submitModal').classList.add('hidden')" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 transition">
                    Ya, Submit
                </button>
            </div>
        </div>
    </div>
</div>