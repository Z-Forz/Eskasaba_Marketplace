<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $users = User::when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
                ->orWhere('nis_nip', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('class', 'like', "%{$search}%")
                ->orWhere('major', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            $data['password'] = Hash::make('password');
            $data['is_default_password'] = true;
        }

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = array_filter($request->validated());

        if (isset($data['password']) && ! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
            $data['is_default_password'] = false;
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', "Data pengguna {$user->username} berhasil diperbarui.");
    }

    /**
     * Reset password user secara langsung oleh admin jika lupa kata sandi.
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'new_password' => ['required', 'string', 'min:6'],
        ], [
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min'      => 'Password baru minimal 6 karakter.',
        ]);

        $newPass = $request->input('new_password');

        $user->update([
            'password'            => Hash::make($newPass),
            'is_default_password' => false,
        ]);

        \App\Models\ActivityLog::record(
            $user->id,
            'password_reset_admin',
            "Password direset oleh Admin sekolah (IP: {$request->ip()})",
            $request,
            \Illuminate\Support\Facades\Auth::guard('admin')->id()
        );

        return redirect()->back()
            ->with('success', "Password untuk user {$user->username} ({$user->nis_nip}) berhasil direset menjadi '{$newPass}'. Silakan sampaikan password baru ini kepada user.");
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Sinkronisasi data pengguna dari Database/API Sekolah.
     */
    public function sync(Request $request, \App\Services\SchoolApiService $schoolApi): RedirectResponse
    {
        $count = $schoolApi->syncAllUsers();

        return redirect()->route('admin.users.index')
            ->with('success', "Sinkronisasi berhasil! {$count} data pengguna telah diperbarui dari Database Sekolah.");
    }
}
