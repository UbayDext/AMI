<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl">Admin - Create User</h2>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border rounded">Kembali</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 rounded">
                <b>Validasi gagal:</b>
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded shadow p-6">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block mb-1">Nama</label>
                    <input name="name" value="{{ old('name') }}" class="w-full border rounded p-2" required>
                    @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded p-2" required>
                    @error('email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1">Password</label>
                        <input type="password" name="password" class="w-full border rounded p-2" required>
                        @error('password')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block mb-1">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="w-full border rounded p-2" required>
                    </div>
                </div>

                <div>
                    <label class="block mb-1">Role</label>
                    <select name="role" class="w-full border rounded p-2" required>
                        <option value="">- pilih -</option>
                        @foreach($roles as $r)
                            <option value="{{ $r }}" @selected(old('role')===$r)>{{ $r }}</option>
                        @endforeach
                    </select>
                    @error('role')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>

                <button class="px-4 py-2 bg-black text-white rounded">
                    Simpan
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
