<x-app-layout>
    <x-slot name="header">
        <div><div class="mb-1 text-xs font-medium text-slate-400">Akun / <span class="text-blue-600 dark:text-blue-400">Profil</span></div><h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Profil Saya</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola informasi pribadi dan keamanan akun Anda.</p></div>
    </x-slot>

    <div class="min-h-screen bg-slate-50/70 py-8 dark:bg-slate-950/30">
        <div class="mx-auto grid w-full max-w-6xl grid-cols-1 items-start gap-6 px-4 sm:px-6 lg:grid-cols-12 lg:px-8">
            <div class="space-y-6 lg:col-span-8">
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <div class="border-t-4 border-sky-500 px-5 py-5 sm:px-7"><h3 class="text-lg font-bold text-slate-900 dark:text-white">Informasi & Verifikasi Email</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Perbarui nama akun dan alamat email yang digunakan.</p></div>
                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-5 border-t border-slate-100 px-5 py-6 sm:px-7 dark:border-slate-700">
                        @csrf @method('PATCH')
                        <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Nama Lengkap <span class="text-rose-500">*</span></span><input name="name" value="{{ old('name', $user->name) }}" required autocomplete="name" class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-slate-600 dark:bg-slate-900 dark:text-white"><x-input-error class="mt-2" :messages="$errors->get('name')" /></label>
                        <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Email Pribadi <span class="text-rose-500">*</span></span><input name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200"><x-input-error class="mt-2" :messages="$errors->get('email')" /></label>
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            @if($user->email_verified_at)<span class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-600 dark:text-emerald-400"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Email sudah diverifikasi</span>@else<span class="text-sm font-medium text-amber-600 dark:text-amber-400">Email belum diverifikasi</span>@endif
                            <button type="submit" class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/20">Simpan Profil</button>
                        </div>
                        @if(session('status') === 'profile-updated')<p class="text-sm font-medium text-emerald-600">Perubahan profil berhasil disimpan.</p>@endif
                    </form>
                </section>

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <div class="border-t-4 border-amber-500 px-5 py-5 sm:px-7"><h3 class="text-lg font-bold text-slate-900 dark:text-white">Keamanan Akun</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Gunakan kata sandi panjang dan unik untuk menjaga akun.</p></div>
                    <form method="POST" action="{{ route('password.update') }}" class="space-y-5 border-t border-slate-100 px-5 py-6 sm:px-7 dark:border-slate-700">
                        @csrf @method('PUT')
                        <div class="grid gap-5 md:grid-cols-2">
                            <label class="block md:col-span-2"><span class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Kata Sandi Saat Ini</span><input name="current_password" type="password" autocomplete="current-password" class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-white"><x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" /></label>
                            <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Kata Sandi Baru</span><input name="password" type="password" autocomplete="new-password" class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-white"><x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" /></label>
                            <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Konfirmasi Kata Sandi</span><input name="password_confirmation" type="password" autocomplete="new-password" class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-white"><x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" /></label>
                        </div>
                        <div class="flex items-center justify-between gap-3"><p class="text-xs text-slate-400">Disarankan mengganti kata sandi secara berkala.</p><button class="rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-600">Ganti Kata Sandi</button></div>
                        @if(session('status') === 'password-updated')<p class="text-sm font-medium text-emerald-600">Kata sandi berhasil diperbarui.</p>@endif
                    </form>
                </section>

                <section class="rounded-2xl border border-rose-200 bg-white p-5 shadow-sm dark:border-rose-900/50 dark:bg-slate-800 sm:p-7">@include('profile.partials.delete-user-form')</section>
            </div>

            <aside class="lg:sticky lg:top-6 lg:col-span-4">
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <div class="border-t-4 border-sky-500 px-5 py-5 text-center"><h3 class="text-lg font-bold text-slate-900 dark:text-white">Foto Profil</h3><p class="mt-1 text-xs text-slate-400">JPG, PNG, atau WebP · maksimal 2 MB</p></div>
                    <div class="border-t border-slate-100 px-5 py-7 text-center dark:border-slate-700">
                        <div class="mx-auto flex h-40 w-40 items-center justify-center overflow-hidden rounded-full border-4 border-white bg-gradient-to-br from-blue-100 to-indigo-200 text-4xl font-extrabold text-blue-700 shadow-xl ring-1 ring-slate-200 dark:border-slate-800 dark:from-slate-700 dark:to-slate-600 dark:text-blue-300 dark:ring-slate-600">
                            @if($user->avatar_path)<img src="{{ Storage::url($user->avatar_path) }}" alt="Foto {{ $user->name }}" class="h-full w-full object-cover">@else{{ strtoupper(substr($user->name, 0, 2)) }}@endif
                        </div>
                        <h4 class="mt-5 text-base font-bold text-slate-900 dark:text-white">{{ $user->name }}</h4><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $user->roles->pluck('name')->join(', ') ?: 'User' }}</p>
                        <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" class="mt-6" x-data="{ fileName: '' }">
                            @csrf
                            <label class="block cursor-pointer rounded-xl border-2 border-dashed border-slate-300 p-4 transition hover:border-blue-400 hover:bg-blue-50/50 dark:border-slate-600 dark:hover:border-blue-500 dark:hover:bg-blue-900/10"><input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" required class="sr-only" @change="fileName = $event.target.files[0]?.name || ''"><span class="text-sm font-semibold text-blue-600 dark:text-blue-400" x-text="fileName || '{{ $user->avatar_path ? 'Pilih foto pengganti' : 'Pilih foto profil' }}'"></span></label>
                            <x-input-error :messages="$errors->get('avatar')" class="mt-2 text-left" />
                            <button type="submit" class="mt-3 w-full rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">{{ $user->avatar_path ? 'Ganti Foto' : 'Unggah Foto' }}</button>
                        </form>
                        @if($user->avatar_path)<form method="POST" action="{{ route('profile.avatar.destroy') }}" class="mt-2">@csrf @method('DELETE')<button type="submit" onclick="return confirm('Hapus foto profil?')" class="w-full rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-900 dark:text-rose-400 dark:hover:bg-rose-900/20">Hapus Foto</button></form>@endif
                        @if(in_array(session('status'), ['avatar-updated', 'avatar-deleted']))<p class="mt-3 text-sm font-medium text-emerald-600">Foto profil berhasil diperbarui.</p>@endif
                    </div>
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>
