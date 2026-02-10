<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Assessment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.assessments.store') }}" class="space-y-6">
                        @csrf

                        <!-- Accreditation Year -->
                        <div>
                            <x-input-label for="accreditation_year_id" :value="__('Accreditation Year')" />
                            <select id="accreditation_year_id" name="accreditation_year_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
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
                            <select id="assessor_id" name="assessor_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="" disabled selected>Select Assessor</option>
                                @foreach($assessors as $u)
                                <option value="{{ $u->id }}" {{ old('assessor_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->email }})
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('assessor_id')" class="mt-2" />
                        </div>

                        <!-- Unit Name -->
                        <div>
                            <x-input-label for="unit_name" :value="__('Unit Name')" />
                            <x-text-input id="unit_name" class="block mt-1 w-full" type="text" name="unit_name" :value="old('unit_name')" required autofocus />
                            <x-input-error :messages="$errors->get('unit_name')" class="mt-2" />
                        </div>

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