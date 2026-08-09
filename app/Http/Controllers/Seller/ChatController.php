<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatController extends Controller
{
    /**
     * Display chat list yang masuk ke seller yang login.
     */
    public function index(): View
    {
        $chats = Chat::with([
            'buyer',
            'seller.user',
            'product',
        ])
        ->where('seller_id', Auth::user()->seller->id)
        ->latest()
        ->paginate(10);

        return view('seller.chat.index', compact(
            'chats'
        ));
    }

    /**
     * Display chat detail.
     */
    public function show(Chat $chat): View
    {
        abort_unless($chat->seller_id === Auth::user()->seller->id, 403);

        $chat->load([
            'buyer',
            'seller.user',
            'product',
        ]);

        return view('seller.chat.show', compact(
            'chat'
        ));
    }

    // store() BELUM dibuat -- masih nunggu skema Chat/pesan
    // (kirim model & migration Chat, biar saya pastiin field-nya)
}