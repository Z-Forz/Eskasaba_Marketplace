<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Seller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatController extends Controller
{
    /**
     * Display chat list yang masuk ke seller yang login.
     */
    public function index(): View
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();

        $chats = Chat::with([
            'buyer',
            'seller.user',
            'product',
        ])
        ->where('seller_id', $seller->id)
        ->latest('last_message_at')
        ->paginate(10);

        return view('seller.chats.index', compact('chats'));
    }

    /**
     * Display chat detail.
     */
    public function show(Chat $chat): View
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        abort_unless($chat->seller_id === $seller->id, 403);

        $chat->load([
            'buyer',
            'seller.user',
            'product',
            'messages.sender',
        ]);

        return view('seller.chats.show', compact('chat'));
    }

    /**
     * Send message in chat.
     */
    public function storeMessage(Request $request, Chat $chat): RedirectResponse
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        abort_unless($chat->seller_id === $seller->id, 403);

        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $msg = ChatMessage::create([
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