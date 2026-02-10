@props([
'options' => [],
'selected' => [],
'name' => 'items',
'id' => null,
'label' => 'Select Items',
'placeholder' => 'Select items...',
])

@php
$id = $id ?? $name;
// Format options for JS: [{value: 1, label: 'Option 1'}, ...]
$optionsJson = json_encode($options);
$selectedJson = json_encode($selected);
@endphp

<div
    x-data="{
        options: {{ $optionsJson }},
        selected: {{ $selectedJson }},
        show: false,
        search: '',
        
        get filteredOptions() {
            if (this.search === '') {
                return this.options.filter(i => !this.selected.includes(i.value));
            }
            return this.options.filter(i => 
                i.label.toLowerCase().includes(this.search.toLowerCase()) && 
                !this.selected.includes(i.value)
            );
        },

        get selectedLabels() {
            return this.options.filter(i => this.selected.includes(i.value));
        },

        select(value) {
            if (!this.selected.includes(value)) {
                this.selected.push(value);
            }
            this.search = '';
            // keep open for multiple selection or close? filament usually keeps open.
            this.show = true; 
        },

        remove(index) {
            this.selected.splice(index, 1);
        },

        toggle() {
            this.show = !this.show;
            if (this.show) {
                this.$nextTick(() => {
                    this.$refs.search.focus();
                });
            }
        },
        
        close() {
            this.show = false;
        }
    }"
    class="relative"
    @click.away="close()">
    <!-- Label -->
    <label for="{{ $id }}" class="block text-xs font-medium text-gray-700 mb-1">
        {{ $label }}
    </label>

    <!-- Trigger / Container -->
    <div
        @click="toggle()"
        class="min-h-[38px] w-full bg-white border border-gray-300 rounded-md shadow-sm px-2 py-1 flex items-center flex-wrap gap-1 cursor-text focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500">
        <!-- Selected Items (Tags) -->
        <template x-for="(value, index) in selected" :key="value">
            <div class="bg-indigo-100 text-indigo-700 rounded px-1.5 py-0.5 text-xs font-medium flex items-center gap-1">
                <span x-text="options.find(o => o.value == value)?.label"></span>
                <button type="button" @click.stop="remove(index)" class="hover:text-indigo-900 focus:outline-none">
                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </template>

        <!-- Search Input -->
        <input
            x-ref="search"
            x-model="search"
            type="text"
            placeholder="{{ $placeholder }}"
            class="outline-none border-none focus:ring-0 p-0 text-xs flex-1 min-w-[100px] bg-transparent">
    </div>

    <!-- Dropdown -->
    <div
        x-show="show"
        x-transition
        class="absolute z-50 mt-1 w-full bg-white shadow-lg rounded-md border border-gray-200 max-h-60 overflow-auto"
        style="display: none;">
        <template x-for="option in filteredOptions" :key="option.value">
            <div
                @click="select(option.value)"
                class="px-3 py-2 text-xs hover:bg-gray-100 cursor-pointer text-gray-700"
                x-text="option.label"></div>
        </template>
        <div x-show="filteredOptions.length === 0" class="px-3 py-2 text-xs text-gray-500">
            No results found.
        </div>
    </div>

    <!-- Hidden Input for Form Submission -->
    <select name="{{ $name }}[]" multiple class="hidden">
        <template x-for="value in selected" :key="value">
            <option :value="value" selected></option>
        </template>
    </select>
</div>