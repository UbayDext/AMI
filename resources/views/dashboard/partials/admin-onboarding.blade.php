<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('adminOnboarding', () => ({
        open: @js($adminOnboarding?->status === 'started'),
        step: @js((int) ($adminOnboarding?->current_step ?? 0)),
        rect: null,
        panelStyle: 'position:fixed;z-index:10001;left:50%;bottom:24px;transform:translateX(-50%);width:calc(100% - 32px);max-width:440px',
        steps: [
            { target: 'welcome', title: 'Selamat Datang, Admin', text: 'Dashboard menampilkan ringkasan kegiatan Audit Mutu Internal.', func: 'Memberikan gambaran cepat kondisi audit, temuan, dan tindak lanjut mutu.' },
            { target: 'dashboard-menu', title: 'Dashboard', text: 'Kembali ke ringkasan sistem dari halaman mana pun.', func: 'Menjadi halaman pusat untuk memantau seluruh aktivitas AMI.' },
            { target: 'ami-menu', title: 'Audit & AMI', text: 'Kelola assessment, siklus AMI, penugasan, dan proses audit.', func: 'Membuka proses utama audit mulai dari pembuatan siklus sampai pemeriksaan auditor.' },
            { target: 'assessment-submenu', title: 'Kelola Assessment', text: 'Buat dan kelola assessment mutu yang akan dikerjakan auditor.', func: 'Menentukan unit, periode, auditor, serta pertanyaan assessment.' },
            { target: 'cycle-submenu', title: 'Siklus AMI', text: 'Atur periode pelaksanaan Audit Mutu Internal.', func: 'Membuat siklus, menentukan auditee, standar, dan penugasan reviewer.' },
            { target: 'document-menu', title: 'Dokumen', text: 'Kelola evidence standar, FOSK, dan dokumen persiapan.', func: 'Menyimpan dan menata dokumen yang diperlukan sebagai bukti audit.' },
            { target: 'evidence-submenu', title: 'Evidence Standar', text: 'Kelola kebutuhan evidence untuk setiap standar.', func: 'Menyusun tahapan, tugas, dan dokumen bukti yang harus disiapkan.' },
            { target: 'fosk-submenu', title: 'FOSK Akreditasi', text: 'Kelola dokumen berdasarkan kriteria akreditasi.', func: 'Mengelompokkan dokumen dan evidence untuk kebutuhan akreditasi.' },
            { target: 'reference-menu', title: 'Referensi', text: 'Kelola bank pertanyaan, Bank Bidang, prodi, dan referensi audit.', func: 'Menyiapkan data acuan yang digunakan saat assessment dan AMI.' },
            { target: 'question-bank-submenu', title: 'Bank Soal Assessment', text: 'Kelola pertanyaan untuk assessment umum.', func: 'Menjadi sumber pertanyaan yang dipakai pada proses assessment.' },
            { target: 'ami-question-submenu', title: 'Pertanyaan AMI', text: 'Kelola pertanyaan AMI berdasarkan standar dan bidang.', func: 'Mengunduh template, mengimpor pertanyaan, dan mengatur Bank Bidang.' },
            { target: 'prodi-submenu', title: 'Program Studi', text: 'Kelola referensi program studi.', func: 'Membatasi pertanyaan dan pelaksanaan AMI pada program studi tertentu.' },
            { target: 'year-submenu', title: 'Tahun Akreditasi', text: 'Kelola periode tahun akreditasi.', func: 'Menjadi acuan waktu untuk assessment dan laporan mutu.' },
            { target: 'decree-submenu', title: 'SK Auditor', text: 'Kelola dokumen keputusan penugasan auditor.', func: 'Mencatat dasar resmi penugasan auditor pada kegiatan audit.' },
            { target: 'user-menu', title: 'Pengguna', text: 'Kelola pengguna, peran, dan pengajuan akses standar.', func: 'Mengatur siapa yang menjadi admin, auditor, atau auditee beserta hak aksesnya.' },
            { target: 'users-submenu', title: 'Kelola Pengguna', text: 'Atur akun, status, dan peran pengguna.', func: 'Mengaktifkan, memblokir, dan memberikan hak akses sesuai tanggung jawab.' },
            { target: 'role-request-submenu', title: 'Pengajuan Standar', text: 'Periksa permintaan akses standar dari pengguna.', func: 'Menyetujui atau menolak akses auditee terhadap standar tertentu.' },
            { target: 'dashboard-filters', title: 'Filter Dashboard', text: 'Saring ringkasan berdasarkan tahun dan unit.', func: 'Memusatkan laporan pada periode atau program studi yang ingin diperiksa.' },
            { target: 'summary-cards', title: 'Ringkasan Utama', text: 'Pantau assessment, temuan, dan tindak lanjut dalam satu tampilan.', func: 'Menampilkan indikator utama agar admin cepat mengetahui progres audit.' },
            { target: 'recent-activity', title: 'Aktivitas Terbaru', text: 'Lihat assessment yang terakhir dibuat atau diperbarui.', func: 'Memudahkan admin melanjutkan pekerjaan terbaru tanpa mencari ulang.' },
            { target: 'welcome', title: 'Panduan Selesai', text: 'Klik Lihat Panduan kapan saja untuk mengulang tur ini.', func: 'Panduan tersimpan untuk akun ini dan tidak muncul otomatis setelah selesai.' },
        ],
        init() {
            this.$watch('step', () => this.position());
            window.addEventListener('resize', () => this.position());
            window.addEventListener('scroll', () => this.position(), true);
            window.addEventListener('restart-admin-onboarding', () => this.restart());
            if (this.open) this.$nextTick(() => this.position());
        },
        position() {
            if (!this.open) return;
            document.querySelectorAll('[data-onboarding-active]').forEach(node => node.removeAttribute('data-onboarding-active'));
            const el = document.querySelector(`[data-onboarding="${this.steps[this.step].target}"]`);
            if (!el) { this.rect = null; return; }
            const menu = el.closest('[data-onboarding$="-menu"]');
            el.setAttribute('data-onboarding-active', 'true');
            window.dispatchEvent(new CustomEvent('onboarding-open-menu', { detail: menu?.dataset.onboarding || null }));
            setTimeout(() => {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                const r = el.getBoundingClientRect();
                this.rect = { top:r.top-6, left:r.left-6, width:r.width+12, height:r.height+12 };
                this.placePanel(r);
            }, 180);
        },
        placePanel(target) {
            const gap = 18, margin = 16;
            const panelWidth = Math.min(440, window.innerWidth - (margin * 2));
            const panelHeight = Math.min(390, window.innerHeight - (margin * 2));
            let left, top;

            if (window.innerWidth < 640) {
                left = margin;
                top = Math.max(margin, window.innerHeight - panelHeight - margin);
            } else if (target.right + gap + panelWidth <= window.innerWidth - margin) {
                left = target.right + gap;
                top = Math.min(Math.max(margin, target.top + (target.height / 2) - (panelHeight / 2)), window.innerHeight - panelHeight - margin);
            } else if (target.left - gap - panelWidth >= margin) {
                left = target.left - gap - panelWidth;
                top = Math.min(Math.max(margin, target.top + (target.height / 2) - (panelHeight / 2)), window.innerHeight - panelHeight - margin);
            } else if (target.bottom + gap + panelHeight <= window.innerHeight - margin) {
                left = Math.min(Math.max(margin, target.left), window.innerWidth - panelWidth - margin);
                top = target.bottom + gap;
            } else {
                left = Math.min(Math.max(margin, target.left), window.innerWidth - panelWidth - margin);
                top = Math.max(margin, target.top - gap - panelHeight);
            }

            this.panelStyle = `position:fixed;z-index:10001;left:${left}px;top:${top}px;width:${panelWidth}px;max-height:${panelHeight}px;overflow-y:auto`;
        },
        async save(status) {
            await fetch(@js(route('onboarding.dashboard-admin.progress')), { method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':@js(csrf_token())}, body:JSON.stringify({current_step:this.step,status}) });
        },
        closeMenus() { document.querySelectorAll('[data-onboarding-active]').forEach(node => node.removeAttribute('data-onboarding-active')); window.dispatchEvent(new CustomEvent('onboarding-open-menu', {detail:null})); },
        next() { if (this.step === this.steps.length-1) { this.save('completed'); this.closeMenus(); this.open=false; return; } this.step++; this.save('started'); },
        back() { if (this.step > 0) { this.step--; this.save('started'); } },
        skip() { if (window.confirm('Lewati panduan ini? Anda tetap dapat membukanya kembali melalui tombol Lihat Panduan.')) { this.save('skipped'); this.closeMenus(); this.open=false; } },
        async restart() { await fetch(@js(route('onboarding.dashboard-admin.restart')), {method:'POST',headers:{'Accept':'application/json','X-CSRF-TOKEN':@js(csrf_token())}}); this.step=0; this.open=true; this.$nextTick(() => this.position()); },
    }));
});
</script>

