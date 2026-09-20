<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SellerRequest;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SellerRequestController extends Controller
{
    /**
     * Tampilkan daftar request kategori & fitur dari seller ini.
     */
    public function index(Request $request): View
    {
        $seller = Auth::user()->seller;

        $query = SellerRequest::where('seller_id', $seller->id)
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $requests = $query->paginate(10)->withQueryString();
        $categories = Category::withCount('products')->latest()->get();

        return view('seller.requests.index', compact('requests', 'categories'));
    }

    /**
     * Form untuk mengirimkan request baru.
     */
    public function create(): View
    {
        return view('seller.requests.create');
    }

    /**
     * Simpan request baru dari seller.
     */
    public function store(Request $request): RedirectResponse
    {
        $seller = Auth::user()->seller;

        $data = $request->validate([
            'type'        => ['required', 'string', 'in:category,feature,other'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'title.required' => 'Nama kategori / judul request wajib diisi.',
            'title.max'      => 'Nama kategori terlalu panjang (maksimal 255 karakter).',
            'type.required'  => 'Tipe request wajib dipilih.',
        ]);

        $sellerRequest = SellerRequest::create([
            'seller_id'   => $seller->id,
            'type'        => $data['type'],
            'title'       => $data['title'],
            'description' => $data['description'] ?? '-',
            'status'      => 'pending',
            'is_read'     => false,
        ]);

        // Kirim Notifikasi WhatsApp ke Admin
        WhatsAppService::sendSellerRequestSubmittedNotification($sellerRequest);

        return redirect()->route('seller.seller-requests.index')
            ->with('success', 'Request Anda berhasil dikirim ke Admin! Anda dapat memantau balasan & statusnya di halaman ini.');
    }

    /**
     * Detail dari request tertentu.
     */
    public function show(SellerRequest $sellerRequest): View|RedirectResponse
    {
        $seller = Auth::user()->seller;

        if ($sellerRequest->seller_id !== $seller->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        return view('seller.requests.show', compact('sellerRequest'));
    }
}
