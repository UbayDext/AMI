<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Fill Assessment') }}
                </h2>
                <div class="text-xs text-gray-500 mt-1">
                    Unit: <span class="font-medium text-gray-700">{{ $assessment->unit_name }}</span> |
                    Status: <span class="font-medium text-gray-700">{{ ucfirst($assessment->status) }}</span>
                </div>
            </div>

            <a href="{{ route('assessor.assessments.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="p-4 bg-green-100 border border-green-200 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="p-4 bg-red-100 border border-red-200 text-red-700 rounded-md">
                <b>Validation Failed:</b>
                <ul class="list-disc ml-5 mt-1 text-sm">
                    @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form id="assessmentForm" method="POST"
                action="{{ route('assessor.assessments.fill.update', $assessment) }}"
                enctype="multipart/form-data">
                @csrf

                <!-- Instructions Card -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r shadow-sm mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Petunjuk:</strong> Pilih keterangan untuk setiap pertanyaan. Jika status <strong>"Tidak Sesuai"</strong>, detail PTK akan muncul otomatis untuk diisi.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Questions Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">Assessment Questions</h3>
                        <p class="text-sm text-gray-500 mt-1">Isi keterangan untuk setiap pertanyaan. Scroll horizontal untuk melihat detail PTK.</p>
                    </div>

                    @php
                    $no = 1;
                    @endphp

                    @forelse($groupedQuestions as $categoryId => $items)
                    @php
                    $cat = $items->first()?->category;
                    $catTitle = $cat ? $cat->name : 'Uncategorized';
                    @endphp

                    <!-- Category Section -->
                    <div class="border-b border-gray-300 bg-gradient-to-r from-indigo-50 to-white px-6 py-3">
                        <h4 class="text-sm font-bold text-indigo-900 uppercase tracking-wide">
                            {{ $catTitle }}
                        </h4>
                    </div>

                    <!-- Table with horizontal scroll -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100 sticky top-0 z-10">
                                <tr>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">No</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider" style="min-width: 250px;">Pertanyaan</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider" style="min-width: 100px;">Referensi</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider" style="min-width: 200px;">Bukti</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider" style="min-width: 150px;">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($items as $q)
                                @php
                                $ans = $answers[$q->id] ?? null;
                                $ptk = $ptks[$q->id] ?? null;
                                @endphp
                                @include('assessor.assessments.partials.ptk-table-row', [
                                'question' => $q,
                                'answer' => $ans,
                                'ptk' => $ptk,
                                'no' => $no,
                                'areas' => $areas
                                ])
                                @php $no++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @empty
                    <div class="px-6 py-12 text-center text-gray-500">
                        <p>No questions available for this assessment.</p>
                    </div>
                    @endforelse
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-4 mt-6">
                    <a href="{{ route('assessor.assessments.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Cancel</a>

                    {{-- Save Draft --}}
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Assessment
                    </button>

                    {{-- Submit Final --}}
                    <button type="button" onclick="document.getElementById('submitModal').classList.remove('hidden')" class="inline-flex items-center px-6 py-3 bg-emerald-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Submit Assessment
                    </button>
                </div>

                {{-- Hidden submit flag --}}
                <input type="hidden" name="submit" id="submitFlag" value="0">
            </form>

            {{-- Confirmation Modal --}}
            <div id="submitModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4">
                    {{-- Backdrop --}}
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('submitModal').classList.add('hidden')"></div>

                    {{-- Modal --}}
                    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Submit</h3>
                        </div>

                        <p class="text-sm text-gray-600 mb-2">Apakah Anda yakin ingin <strong>submit</strong> assessment ini?</p>
                        <p class="text-sm text-gray-500 mb-6">Setelah di-submit, assessment tidak dapat diedit lagi.</p>

                        <div class="flex items-center justify-end gap-3">
                            <button type="button" onclick="document.getElementById('submitModal').classList.add('hidden')" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
                                Batal
                            </button>
                            <button type="button" onclick="confirmSubmit()" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 transition">
                                Ya, Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

        // Confirm and submit assessment
        function confirmSubmit() {
            document.getElementById('submitFlag').value = '1';
            document.getElementById('submitModal').classList.add('hidden');
            document.getElementById('assessmentForm').submit();
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
</x-app-layout>