{{-- ── Charts Row ───────────────────────────────────────── --}}
<section>
<div class="mb-3"><h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Analitik Audit</h3><p class="text-xs text-slate-400">Perbandingan assessment dan distribusi kategori tindak lanjut.</p></div>
<div class="grid grid-cols-1 gap-5 xl:grid-cols-12">

    {{-- Radar: Assessments & Findings per Year (3/5) --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-7 dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Assessments & Temuan per Tahun</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Perbandingan assessment submitted vs temuan</p>
            </div>
            <span class="text-xs bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 font-medium px-2.5 py-1 rounded-full">Radar</span>
        </div>
        <div class="h-72">
            <canvas id="chartAssessments"></canvas>
        </div>
    </div>

    {{-- Kategori PTK Radar (2/5) --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-5 dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Distribusi Kategori PTK</h3>
                <p id="chartKategoriSubtitle" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Semua tahun</p>
            </div>
            <select id="yearFilter" class="text-xs border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1 px-2">
                <option value="">Semua Tahun</option>
                {{-- Populated via JS --}}
            </select>
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap gap-x-3 gap-y-1 mb-3">
            <span class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400"><span class="w-2.5 h-2.5 rounded-full inline-block bg-green-500"></span>Sesuai</span>
            <span class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400"><span class="w-2.5 h-2.5 rounded-full inline-block bg-yellow-500"></span>Observasi</span>
            <span class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400"><span class="w-2.5 h-2.5 rounded-full inline-block bg-orange-500"></span>KTS Minor</span>
            <span class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400"><span class="w-2.5 h-2.5 rounded-full inline-block bg-red-500"></span>KTS Mayor</span>
            <span class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400"><span class="w-2.5 h-2.5 rounded-full inline-block bg-blue-500"></span>OFI</span>
        </div>

        <div class="relative h-56">
            <canvas id="chartKategoriPtk"></canvas>
            <div id="chartKategoriEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                <p class="text-xs text-gray-400 dark:text-gray-500 italic">Belum ada data PTK.</p>
            </div>
        </div>
    </div>
</div>
</section>
