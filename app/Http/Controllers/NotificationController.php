<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of user notifications.
     */
    public function index(): View
    {
        $notifications = Auth::user()
            ->notifications()
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a specific notification as read and redirect to link.
     */
    public function read(Notification $notification): RedirectResponse
    {
        if ($notification->user_id === Auth::id()) {
            $notification->update(['is_read' => true]);
        }

        if ($notification->link) {
            return redirect($notification->link);
        }

        return back();
    }

    /**
     * Mark all notifications as read.
     */
    public function readAll(): RedirectResponse
    {
        Auth::user()
            ->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
