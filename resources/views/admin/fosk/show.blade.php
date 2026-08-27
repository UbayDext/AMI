<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-600 text-white mr-2">{{ $criteria->code }}</span>
                    {{ $criteria->name }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola data {{ strtoupper($type) }} untuk kriteria ini.</p>
            </div>
            <div class="flex items-center gap-3">
                <select onchange="window.location.href='{{ route('admin.fosk.show', $criteria) }}?year_id='+this.value+'&type={{ $type }}'"
                    class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($years as $y)
                    <option value="{{ $y->id }}" @selected($y->id == $yearId)>Tahun {{ $y->year }}</option>
                    @endforeach
                </select>
                <a href="{{ route('admin.fosk.index', ['year_id' => $yearId]) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">← Semua Kriteria</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{ showAddForm: false, showDeleteModal: false, deleteFormAction: '', deleteTargetName: '', editingId: null }">

        <x-alert-success :message="session('success')" />
        @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl text-sm">{{ session('error') }}</div>
        @endif

        {{-- Type Tabs --}}
        <div class="flex items-center gap-1 mb-6 bg-gray-100 dark:bg-gray-800/80 rounded-xl p-1 w-fit">
            <a href="{{ route('admin.fosk.show', [$criteria, 'year_id' => $yearId, 'type' => 'lkpt']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                    {{ $type === 'lkpt' ? 'bg-white dark:bg-gray-700 text-indigo-700 dark:text-indigo-300 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                📊 LKPT <span class="text-xs opacity-60">(Kuantitatif)</span>
            </a>
            <a href="{{ route('admin.fosk.show', [$criteria, 'year_id' => $yearId, 'type' => 'lkpd']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                    {{ $type === 'lkpd' ? 'bg-white dark:bg-gray-700 text-indigo-700 dark:text-indigo-300 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                📝 LKPD <span class="text-xs opacity-60">(Kualitatif)</span>
            </a>
        </div>

        {{-- Documents List --}}
        <div class="space-y-4">
            @forelse($documents as $doc)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5" x-data="{ showEvidenceForm: false, expanded: false }">
                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm">{{ $doc->title }}</h4>
                                @php
                                $statusColor = match($doc->status) {
                                'final' => 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300',
                                'review' => 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300',
                                default => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                                };
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $statusColor }}">{{ ucfirst($doc->status) }}</span>
                            </div>
                            @if($doc->pic)
                            <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1 mb-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                PIC: <strong>{{ $doc->pic }}</strong>
                            </div>
                            @endif

                            {{-- Data/Description --}}
                            @if($type === 'lkpt' && $doc->data_value)
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 text-sm text-gray-800 dark:text-gray-200 mt-2">
                                <span class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide">Data Kuantitatif</span>
                                <div class="mt-1">{{ $doc->data_value }}</div>
                            </div>
                            @endif
                            @if($doc->description)
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-3 text-sm text-gray-700 dark:text-gray-300 mt-2">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Deskripsi</span>
                                <div class="mt-1 whitespace-pre-line">{{ $doc->description }}</div>
                            </div>
                            @endif
                        </div>

                        {{-- Action buttons --}}
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <button @click="expanded = !expanded" class="p-1.5 text-gray-400 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button @click="deleteTargetName = '{{ addslashes($doc->title) }}'; deleteFormAction = '{{ route('admin.fosk.documents.destroy', $doc) }}'; showDeleteModal = true"
                                class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Edit Form (inline, collapsible) --}}
                    <div x-show="expanded" x-collapse class="mt-4 border-t border-gray-100 dark:border-gray-700 pt-4">
                        <form method="POST" action="{{ route('admin.fosk.documents.update', $doc) }}">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Judul *</label>
                                    <input type="text" name="title" value="{{ $doc->title }}" required class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">PIC</label>
                                    <input type="text" name="pic" value="{{ $doc->pic }}" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none" placeholder="Penanggung jawab...">
                                </div>
                                @if($type === 'lkpt')
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Data Kuantitatif</label>
                                    <textarea name="data_value" rows="3" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none" placeholder="Isi data kuantitatif...">{{ $doc->data_value }}</textarea>
                                </div>
                                @endif
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Deskripsi / Narasi</label>
                                    <textarea name="description" rows="4" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none" placeholder="Deskripsi kualitatif...">{{ $doc->description }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
                                    <select name="status" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                                        <option value="draft" @selected($doc->status==='draft')>Draft</option>
                                        <option value="review" @selected($doc->status==='review')>Review</option>
                                        <option value="final" @selected($doc->status==='final')>Final</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex justify-end mt-3">
                                <button type="submit" class="px-4 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>

                    {{-- Evidences section --}}
                    <div class="mt-4 border-t border-gray-100 dark:border-gray-700 pt-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Bukti ({{ $doc->evidences->count() }})</span>
                            <button @click="showEvidenceForm = !showEvidenceForm" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Bukti
                            </button>
                        </div>

                        @if($doc->evidences->isNotEmpty())
                        <div class="space-y-1.5">
                            @foreach($doc->evidences as $ev)
                            <div class="flex items-center justify-between gap-2 text-xs bg-gray-50 dark:bg-gray-900/50 rounded-lg px-3 py-2">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    @if($ev->link_url)
                                    <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    @else
                                    <svg class="w-3.5 h-3.5 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    @endif
                                    <div class="min-w-0">
                                        @if($ev->link_url)
                                        <a href="{{ $ev->link_url }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium truncate block">{{ $ev->title }}</a>
                                        @elseif($ev->file_path)
                                        <a href="{{ asset('storage/'.$ev->file_path) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium truncate block">{{ $ev->title }}</a>
                                        @else
                                        <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $ev->title }}</span>
                                        @endif
                                        @if($ev->original_name)
                                        <span class="text-gray-400 dark:text-gray-500">{{ $ev->original_name }}</span>
                                        @endif
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('admin.fosk.evidences.destroy', $ev) }}" onsubmit="return confirm('Hapus bukti ini?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1 text-red-400 hover:text-red-600" title="Hapus"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg></button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Add evidence form --}}
                        <div x-show="showEvidenceForm" x-collapse class="mt-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <form method="POST" action="{{ route('admin.fosk.evidences.store', $doc) }}" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Judul Bukti *</label>
                                    <input type="text" name="title" required class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none" placeholder="e.g. SK Kurikulum 2024">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Link URL (Google Drive / Website)</label>
                                    <input type="url" name="link_url" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none" placeholder="https://drive.google.com/...">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Atau Upload File</label>
                                    <input type="file" name="file" class="w-full text-xs text-gray-500 dark:text-gray-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/40 dark:file:text-indigo-400">
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="showEvidenceForm = false" class="px-3 py-1.5 text-xs text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition">Batal</button>
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Simpan Bukti</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
                <svg class="mx-auto w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-1">Belum ada dokumen {{ strtoupper($type) }}</p>
                <p class="text-gray-400 dark:text-gray-500 text-xs">Klik tombol di bawah untuk menambahkan.</p>
            </div>
            @endforelse
        </div>

        {{-- Add Document Button --}}
        <div class="mt-6">
            <button @click="showAddForm = !showAddForm"
                class="w-full py-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-500 dark:text-gray-400 hover:border-indigo-400 hover:text-indigo-600 dark:hover:border-indigo-500 dark:hover:text-indigo-400 transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Dokumen {{ strtoupper($type) }}
            </button>
        </div>

        {{-- Add Document Form --}}
        <div x-show="showAddForm" x-collapse class="mt-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">Tambah Dokumen {{ strtoupper($type) }}</h4>
                <form method="POST" action="{{ route('admin.fosk.documents.store', $criteria) }}">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="year_id" value="{{ $yearId }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Judul *</label>
                            <input type="text" name="title" required class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none" placeholder="e.g. Jumlah Dosen Tetap">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">PIC</label>
                            <input type="text" name="pic" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none" placeholder="Penanggung jawab...">
                        </div>
                        @if($type === 'lkpt')
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Data Kuantitatif</label>
                            <textarea name="data_value" rows="3" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none" placeholder="Isi data kuantitatif..."></textarea>
                        </div>
                        @endif
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Deskripsi</label>
                            <textarea name="description" rows="4" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none" placeholder="Deskripsi kualitatif..."></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="showAddForm = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Delete Modal --}}
        <x-delete-modal title="Hapus Dokumen?">
            Anda yakin ingin menghapus <span class="text-white font-medium" x-text="deleteTargetName"></span> beserta semua bukti-nya secara permanen?
        </x-delete-modal>
    </div>
</x-app-layout>