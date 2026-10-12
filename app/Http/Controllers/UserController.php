<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('profile');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhereHas('profile', function ($p) use ($search) {
                      $p->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        $users = $query->orderBy('username')->paginate(10)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'full_name' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'staff'])],
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            UserProfile::create([
                'user_id' => $user->id,
                'full_name' => $validated['full_name'],
            ]);
        });

        return redirect()->route('users.index')
            ->with('success', "Pengguna '{$validated['username']}' berhasil didaftarkan.");
    }

    public function edit(User $user): View
    {
        $user->load('profile');
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'full_name' => ['required', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'staff'])],
        ]);

        DB::transaction(function () use ($validated, $user) {
            $updateData = [
                'username' => $validated['username'],
                'role' => $validated['role'],
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                ['full_name' => $validated['full_name']]
            );
        });

        return redirect()->route('users.index')
            ->with('success', "Data pengguna '{$user->username}' berhasil diperbarui.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        if ($user->stockMovements()->exists()) {
            return back()->with('error', "Pengguna '{$user->username}' tidak dapat dihapus karena tercatat dalam histori mutasi stok (Audit Trail).");
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Pengguna '{$user->username}' berhasil dihapus.");
    }
}
