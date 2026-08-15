<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerController extends Controller
{
    /**
     * Display a listing of active sellers.
     */
    public function index(Request $request): View
    {
        $status = 'approved';

        $sellers = Seller::with('user')
            ->withCount('products')
            ->where('status', 'approved')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalActiveSellers = Seller::where('status', 'approved')->count();
        $totalPendingVerifications = Seller::where('status', 'pending')->count();

        return view('admin.sellers.index', compact('sellers', 'status', 'totalActiveSellers', 'totalPendingVerifications'));
    }

    /**
     * Display a listing of seller applications for verification.
     */
    public function verifications(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $sellers = Seller::with('user')
            ->whereIn('status', ['pending', 'revision', 'rejected'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'pending'  => Seller::where('status', 'pending')->count(),
            'revision' => Seller::where('status', 'revision')->count(),
            'rejected' => Seller::where('status', 'rejected')->count(),
            'all'      => Seller::whereIn('status', ['pending', 'revision', 'rejected'])->count(),
        ];

        return view('admin.sellers.verifications', compact('sellers', 'status', 'counts'));
    }

    /**
     * Show the form for creating a new seller (manual by admin).
     */
    public function create(): View
    {
        $users = User::all();

        return view('admin.sellers.create', compact('users'));
    }

    /**
     * Store a manually-created seller.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id'          => ['required', 'exists:users,id'],
            'whatsapp_number'  => ['nullable', 'string', 'max:20'],
            'description'      => ['nullable', 'string'],
        ]);

        Seller::create(array_merge($data, [
            'status'      => 'approved',
            'approved_at' => now(),
        ]));

        return redirect()
            ->route('admin.sellers.index')
            ->with('success', 'Seller baru berhasil ditambahkan.');
    }

    /**
     * Display the specified seller.
     */
    public function show(Seller $seller): View
    {
        $seller->load('user')->loadCount('products');

        return view('admin.sellers.show', compact('seller'));
    }

    /**
     * Show the form for editing the specified seller.
     */
    public function edit(Seller $seller): View
    {
        $seller->load('user');

        return view('admin.sellers.edit', compact('seller'));
    }

    /**
     * Update seller data.
     */
    public function update(Request $request, Seller $seller): RedirectResponse
    {
        $data = $request->validate([
            'status'           => ['required', 'in:pending,approved,rejected,revision'],
            'whatsapp_number'  => ['nullable', 'string', 'max:20'],
            'description'      => ['nullable', 'string'],
            'rejection_note'   => ['nullable', 'string'],
        ]);

        $updateData = [
            'status'          => $data['status'],
            'whatsapp_number' => $data['whatsapp_number'] ?? $seller->whatsapp_number,
            'description'     => $data['description'] ?? $seller->description,
            'rejection_note'  => $data['rejection_note'] ?? $seller->rejection_note,
        ];

        if ($data['status'] === 'approved' && ! $seller->approved_at) {
            $updateData['approved_at'] = now();
        }

        $seller->update($updateData);

        return redirect()
            ->route('admin.sellers.show', $seller)
            ->with('success', 'Data seller berhasil diperbarui.');
    }

    /**
     * Delete seller.
     */
    public function destroy(Seller $seller): RedirectResponse
    {
        $seller->delete();

        return redirect()
            ->route('admin.sellers.index')
            ->with('success', 'Seller berhasil dihapus.');
    }

    /**
     * Approve seller registration.
     */
    public function approve(Seller $seller): RedirectResponse
    {
        $seller->update([
            'status'         => 'approved',
            'approved_at'    => now(),
            'rejection_note' => null,
        ]);

        return redirect()
            ->route('admin.sellers.show', $seller)
            ->with('success', "Pengajuan {$seller->user->username} berhasil disetujui.");
    }

    /**
     * Reject seller registration with a reason.
     */
    public function reject(Request $request, Seller $seller): RedirectResponse
    {
        $request->validate([
            'rejection_note' => ['required', 'string', 'min:5'],
        ], [
            'rejection_note.required' => 'Alasan penolakan wajib diisi.',
            'rejection_note.min'      => 'Alasan terlalu singkat.',
        ]);

        $seller->update([
            'status'         => 'rejected',
            'rejection_note' => $request->rejection_note,
            'approved_at'    => null,
        ]);

        return redirect()
            ->route('admin.sellers.show', $seller)
            ->with('success', "Pengajuan {$seller->user->username} berhasil ditolak.");
    }

    /**
     * Request revision from seller.
     */
    public function requestRevision(Request $request, Seller $seller): RedirectResponse
    {
        $request->validate([
            'rejection_note' => ['required', 'string', 'min:5'],
        ], [
            'rejection_note.required' => 'Catatan revisi wajib diisi.',
            'rejection_note.min'      => 'Catatan terlalu singkat.',
        ]);

        $seller->update([
            'status'         => 'revision',
            'rejection_note' => $request->rejection_note,
        ]);

        return redirect()
            ->route('admin.sellers.show', $seller)
            ->with('success', "Permintaan revisi berhasil dikirim ke {$seller->user->username}.");
    }
}