<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatController extends Controller
{
    /**
     * Display chat list milik buyer yang login.
     */
    public function index(): View
    {
        $chats = Chat::with([
            'buyer',
            'seller.user',
            'product',
        ])
        ->where('buyer_id', Auth::id())
        ->latest()
        ->paginate(10);

        return view('buyer.chat.index', compact(
            'chats'
        ));
    }

    /**
     * Display chat detail.
     */
    public function show(Chat $chat): View
    {
        abort_unless($chat->buyer_id === Auth::id(), 403);

        $chat->load([
            'buyer',
            'seller.user',
            'product',
        ]);

        return view('buyer.chat.show', compact(
            'chat'
        ));
    }

    /**
     * Start a new chat (atau lanjut yang udah ada) soal 1 produk.
     */
    public function store(
        Request $request,
        Product $product
    ): RedirectResponse {

        $chat = Chat::firstOrCreate([
            'buyer_id'   => Auth::id(),
            'seller_id'  => $product->seller_id,
            'product_id' => $product->id,
        ]);

        return redirect()->route('buyer.chats.show', $chat);
    }
}