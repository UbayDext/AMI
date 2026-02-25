@push('scripts')
<script>
    // Category color map
    const categoryColors = {
        'Sesuai': 'bg-green-100 text-green-800 border-green-300 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-800',
        'Observasi': 'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-800',
        'KTS Minor': 'bg-orange-100 text-orange-800 border-orange-300 dark:bg-orange-900/40 dark:text-orange-300 dark:border-orange-800',
        'KTS Mayor': 'bg-red-500 text-white border-red-700 dark:bg-red-900/60 dark:text-red-200 dark:border-red-800',
        'OFI': 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-800',
    };

    function applyCategoryStyle(displayEl, catValue) {
        // Remove all category color classes
        const allClasses = ['bg-green-100', 'text-green-800', 'border-green-300', 'dark:bg-emerald-900/40', 'dark:text-emerald-300', 'dark:border-emerald-800',
            'bg-yellow-100', 'text-yellow-800', 'border-yellow-300', 'dark:bg-amber-900/40', 'dark:text-amber-300', 'dark:border-amber-800',
            'bg-orange-100', 'text-orange-800', 'border-orange-300', 'dark:bg-orange-900/40', 'dark:text-orange-300', 'dark:border-orange-800',
            'bg-red-500', 'text-white', 'border-red-700', 'dark:bg-red-900/60', 'dark:text-red-200', 'dark:border-red-800',
            'bg-blue-100', 'text-blue-800', 'border-blue-300', 'dark:bg-blue-900/40', 'dark:text-blue-300', 'dark:border-blue-800',
            'bg-gray-100', 'dark:bg-gray-800/80', 'text-gray-800', 'dark:text-gray-200', 'border-gray-300', 'dark:border-gray-600'
        ];
        displayEl.classList.remove(...allClasses);

        const colors = categoryColors[catValue];
        if (colors) {
            displayEl.className += ' ' + colors;
        } else {
            displayEl.classList.add('bg-gray-100', 'dark:bg-gray-800/80', 'text-gray-800', 'dark:text-gray-200', 'border-gray-300', 'dark:border-gray-600');
        }
    }

    // Determine category from keterangan value
    function autoDetectCategory(val) {
        if (val === 'sesuai') {
            return 'Sesuai';
        } else if (val === 'sebagian_sesuai') {
            return 'Observasi';
        } else if (val === 'tidak_sesuai_tidak_ada_bukti' || val === 'tidak_dilaksanakan_tidak_ada_bukti') {
            return 'KTS Mayor';
        } else if (val && val.startsWith('tidak')) {
            return 'KTS Minor';
        }
        return '';
    }

    // Toggle PTK row when keterangan changes
    function togglePtkRow(qid, val) {
        const row = document.getElementById('ptk-row-' + qid);
        const selectEl = document.querySelector(`.ket-select[data-qid="${qid}"]`);

        // Update Keterangan select styling (same colors as Kategori)
        if (selectEl) {
            selectEl.classList.remove(
                'bg-green-100', 'text-green-800', 'border-green-300', 'dark:bg-emerald-900/40', 'dark:text-emerald-300', 'dark:border-emerald-800',
                'bg-yellow-100', 'text-yellow-800', 'border-yellow-300', 'dark:bg-amber-900/40', 'dark:text-amber-300', 'dark:border-amber-800',
                'bg-orange-100', 'text-orange-800', 'border-orange-300', 'dark:bg-orange-900/40', 'dark:text-orange-300', 'dark:border-orange-800',
                'bg-red-100', 'text-red-800', 'border-red-300', 'bg-red-500', 'text-white', 'border-red-700', 'dark:bg-red-900/60', 'dark:text-red-200', 'dark:border-red-800',
                'bg-blue-100', 'text-blue-800', 'border-blue-300', 'dark:bg-blue-900/40', 'dark:text-blue-300', 'dark:border-blue-800',
                'bg-gray-100', 'dark:bg-gray-800/80', 'text-gray-800', 'dark:text-gray-200', 'border-gray-300', 'dark:border-gray-600'
            );
            const catValue = autoDetectCategory(val);
            const colors = categoryColors[catValue];
            if (colors) {
                selectEl.className = 'ket-select block w-full rounded-md shadow-sm sm:text-xs font-semibold ' + colors;
            } else {
                selectEl.className = 'ket-select block w-full rounded-md shadow-sm sm:text-xs font-semibold bg-gray-100 dark:bg-gray-800/80 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600';
            }
        }

        if (row) {
            if (!val || val === 'sesuai') {
                row.classList.add('hidden');

                // Auto-set Category to Sesuai
                const catValue = 'Sesuai';
                const categoryInput = document.getElementById('ptk_kategori_' + qid);
                const categoryDisplay = document.getElementById('ptk_kategori_display_' + qid);

                if (categoryInput) {
                    categoryInput.value = catValue;
                }
                if (categoryDisplay) {
                    categoryDisplay.value = catValue;
                    applyCategoryStyle(categoryDisplay, catValue);
                }
            } else {
                row.classList.remove('hidden');

                // Auto-set Category
                const catValue = autoDetectCategory(val);
                const categoryInput = document.getElementById('ptk_kategori_' + qid);
                const categoryDisplay = document.getElementById('ptk_kategori_display_' + qid);

                if (categoryInput) {
                    categoryInput.value = catValue;
                }
                if (categoryDisplay) {
                    categoryDisplay.value = catValue;
                    applyCategoryStyle(categoryDisplay, catValue);
                }
            }
        }
    }



    // Style TL Status dropdown color
    function styleTlStatus(el) {
        el.classList.remove(
            'bg-teal-600', 'text-white', 'border-teal-700',
            'bg-yellow-100', 'text-yellow-800', 'border-yellow-300',
            'bg-orange-100', 'text-orange-800', 'border-orange-300', 'dark:bg-orange-900/40', 'dark:text-orange-300', 'dark:border-orange-800',
            'border-gray-300', 'dark:border-gray-600', 'dark:bg-gray-900', 'dark:text-gray-300'
        );
        switch (el.value) {
            case 'Close':
                el.classList.add('bg-teal-600', 'text-white', 'border-teal-700', 'dark:bg-teal-900/40', 'dark:text-teal-300', 'dark:border-teal-800');
                break;
            case 'Open':
                el.classList.add('bg-yellow-100', 'text-yellow-800', 'border-yellow-300', 'dark:bg-yellow-900/40', 'dark:text-yellow-300', 'dark:border-yellow-800');
                break;
            case 'Toleran':
                el.classList.add('bg-orange-100', 'text-orange-800', 'border-orange-300', 'dark:bg-orange-900/40', 'dark:text-orange-300', 'dark:border-orange-800');
                break;
            default:
                el.classList.add('border-gray-300', 'dark:border-gray-600', 'dark:bg-gray-900', 'dark:text-gray-300');
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const selects = document.querySelectorAll('.ket-select');
        selects.forEach(select => {
            togglePtkRow(select.dataset.qid, select.value);
        });
    });
</script>
@endpush