<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        return view('profile.index');
    }

    public function edit(): View
    {
        return view('profile.edit');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');

            // delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $data['avatar'] = $path;
        }

        $user->update($data);

        return redirect()->route('profile.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
