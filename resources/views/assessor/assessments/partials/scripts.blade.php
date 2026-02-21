@push('scripts')
<script>
    // Category color map
    const categoryColors = {
        'Sesuai': {
            bg: 'bg-green-100',
            text: 'text-green-800',
            border: 'border-green-300'
        },
        'Observasi': {
            bg: 'bg-yellow-100',
            text: 'text-yellow-800',
            border: 'border-yellow-300'
        },
        'KTS Minor': {
            bg: 'bg-orange-100',
            text: 'text-orange-800',
            border: 'border-orange-300'
        },
        'KTS Mayor': {
            bg: 'bg-red-500',
            text: 'text-white',
            border: 'border-red-700'
        },
        'OFI': {
            bg: 'bg-blue-100',
            text: 'text-blue-800',
            border: 'border-blue-300'
        },
    };

    function applyCategoryStyle(displayEl, catValue) {
        // Remove all category color classes
        const allClasses = ['bg-green-100', 'text-green-800', 'border-green-300',
            'bg-yellow-100', 'text-yellow-800', 'border-yellow-300',
            'bg-orange-100', 'text-orange-800', 'border-orange-300',
            'bg-red-500', 'text-white', 'border-red-700',
            'bg-blue-100', 'text-blue-800', 'border-blue-300',
            'bg-gray-100', 'text-gray-800', 'border-gray-300'
        ];
        displayEl.classList.remove(...allClasses);

        const colors = categoryColors[catValue];
        if (colors) {
            displayEl.classList.add(colors.bg, colors.text, colors.border);
        } else {
            displayEl.classList.add('bg-gray-100', 'text-gray-800', 'border-gray-300');
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
                'bg-green-100', 'text-green-800', 'border-green-300',
                'bg-yellow-100', 'text-yellow-800', 'border-yellow-300',
                'bg-orange-100', 'text-orange-800', 'border-orange-300',
                'bg-red-100', 'text-red-800', 'border-red-300',
                'bg-red-500', 'text-white', 'border-red-700',
                'bg-blue-100', 'text-blue-800', 'border-blue-300',
                'bg-gray-100', 'text-gray-800', 'border-gray-300'
            );
            const catValue = autoDetectCategory(val);
            const colors = categoryColors[catValue];
            if (colors) {
                selectEl.classList.add(colors.bg, colors.text, colors.border);
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
            'bg-orange-100', 'text-orange-800', 'border-orange-300',
            'border-gray-300'
        );
        switch (el.value) {
            case 'Close':
                el.classList.add('bg-teal-600', 'text-white', 'border-teal-700');
                break;
            case 'Open':
                el.classList.add('bg-yellow-100', 'text-yellow-800', 'border-yellow-300');
                break;
            case 'Toleran':
                el.classList.add('bg-orange-100', 'text-orange-800', 'border-orange-300');
                break;
            default:
                el.classList.add('border-gray-300');
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