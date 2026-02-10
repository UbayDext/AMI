<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Question') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded-r shadow-sm">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.questions.update', $question) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Standard & Category -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="standard_id" :value="__('Standard')" />
                                <select id="standard_id" name="standard_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- Select Standard --</option>
                                    @foreach($standards as $s)
                                    <option value="{{ $s->id }}" @selected((int)$question->standard_id === (int)$s->id)>
                                        {{ $s->code }} - {{ $s->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('standard_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="category_id" :value="__('Category')" />
                                <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $c)
                                    <option value="{{ $c->id }}" @selected(old('category_id', $question->category_id ?? null) == $c->id)>
                                        {{ $c->code ? $c->code.' - ' : '' }}{{ $c->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Label -->
                        <div>
                            <x-input-label for="label" :value="__('Question Label')" />
                            <x-text-input id="label" class="block mt-1 w-full" type="text" name="label" :value="old('label', $question->label)" required />
                            <x-input-error :messages="$errors->get('label')" class="mt-2" />
                        </div>

                        <!-- Type & Sort Order -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="type" :value="__('Type')" />
                                <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    @foreach($types as $t)
                                    <option value="{{ $t }}" @selected(old('type', $question->type) === $t)>
                                        {{ ucfirst($t) }}
                                    </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="sort_order" :value="__('Sort Order')" />
                                <x-text-input id="sort_order" class="block mt-1 w-full" type="number" name="sort_order" :value="old('sort_order', $question->sort_order)" />
                                <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Reference -->
                        <div>
                            <x-input-label for="reference" :value="__('Reference')" />
                            <textarea id="reference" name="reference" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('reference', $question->reference ?? '') }}</textarea>
                            <x-input-error :messages="$errors->get('reference')" class="mt-2" />
                        </div>

                        <!-- Toggles -->
                        <div class="flex items-center gap-8">
                            <label for="is_required" class="inline-flex items-center cursor-pointer">
                                <input id="is_required" type="checkbox" name="is_required" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_required', $question->is_required))>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Required') }}</span>
                            </label>

                            <label for="is_active" class="inline-flex items-center cursor-pointer">
                                <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_active', $question->is_active))>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Active') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-between mt-4">
                            <!-- Delete Button Trigger -->
                            <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-question-deletion')">
                                {{ __('Delete Question') }}
                            </x-danger-button>

                            <div class="flex items-center">
                                <x-secondary-button onclick="window.history.back()" class="mr-3">
                                    {{ __('Cancel') }}
                                </x-secondary-button>

                                <x-primary-button>
                                    {{ __('Update Question') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl text-sm text-gray-600">
                    <h3 class="font-medium text-gray-900 mb-2">Notes</h3>
                    <p>If the type is <b>Select</b> or <b>Radio</b>, the system will automatically generate <b>Yes / No</b> options initially. You can modify them later if needed.</p>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <x-modal name="confirm-question-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" class="p-6">
                    @csrf
                    @method('DELETE')

                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Are you sure you want to delete this question?') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Once this question is deleted, all of its resources and data will be permanently deleted.') }}
                    </p>

                    <div class="mt-6 flex justify-end">
                        <x-secondary-button x-on:click="$dispatch('close')">
                            {{ __('Cancel') }}
                        </x-secondary-button>

                        <x-danger-button class="ml-3">
                            {{ __('Delete Question') }}
                        </x-danger-button>
                    </div>
                </form>
            </x-modal>

        </div>
    </div>
</x-app-layout>