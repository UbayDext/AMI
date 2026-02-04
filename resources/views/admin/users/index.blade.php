<x-app-layout>
    <x-slot name="header">
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl">Admin - Users</h2>

        <a href="{{ route('admin.users.create') }}"
           class="px-4 py-2 bg-black text-white rounded">
            Create User
        </a>
    </div>
</x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left">Nama</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Role</th>
                        <th class="p-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr class="border-t">
                            <td class="p-3">{{ $u->name }}</td>
                            <td class="p-3">{{ $u->email }}</td>
                            <td class="p-3">{{ $u->getRoleNames()->join(', ') ?: '-' }}</td>
                            <td class="p-3">
                                <form method="POST" action="{{ route('admin.users.update', $u) }}" class="flex gap-2 items-center">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" class="border rounded p-2">
                                        <option value="admin" @selected($u->hasRole('admin'))>admin</option>
                                        <option value="asesor" @selected($u->hasRole('asesor'))>asesor</option>
                                    </select>
                                    <button class="px-3 py-2 bg-black text-white rounded">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</x-app-layout>
