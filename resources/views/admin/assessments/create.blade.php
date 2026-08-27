<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Assessment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('admin.assessments.store') }}" class="space-y-6">
                        @csrf

                        <!-- Accreditation Year -->
                        <div>
                            <x-input-label for="accreditation_year_id" :value="__('Accreditation Year')" />
                            <select id="accreditation_year_id" name="accreditation_year_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="" disabled selected>Select Year</option>
                                @foreach($years as $y)
                                <option value="{{ $y->id }}" {{ old('accreditation_year_id') == $y->id ? 'selected' : '' }}>
                                    {{ $y->year }}
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('accreditation_year_id')" class="mt-2" />
                        </div>

                        <!-- Assessor -->
                        <div>
                            <x-input-label for="assessor_id" :value="__('Assessor')" />
                            <select id="assessor_id" name="assessor_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="" disabled selected>Select Assessor</option>
                                @foreach($assessors as $u)
                                <option value="{{ $u->id }}" {{ old('assessor_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->email }})
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('assessor_id')" class="mt-2" />
                        </div>

                        <!-- Standard Filter -->
                        <div>
                            <x-input-label for="standard_filter" :value="__('Standard (Optional)')" />
                            <select id="standard_filter" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Semua Standar --</option>
                                @foreach($standards as $std)
                                <option value="{{ $std->id }}">{{ $std->code }} - {{ $std->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Unit (Kategori Soal) -->
                        <div>
                            <x-input-label for="unit_name" :value="__('Study Program')" />
                            <select id="unit_name" name="unit_name" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="" disabled selected>Select Study Program</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->name }}"
                                    data-cat-id="{{ $cat->id }}"
                                    {{ old('unit_name') == $cat->name ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('unit_name')" class="mt-2" />
                        </div>

                        @push('scripts')
                        <script>
                            const standardCategoryMap = {
                                !!json_encode($standardCategoryMap) !!
                            };

                            document.addEventListener('DOMContentLoaded', function() {
                                const standardFilter = document.getElementById('standard_filter');
                                const unitSelect = document.getElementById('unit_name');
                                const allOptions = Array.from(unitSelect.querySelectorAll('option'));

                                standardFilter.addEventListener('change', function() {
                                    const selectedStandardId = this.value;
                                    const allowedCatIds = selectedStandardId ? (standardCategoryMap[selectedStandardId] || []) : null;

                                    allOptions.forEach(option => {
                                        if (!option.value) return; // skip placeholder
                                        const catId = parseInt(option.getAttribute('data-cat-id'));
                                        if (allowedCatIds === null || allowedCatIds.includes(catId)) {
                                            option.style.display = '';
                                            option.disabled = false;
                                        } else {
                                            option.style.display = 'none';
                                            option.disabled = true;
                                        }
                                    });

                                    // Reset selection if current selection is now filtered out
                                    const currentOption = unitSelect.querySelector('option:checked');
                                    if (currentOption && currentOption.disabled) {
                                        unitSelect.value = '';
                                    }
                                });
                            });
                        </script>
                        @endpush

                        <div class="flex items-center justify-end mt-4">
                            <x-secondary-button onclick="window.history.back()" class="mr-3">
                                {{ __('Cancel') }}
                            </x-secondary-button>

                            <x-primary-button>
                                {{ __('Create Assessment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
