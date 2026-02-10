<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Question') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.questions.store') }}" class="space-y-6">
                        @csrf

                        <!-- Standard & Category -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="standard_id" :value="__('Standard')" />
                                <select id="standard_id" name="standard_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- Select Standard --</option>
                                    @foreach($standards as $s)
                                    <option value="{{ $s->id }}" {{ old('standard_id') == $s->id ? 'selected' : '' }}>
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
                                    <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>
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
                            <x-text-input id="label" class="block mt-1 w-full" type="text" name="label" :value="old('label')" required />
                            <x-input-error :messages="$errors->get('label')" class="mt-2" />
                        </div>

                        <!-- Type & Sort Order -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="type" :value="__('Type')" />
                                <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    @foreach($types as $t)
                                    <option value="{{ $t }}" {{ old('type') == $t ? 'selected' : '' }}>
                                        {{ ucfirst($t) }}
                                    </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="sort_order" :value="__('Sort Order')" />
                                <x-text-input id="sort_order" class="block mt-1 w-full" type="number" name="sort_order" :value="old('sort_order', 0)" />
                                <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Reference -->
                        <div>
                            <x-input-label for="reference" :value="__('Reference')" />
                            <textarea id="reference" name="reference" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('reference') }}</textarea>
                            <x-input-error :messages="$errors->get('reference')" class="mt-2" />
                        </div>

                        <!-- Toggles -->
                        <div class="flex items-center gap-8">
                            <label for="is_required" class="inline-flex items-center cursor-pointer">
                                <input id="is_required" type="checkbox" name="is_required" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_required') ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Required') }}</span>
                            </label>

                            <label for="is_active" class="inline-flex items-center cursor-pointer">
                                <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_active', true) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Active') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-secondary-button onclick="window.history.back()" class="mr-3">
                                {{ __('Cancel') }}
                            </x-secondary-button>

                            <x-primary-button>
                                {{ __('Create Question') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>