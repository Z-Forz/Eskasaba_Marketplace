<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
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
        ->latest('last_message_at')
        ->paginate(10);

        return view('buyer.chats.index', compact('chats'));
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
            'messages.sender',
        ]);

        return view('buyer.chats.show', compact('chat'));
    }

    /**
     * Start a new chat (atau lanjut yang sudah ada) tentang produk.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $chat = Chat::firstOrCreate([
            'buyer_id'   => Auth::id(),
            'seller_id'  => $product->seller_id,
            'product_id' => $product->id,
        ], [
            'last_message'    => 'Chat dimulai mengenai produk: ' . $product->name,
            'last_message_at' => now(),
        ]);

        return redirect()->route('buyer.chats.show', $chat);
    }

    /**
     * Send message in chat.
     */
    public function storeMessage(Request $request, Chat $chat): RedirectResponse
    {
        abort_unless($chat->buyer_id === Auth::id(), 403);

        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        ChatMessage::create([
            'chat_id'   => $chat->id,
            'sender_id' => Auth::id(),
            'message'   => $request->message,
        ]);

        $chat->update([
            'last_message'    => $request->message,
            'last_message_at' => now(),
        ]);

        return back()->with('success', 'Pesan terkirim.');
    }
}