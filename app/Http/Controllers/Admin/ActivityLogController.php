<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Tampilkan riwayat log login dan aktivitas sistem.
     */
    public function index(Request $request): View
    {
        $query = ActivityLog::with(['user', 'admin'])
            ->latest();

        // Search Filter
        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('event', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('nis_nip', 'like', "%{$search}%");
                  })
                  ->orWhereHas('admin', function ($a) use ($search) {
                      $a->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Event
        if ($event = $request->input('event')) {
            $query->where('event', $event);
        }

        // Filter Account Type (User vs Admin)
        if ($type = $request->input('type')) {
            if ($type === 'admin') {
                $query->whereNotNull('admin_id');
            } elseif ($type === 'user') {
                $query->whereNotNull('user_id');
            }
        }

        $logs = $query->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'total_logs'    => ActivityLog::count(),
            'today_logins'  => ActivityLog::whereDate('created_at', today())
                                    ->whereIn('event', ['login', 'admin_login'])
                                    ->count(),
            'unique_users'  => ActivityLog::whereDate('created_at', today())
                                    ->whereNotNull('user_id')
                                    ->distinct('user_id')
                                    ->count('user_id'),
            'admin_actions' => ActivityLog::whereNotNull('admin_id')->count(),
        ];

        $events = ActivityLog::select('event')->distinct()->pluck('event');

        return view('admin.login-logs.index', compact('logs', 'stats', 'events'));
    }
}
