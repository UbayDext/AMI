<x-app-layout>
    <x-slot name="header"><span class="sr-only">Ajukan Standar</span></x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-gray-100 dark:bg-gray-800/80 flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-3xl shadow-lg p-8">

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-7">
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/60 rounded-2xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Ajukan Permintaan Standar</h1>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Pilih standar yang ingin Anda kelola (maks. 20 dalam sekali pengajuan)</p>
                </div>
            </div>

            {{-- Flash --}}
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
                class="mb-5 flex items-center gap-2.5 p-3.5 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-400 rounded-2xl text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-5 p-3 bg-red-50 border border-red-100 rounded-2xl text-xs text-red-600">
                <ul class="list-disc ml-3 space-y-0.5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            {{-- Status request sebelumnya --}}
            @if($existing)
            @php
                $statusColor = match($existing->status) {
                    'pending'  => 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-700 text-amber-700 dark:text-amber-400',
                    'approved' => 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-400',
                    'rejected' => 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700 text-red-700 dark:text-red-400',
                };
            @endphp
            <div class="mb-6 p-4 border rounded-2xl {{ $statusColor }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold">Permintaan Terakhir</p>
                        <p class="text-xs mt-0.5 opacity-80">Status: {{ $existing->statusLabel() }} · {{ $existing->created_at->diffForHumans() }}</p>
                        <p class="text-xs mt-1 opacity-80 break-words">{{ $existing->roleLabel() }}</p>
                        @if($existing->review_note)
                        <p class="text-xs mt-1.5 opacity-70">Catatan admin: {{ $existing->review_note }}</p>
                        @endif
                    </div>
                    @if($existing->isPending())
                    <form method="POST" action="{{ route('role-requests.destroy', $existing) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-medium underline opacity-70 hover:opacity-100 flex-shrink-0">
                            Batalkan
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endif

            {{-- Form --}}
            @if(!$existing || !$existing->isPending())

            @if($standards->isEmpty())
            <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <p class="text-sm font-medium">Semua standar sudah Anda kelola.</p>
            </div>
            @else
            @php $remaining = 20 - auth()->user()->roles->whereIn('name', $standards->pluck('code'))->count(); @endphp

            <form method="POST" action="{{ route('role-requests.store') }}" class="space-y-5"
                x-data="{ selected: {{ Js::from($evidenceCodes) }} }">
                @csrf

                {{-- Pilih Standar --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Pilih Standar yang Ingin Dikelola
                        </label>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                Dipilih: <span class="font-semibold text-indigo-600 dark:text-indigo-400" x-text="selected.length"></span>/{{ $remaining }}
                            </span>
                            <button type="button" x-show="selected.length > 0" @click="selected = []"
                                class="text-xs text-red-500 hover:text-red-700 font-medium transition-colors">
                                Reset
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-1.5 max-h-80 overflow-y-auto pr-1">
                        @foreach($standards as $standard)
                        <label class="cursor-pointer"
                            @click.prevent="
                                if (selected.includes('{{ $standard->code }}')) {
                                    selected = selected.filter(s => s !== '{{ $standard->code }}')
                                } else if (selected.length < {{ $remaining }}) {
                                    selected = [...selected, '{{ $standard->code }}']
                                }
                            ">
                            <input type="checkbox" name="requested_roles[]" value="{{ $standard->code }}"
                                class="sr-only"
                                :checked="selected.includes('{{ $standard->code }}')"
                                @change.stop />
                            <div class="flex items-center gap-3 p-3 rounded-2xl border-2 transition-all duration-150"
                                :class="selected.includes('{{ $standard->code }}')
                                    ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 shadow-sm'
                                    : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 hover:border-gray-300'">
                                <div class="flex-shrink-0 w-5 h-5 rounded border-2 flex items-center justify-center transition-all"
                                    :class="selected.includes('{{ $standard->code }}') ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300 dark:border-gray-600'">
                                    <svg x-show="selected.includes('{{ $standard->code }}')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="flex-shrink-0 w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                                    <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400">{{ $standard->code }}</span>
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300 leading-snug">{{ $standard->name }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <p x-show="selected.length >= {{ $remaining }}" x-cloak
                        class="mt-2 text-xs text-amber-600 dark:text-amber-400 font-medium">
                        Batas maksimal {{ $remaining }} standar sudah dipilih.
                    </p>
                    @error('requested_roles')
                    <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Alasan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Alasan Pengajuan <span class="font-normal text-gray-400">(Opsional)</span>
                    </label>
                    <textarea name="reason" rows="3"
                        class="w-full bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-3 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition-colors resize-none"
                        placeholder="Jelaskan mengapa Anda ingin mengelola standar-standar ini...">{{ old('reason') }}</textarea>
                </div>

                <button type="submit" :disabled="selected.length === 0"
                    class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-2xl shadow-sm hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Ajukan Permintaan
                    <span x-show="selected.length > 0" x-text="`(${selected.length} standar)`"
                        class="text-indigo-200 text-xs font-normal"></span>
                </button>
            </form>
            @endif

            @else
            <p class="text-center text-sm text-gray-400 dark:text-gray-500 py-4">
                Tunggu admin memproses permintaan Anda sebelum mengajukan yang baru.
            </p>
            @endif
        </div>
    </div>
</x-app-layout>
