<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(): View
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
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
        User::create($request->validated());
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
        $user->update($request->validated());
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
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
}
