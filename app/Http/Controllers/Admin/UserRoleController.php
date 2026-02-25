<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')->latest();

        if ($request->filled('role')) {
            $query->role($request->role);
        }
        if ($request->filled('status')) {
            match ($request->status) {
                'active'  => $query->where('is_active', true)->where('is_blocked', false),
                'pending' => $query->where('is_active', false)->where('is_blocked', false),
                'blocked' => $query->where('is_blocked', true),
                default   => null,
            };
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
        }

        $users = $query->paginate(20)->appends($request->query());
        $roles = Role::query()->whereNotIn('name', \App\Models\Standard::pluck('code'))->orderBy('name')->pluck('name');

        // Stats (from ALL users, not paginated)
        $all = User::with('roles')->get();
        $stats = [
            'total'   => $all->count(),
            'active'  => $all->where('is_active', true)->count(),
            'pending' => $all->where('is_active', false)->where('is_blocked', false)->count(),
            'blocked' => $all->where('is_blocked', true)->count(),
            'admin'   => $all->filter(fn($u) => $u->hasRole('admin'))->count(),
        ];

        return view('admin.users.index', compact('users', 'roles', 'stats'));
    }

    public function create()
    {
        $roles = Role::query()->whereNotIn('name', \App\Models\Standard::pluck('code'))->orderBy('name')->pluck('name');
        $standards = \App\Models\Standard::orderByRaw('LENGTH(code), code')->get();

        return view('admin.users.create', compact('roles', 'standards'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'string', 'exists:roles,name'],
            'assigned_standard' => ['nullable', 'string', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            // Admin accounts are created active; others start inactive
            'is_active' => $data['role'] === 'admin',
        ]);

        $rolesToSync = [$data['role']];
        if ($data['role'] === 'standar' && !empty($data['assigned_standard'])) {
            $rolesToSync[] = $data['assigned_standard'];
        }
        $user->syncRoles($rolesToSync);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $roles = Role::query()->whereNotIn('name', \App\Models\Standard::pluck('code'))->orderBy('name')->pluck('name');
        $standards = \App\Models\Standard::orderByRaw('LENGTH(code), code')->get();
        // Get user's current roles that match standard codes
        $userAssignedStandard = $user->roles()->whereIn('name', $standards->pluck('code'))->pluck('name')->first();

        return view('admin.users.edit', compact('user', 'roles', 'standards', 'userAssignedStandard'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'string', 'exists:roles,name'],
            'assigned_standard' => ['nullable', 'string', 'exists:roles,name'],
        ]);

        $user->name  = $data['name'];
        $user->email = $data['email'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        $rolesToSync = [$data['role']];
        if ($data['role'] === 'standar' && !empty($data['assigned_standard'])) {
            $rolesToSync[] = $data['assigned_standard'];
        }
        $user->syncRoles($rolesToSync);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate.');
    }

    /**
     * Toggle the active status of a non-admin user.
     */
    public function toggleActive(User $user): RedirectResponse
    {
        // Prevent deactivating admin accounts
        if ($user->hasRole('admin')) {
            return back()->with('error', 'Akun admin tidak dapat dinonaktifkan.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        $label = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun {$user->name} berhasil {$label}.");
    }
    /**
     * Unblock a user account.
     */
    public function unblock(User $user): RedirectResponse
    {
        $user->update([
            'is_blocked' => false,
            'failed_login_attempts' => 0,
            'last_failed_login_at' => null,
        ]);

        return back()->with('success', "Akun {$user->name} berhasil dibuka blokirnya.");
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return back()->with('error', 'Tidak dapat menghapus admin terakhir.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} berhasil dihapus.");
    }
}
