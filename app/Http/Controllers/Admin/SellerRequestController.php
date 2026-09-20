<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\SellerRequest;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerRequestController extends Controller
{
    /**
     * Tampilkan daftar request dari seluruh seller.
     */
    public function index(Request $request): View
    {
        $query = SellerRequest::with('seller.user')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->get('unread') === '1') {
            $query->where('is_read', false);
        }

        $requests = $query->paginate(15)->withQueryString();

        $unreadCount = SellerRequest::where('is_read', false)->count();
        $pendingCount = SellerRequest::where('status', 'pending')->count();

        return view('admin.requests.index', compact('requests', 'unreadCount', 'pendingCount'));
    }

    /**
     * Detail request seller & otomatis ubah status dibaca (read_at).
     */
    public function show(SellerRequest $sellerRequest): View
    {
        $sellerRequest->load('seller.user');

        // Otomatis tandai sebagai sudah dibaca oleh admin jika belum dibaca
        if (! $sellerRequest->is_read) {
            $sellerRequest->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return view('admin.requests.show', compact('sellerRequest'));
    }

    /**
     * Konfirmasi / Setujui request dari seller.
     */
    public function confirm(Request $request, SellerRequest $sellerRequest): RedirectResponse
    {
        $data = $request->validate([
            'admin_response' => ['nullable', 'string', 'max:1000'],
        ]);

        $defaultResponse = $sellerRequest->type === 'category'
            ? "Kategori \"{$sellerRequest->title}\" telah ditambahkan ke sistem. Silakan gunakan kategori tersebut saat mengunggah atau mengedit produk Anda."
            : "Request Anda telah diproses dan disetujui oleh Admin.";

        $adminResponse = ! empty($data['admin_response']) ? $data['admin_response'] : $defaultResponse;

        $sellerRequest->update([
            'status'         => 'completed',
            'admin_response' => $adminResponse,
            'completed_at'   => now(),
        ]);

        $sellerRequest->loadMissing('seller.user');

        // Buat Notifikasi Aplikasi untuk Seller User
        if ($sellerRequest->seller?->user_id) {
            Notification::create([
                'user_id' => $sellerRequest->seller->user_id,
                'title'   => 'Request Disetujui & Diproses Admin 🎉',
                'message' => "Request Anda \"{$sellerRequest->title}\" telah disetujui. Balasan Admin: {$adminResponse}",
                'type'    => 'seller_request_completed',
                'is_read' => false,
            ]);
        }

        // Kirim Notifikasi WhatsApp ke Seller
        WhatsAppService::sendSellerRequestResponseNotification($sellerRequest);

        return redirect()->route('admin.seller-requests.show', $sellerRequest)
            ->with('success', 'Request seller berhasil dikonfirmasi & status diubah menjadi Selesai!');
    }

    /**
     * Menolak / Memberikan catatan penolakan atas request seller.
     */
    public function reject(Request $request, SellerRequest $sellerRequest): RedirectResponse
    {
        $data = $request->validate([
            'admin_response' => ['required', 'string', 'max:1000'],
        ], [
            'admin_response.required' => 'Catatan balasan/alasan wajib diisi saat menolak request.',
        ]);

        $sellerRequest->update([
            'status'         => 'rejected',
            'admin_response' => $data['admin_response'],
        ]);

        $sellerRequest->loadMissing('seller.user');

        // Buat Notifikasi Aplikasi untuk Seller User
        if ($sellerRequest->seller?->user_id) {
            Notification::create([
                'user_id' => $sellerRequest->seller->user_id,
                'title'   => 'Tanggapan Admin Atas Request ℹ️',
                'message' => "Admin telah meninjau request Anda \"{$sellerRequest->title}\". Catatan: {$data['admin_response']}",
                'type'    => 'seller_request_rejected',
                'is_read' => false,
            ]);
        }

        // Kirim Notifikasi WhatsApp ke Seller
        WhatsAppService::sendSellerRequestResponseNotification($sellerRequest);

        return redirect()->route('admin.seller-requests.show', $sellerRequest)
            ->with('info', 'Status request telah diperbarui menjadi Ditolak dengan balasan tersimpan.');
    }
}
