<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Services\ImageCompressor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SellerApplicationController extends Controller
{
    /**
     * Tampilkan form pengajuan menjadi seller.
     */
    public function create(): View|RedirectResponse
    {
        $user   = Auth::user();
        $seller = $user->seller;

        // Sudah approved → langsung ke seller panel
        if ($seller?->isApproved()) {
            return redirect()->route('seller.dashboard')
                ->with('info', 'Anda sudah menjadi seller aktif.');
        }

        // Sudah pending → tidak bisa ajukan lagi
        if ($seller?->isPending()) {
            return redirect()->route('profile.index')
                ->with('info', 'Pengajuan Anda sedang diproses oleh admin.');
        }

        return view('profile.apply-seller', compact('seller'));
    }

    /**
     * Simpan pengajuan baru (pertama kali atau setelah revisi).
     */
    public function store(Request $request): RedirectResponse
    {
        $user   = Auth::user();
        $seller = $user->seller;

        $data = $request->validate([
            'whatsapp_number' => ['required', 'string', 'max:20'],
            'reason'          => ['required', 'string', 'min:10', 'max:1000'],
            'qris_image'      => ['nullable', 'image', 'max:10240'],
        ], [
            'whatsapp_number.required' => 'Nomor WhatsApp wajib diisi.',
            'reason.required'          => 'Alasan wajib diisi.',
            'reason.min'               => 'Alasan terlalu singkat, minimal 10 karakter.',
            'qris_image.image'         => 'File QRIS harus berupa gambar.',
        ]);

        if ($request->hasFile('qris_image')) {
            if ($seller?->qris_image) {
                Storage::disk('public')->delete($seller->qris_image);
            }
            $data['qris_image'] = ImageCompressor::compressAndStore($request->file('qris_image'), 'qris');
        }

        if ($seller) {
            // Sudah pernah ada record (revision / rejected) → update & set pending
            if (! $seller->needsRevision() && ! $seller->isRejected()) {
                return redirect()->route('profile.index')
                    ->with('error', 'Pengajuan Anda sedang diproses.');
            }

            $updateData = [
                'whatsapp_number' => $data['whatsapp_number'],
                'reason'          => $data['reason'],
                'status'          => 'pending',
                'rejection_note'  => null,
            ];

            if (isset($data['qris_image'])) {
                $updateData['qris_image'] = $data['qris_image'];
            }

            $seller->update($updateData);
        } else {
            // Pengajuan pertama kali
            Seller::create([
                'user_id'         => $user->id,
                'whatsapp_number' => $data['whatsapp_number'],
                'reason'          => $data['reason'],
                'qris_image'      => $data['qris_image'] ?? null,
                'status'          => 'pending',
            ]);
        }

        return redirect()->route('profile.index')
            ->with('success', 'Pengajuan berhasil dikirim! Admin akan memverifikasi dalam 1×24 jam.');
    }
}
