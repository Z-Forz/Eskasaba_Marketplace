<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Services\ImageCompressor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display seller store profile & QRIS settings form.
     */
    public function edit(): View
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();

        return view('seller.profile.edit', compact('seller'));
    }

    /**
     * Update seller store profile & QRIS barcode.
     */
    public function update(Request $request): RedirectResponse
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();

        $data = $request->validate([
            'whatsapp_number' => ['required', 'string', 'max:20'],
            'description'     => ['nullable', 'string', 'max:1000'],
            'qris_image'      => ['nullable', 'image', 'max:10240'],
        ], [
            'whatsapp_number.required' => 'Nomor WhatsApp wajib diisi.',
            'qris_image.image'         => 'File QRIS harus berupa gambar.',
        ]);

        if ($request->hasFile('qris_image')) {
            if ($seller->qris_image) {
                Storage::disk('public')->delete($seller->qris_image);
            }
            $data['qris_image'] = ImageCompressor::compressAndStore($request->file('qris_image'), 'qris');
        }

        $seller->update($data);

        // Synchronize with user profile phone number
        if ($seller->user && isset($data['whatsapp_number'])) {
            $seller->user->update([
                'phone' => $data['whatsapp_number'],
            ]);
        }

        return back()->with('success', 'Profil toko & Barcode QRIS berhasil diperbarui.');
    }
}
