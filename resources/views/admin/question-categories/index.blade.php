<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Prodi</h1>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">Kelola struktur Prodi dan sub-kategori pertanyaan.</p>
            </div>
            <a href="{{ route('admin.question-categories.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Buat Prodi Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ showDeleteModal: false, deleteTargetName: '', deleteFormAction: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm">
                <svg class="h-5 w-5 text-emerald-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm sm:rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                    Nama Prodi
                                </th>
                                <th scope="col" class="relative px-6 py-4">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @if($tree->isEmpty())
                            <tr>
                                <td colspan="2" class="px-6 py-16 text-center">
                                    <div class="mx-auto w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-800/80 flex items-center justify-center mb-4">
                                        <svg class="w-7 h-7 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada Prodi.</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Coba buat Prodi baru untuk memulai.</p>
                                </td>
                            </tr>
                            @else
                            @foreach($tree as $node)
                            @include('admin.question-categories.partials.row', ['item' => $node['item'], 'children' => $node['children'], 'level' => 0])
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- Delete Confirmation Modal --}}
        <x-delete-modal title="Hapus Kategori?">
            Anda yakin ingin menghapus Prodi <span class="text-white font-medium" x-text="deleteTargetName"></span> secara permanen?
        </x-delete-modal>
    </div>
</x-app-layout>