<div x-data="adminOnboarding" x-show="open" x-cloak style="position:fixed;inset:0;z-index:9999" role="dialog" aria-modal="true" aria-label="Panduan Dashboard Admin">
    <div x-show="!rect" style="position:absolute;inset:0;background:rgba(2,6,23,.72)"></div>
    <template x-if="rect">
        <div class="pointer-events-none">
            <div :style="`position:fixed;z-index:9999;left:0;right:0;top:0;height:${Math.max(0,rect.top)}px;background:rgba(2,6,23,.72)`"></div>
            <div :style="`position:fixed;z-index:9999;left:0;top:${rect.top}px;width:${Math.max(0,rect.left)}px;height:${rect.height}px;background:rgba(2,6,23,.72)`"></div>
            <div :style="`position:fixed;z-index:9999;left:${rect.left+rect.width}px;right:0;top:${rect.top}px;height:${rect.height}px;background:rgba(2,6,23,.72)`"></div>
            <div :style="`position:fixed;z-index:9999;left:0;right:0;top:${rect.top+rect.height}px;bottom:0;background:rgba(2,6,23,.72)`"></div>
        </div>
    </template>
    <div x-show="rect" class="pointer-events-none rounded-xl border-4 border-blue-400 transition-all" :style="`position:fixed;z-index:10000;top:${rect?.top}px;left:${rect?.left}px;width:${rect?.width}px;height:${rect?.height}px`"></div>
    <div class="rounded-2xl bg-white p-5 text-slate-900 shadow-2xl dark:bg-slate-800 dark:text-white" :style="panelStyle">
        <div class="mb-3 flex items-center justify-between"><span class="text-xs font-bold uppercase tracking-wide text-blue-600" x-text="`Langkah ${step+1} dari ${steps.length}`"></span><span class="text-xs font-semibold text-slate-400">Panduan Admin</span></div>
        <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="steps[step].title"></h3>
        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300" x-text="steps[step].text"></p>
        <div class="mt-3 rounded-xl border p-3 text-sm" style="border-color:rgba(59,130,246,.38);background:rgba(37,99,235,.14);color:inherit"><span class="font-bold" style="color:#60a5fa">Fungsi:</span> <span x-text="steps[step].func"></span></div>
        <div class="mt-5 h-1.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700"><div class="h-full rounded-full bg-blue-600 transition-all" :style="`width:${((step+1)/steps.length)*100}%`"></div></div>
        <div class="mt-5 grid grid-cols-3 gap-2"><button type="button" @click="skip" class="h-10 rounded-xl border border-rose-200 text-xs font-bold text-rose-600 hover:bg-rose-50">Lewati Panduan</button><button type="button" @click="back" :disabled="step===0" class="h-10 rounded-xl bg-slate-100 text-sm font-bold disabled:opacity-40 dark:bg-slate-700">Kembali</button><button type="button" @click="next" class="h-10 rounded-xl bg-blue-600 text-sm font-bold text-white" x-text="step===steps.length-1 ? 'Selesai' : 'Lanjut'"></button></div>
    </div>
</div>
