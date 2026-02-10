<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Tahun Akreditasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.accreditation-years.store') }}" class="space-y-6">
                        @csrf

                        <!-- Year -->
                        <div>
                            <x-input-label for="year" :value="__('Tahun')" />
                            <x-text-input id="year" class="block mt-1 w-full" type="number" name="year" :value="old('year')" min="2000" max="2100" placeholder="Contoh: 2024" required autofocus />
                            <x-input-error :messages="$errors->get('year')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500">Masukkan tahun akreditasi (contoh: 2024)</p>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.accreditation-years.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Batal</a>
                            <x-primary-button>
                                {{ __('Simpan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>