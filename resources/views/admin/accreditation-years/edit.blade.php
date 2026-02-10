<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Tahun Akreditasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.accreditation-years.update', $accreditation_year) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Year -->
                        <div>
                            <x-input-label for="year" :value="__('Tahun')" />
                            <x-text-input id="year" class="block mt-1 w-full" type="number" name="year" :value="old('year', $accreditation_year->year)" min="2000" max="2100" required />
                            <x-input-error :messages="$errors->get('year')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between border-t border-gray-100 pt-6">
                            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-year-deletion')" class="text-sm text-red-600 hover:text-red-900 underline">
                                {{ __('Hapus Tahun') }}
                            </button>

                            <div class="flex items-center gap-4">
                                <a href="{{ route('admin.accreditation-years.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Batal</a>
                                <x-primary-button>
                                    {{ __('Update') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>

                    <x-modal name="confirm-year-deletion" focusable>
                        <form method="POST" action="{{ route('admin.accreditation-years.destroy', $accreditation_year) }}" class="p-6">
                            @csrf
                            @method('DELETE')

                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Yakin ingin menghapus tahun ini?') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Tahun yang memiliki assessment tidak bisa dihapus. Pastikan tidak ada data terkait sebelum menghapus.') }}
                            </p>

                            <div class="mt-6 flex justify-end">
                                <x-secondary-button x-on:click="$dispatch('close')">
                                    {{ __('Batal') }}
                                </x-secondary-button>

                                <x-danger-button class="ms-3">
                                    {{ __('Hapus Tahun') }}
                                </x-danger-button>
                            </div>
                        </form>
                    </x-modal>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>