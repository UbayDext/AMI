<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Question Category') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.question-categories.update', $category) }}" class="space-y-6">
                        @csrf
                        @method('PUT')



                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $category->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>



                        <div class="flex items-center justify-between border-t border-gray-100 pt-6">

                            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-category-deletion')" class="text-sm text-red-600 hover:text-red-900 underline">
                                {{ __('Delete Category') }}
                            </button>

                            <div class="flex items-center gap-4">
                                <a href="{{ route('admin.question-categories.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Cancel</a>
                                <x-primary-button>
                                    {{ __('Update Category') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>

                    <x-modal name="confirm-category-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                        <form method="POST" action="{{ route('admin.question-categories.destroy', $category) }}" class="p-6">
                            @csrf
                            @method('DELETE')

                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Are you sure you want to delete this category?') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('If this category has sub-categories or questions, they might need to be reassigned or deleted. Please proceed with caution.') }}
                            </p>

                            <div class="mt-6 flex justify-end">
                                <x-secondary-button x-on:click="$dispatch('close')">
                                    {{ __('Cancel') }}
                                </x-secondary-button>

                                <x-danger-button class="ms-3">
                                    {{ __('Delete Category') }}
                                </x-danger-button>
                            </div>
                        </form>
                    </x-modal>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>