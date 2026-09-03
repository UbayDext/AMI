<script>
document.addEventListener('alpine:init', () => Alpine.data('preparationsOnboarding', () => ({
    open: @js($preparationsOnboarding->status === 'started'),
    step: @js((int) $preparationsOnboarding->current_step),
    rect: null,
    panelStyle: 'position:fixed;z-index:10001;left:16px;top:16px;width:min(440px,calc(100% - 32px))',
    steps: [
        {target:'intro', title:'Evidence Standar', text:'Selamat datang di ruang kerja evidence auditee.', func:'Di sini Anda mengelola bukti untuk standar yang ditugaskan ke akun Anda.'},
        @if($standards->isNotEmpty())
        {target:'standard-list', title:'Standar yang Ditugaskan', text:'Setiap kartu mewakili satu standar yang dapat Anda akses.', func:'Pilih kartu untuk membuka tahap, task, progres, dan dokumen bersama auditee lain pada standar yang sama.'},
        {target:'standard-list', title:'Mulai Mengelola Evidence', text:'Buka salah satu kartu, lalu pilih prodi tujuan.', func:'Anda dapat membuat tahap dan task sendiri, mengunggah dokumen, serta melihat evidence rekan satu standar.'},
        @else
        {target:'empty', title:'Belum Ada Penugasan', text:'Akun Anda belum memiliki standar.', func:'Gunakan tombol Ajukan Standar agar admin dapat memberikan akses.'},
        @endif
        {target:'intro', title:'Panduan Selesai', text:'Panduan dapat dibuka kembali kapan saja.', func:'Klik tombol Lihat Panduan di bagian atas halaman untuk mengulang tur.'}
    ],
    init() {
        if (this.step >= this.steps.length) this.step = 0;
        this.$watch('step', () => this.position());
        addEventListener('resize', () => this.position());
        addEventListener('scroll', () => this.position(), true);
        addEventListener('restart-preparations-onboarding', () => this.restart());
        if (this.open) this.$nextTick(() => this.position());
    },
    position() {
        if (!this.open) return;
        const target = document.querySelector(`[data-onboarding-preparations="${this.steps[this.step].target}"]`);
        if (!target) { this.rect = null; return; }
        target.scrollIntoView({behavior:'smooth', block:'center'});
        setTimeout(() => {
            const r = target.getBoundingClientRect();
            this.rect = {top:r.top-6, left:r.left-6, width:r.width+12, height:r.height+12};
            this.place(r);
        }, 180);
    },
    place(target) {
        const gap=18, margin=16, width=Math.min(440, innerWidth-margin*2), height=Math.min(390, innerHeight-margin*2);
        let left, top;
        if (innerWidth < 640) { left=margin; top=Math.max(margin, innerHeight-height-margin); }
        else if (target.right+gap+width <= innerWidth-margin) { left=target.right+gap; top=Math.min(Math.max(margin,target.top+target.height/2-height/2),innerHeight-height-margin); }
        else if (target.left-gap-width >= margin) { left=target.left-gap-width; top=Math.min(Math.max(margin,target.top+target.height/2-height/2),innerHeight-height-margin); }
        else { left=Math.min(Math.max(margin,target.left),innerWidth-width-margin); top=Math.max(margin,target.top-gap-height); }
        this.panelStyle=`position:fixed;z-index:10001;left:${left}px;top:${top}px;width:${width}px;max-height:${height}px;overflow-y:auto`;
    },
    async save(status) {
        await fetch(@js(route('onboarding.internal-preparations.progress')), {method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':@js(csrf_token())}, body:JSON.stringify({current_step:this.step,status})});
    },
    next() { if (this.step === this.steps.length-1) { this.save('completed'); this.open=false; } else { this.step++; this.save('started'); } },
    back() { if (this.step > 0) { this.step--; this.save('started'); } },
    skip() { if (confirm('Lewati panduan Evidence Standar? Panduan dapat dibuka kembali.')) { this.save('skipped'); this.open=false; } },
    async restart() { await fetch(@js(route('onboarding.internal-preparations.restart')), {method:'POST',headers:{'Accept':'application/json','X-CSRF-TOKEN':@js(csrf_token())}}); this.step=0; this.open=true; this.$nextTick(()=>this.position()); }
})))
</script>

<div x-data="preparationsOnboarding" x-show="open" x-cloak style="position:fixed;inset:0;z-index:9999" role="dialog" aria-modal="true" aria-label="Panduan Evidence Standar">
    <div x-show="!rect" style="position:absolute;inset:0;background:rgba(2,6,23,.72)"></div>
    <template x-if="rect"><div class="pointer-events-none">
        <div :style="`position:fixed;left:0;right:0;top:0;height:${Math.max(0,rect.top)}px;background:rgba(2,6,23,.72)`"></div>
        <div :style="`position:fixed;left:0;top:${rect.top}px;width:${Math.max(0,rect.left)}px;height:${rect.height}px;background:rgba(2,6,23,.72)`"></div>
        <div :style="`position:fixed;left:${rect.left+rect.width}px;right:0;top:${rect.top}px;height:${rect.height}px;background:rgba(2,6,23,.72)`"></div>
        <div :style="`position:fixed;left:0;right:0;top:${rect.top+rect.height}px;bottom:0;background:rgba(2,6,23,.72)`"></div>
    </div></template>
    <div x-show="rect" class="pointer-events-none rounded-xl border-4 border-indigo-400" :style="`position:fixed;z-index:10000;top:${rect?.top}px;left:${rect?.left}px;width:${rect?.width}px;height:${rect?.height}px`"></div>
    <div class="rounded-2xl bg-white p-5 text-slate-900 shadow-2xl dark:bg-slate-800 dark:text-white" :style="panelStyle">
        <div class="mb-3 flex justify-between"><span class="text-xs font-bold uppercase text-indigo-500" x-text="`Langkah ${step+1} dari ${steps.length}`"></span><span class="text-xs text-slate-400">Panduan Auditee</span></div>
        <h3 class="text-lg font-bold" x-text="steps[step].title"></h3>
        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300" x-text="steps[step].text"></p>
        <div class="mt-3 rounded-xl border p-3 text-sm" style="border-color:rgba(99,102,241,.38);background:rgba(79,70,229,.14);color:inherit"><b style="color:#818cf8">Fungsi:</b> <span x-text="steps[step].func"></span></div>
        <div class="mt-5 h-1.5 rounded-full bg-slate-200 dark:bg-slate-700"><div class="h-full rounded-full bg-indigo-600" :style="`width:${((step+1)/steps.length)*100}%`"></div></div>
        <div class="mt-5 gap-2" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr))"><button @click="skip" class="h-10 rounded-xl border text-xs font-bold" style="border-color:rgba(251,113,133,.55);color:#fb7185">Lewati</button><button @click="back" :disabled="step===0" class="h-10 rounded-xl text-sm font-bold disabled:opacity-40" style="background:rgba(100,116,139,.35);color:inherit">Kembali</button><button @click="next" class="h-10 rounded-xl bg-indigo-600 text-sm font-bold text-white" x-text="step===steps.length-1?'Selesai':'Lanjut'"></button></div>
    </div>
</div>
