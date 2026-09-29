<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsAppBroadcast;
use App\Models\WhatsAppBroadcastLog;
use App\Services\WhatsAppBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WhatsAppController extends Controller
{
    /**
     * Tampilan utama Pengelolaan WhatsApp Bot di Admin Panel.
     */
    public function index(): View
    {
        $status = WhatsAppBotService::getStatus();

        // Data statistik penerima dengan nomor WA terisi
        $recipientStats = [
            'all'        => User::byTargetRecipient('all')->count(),
            'teacher'    => User::byTargetRecipient('teacher')->count(),
            'student_10' => User::byTargetRecipient('student_10')->count(),
            'student_11' => User::byTargetRecipient('student_11')->count(),
            'student_12' => User::byTargetRecipient('student_12')->count(),
        ];

        // Daftar Campaign Broadcast Terakhir
        $broadcasts = WhatsAppBroadcast::latest()
            ->take(15)
            ->get();

        // Broadcast aktif saat ini (jika ada)
        $activeBroadcast = WhatsAppBroadcast::whereIn('status', ['pending', 'processing'])
            ->latest()
            ->first();

        return view('admin.whatsapp.index', compact('status', 'recipientStats', 'broadcasts', 'activeBroadcast'));
    }

    /**
     * Endpoint API JSON untuk polling status realtime bot.
     */
    public function status(): JsonResponse
    {
        return response()->json(WhatsAppBotService::getStatus());
    }

    /**
     * Action: Aktifkan Bot WhatsApp.
     */
    public function start(Request $request): JsonResponse|RedirectResponse
    {
        $result = WhatsAppBotService::start();

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->route('admin.whatsapp.index')->with('success', $result['message']);
    }

    /**
     * Action: Menonaktifkan Bot WhatsApp.
     */
    public function stop(Request $request): JsonResponse|RedirectResponse
    {
        $result = WhatsAppBotService::stop();

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->route('admin.whatsapp.index')->with('success', $result['message']);
    }

    /**
     * Action: Memutuskan Koneksi WhatsApp.
     */
    public function disconnect(Request $request): JsonResponse|RedirectResponse
    {
        $result = WhatsAppBotService::disconnect();

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->route('admin.whatsapp.index')->with('success', $result['message']);
    }

    /**
     * Action: Reset Session WhatsApp (Hapus Auth & QR baru).
     */
    public function resetSession(Request $request): JsonResponse|RedirectResponse
    {
        $result = WhatsAppBotService::resetSession();

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->route('admin.whatsapp.index')->with('success', $result['message']);
    }

    /**
     * Endpoint AJAX untuk mendapatkan jumlah penerima berdasarkan target pilihan.
     */
    public function getRecipientCount(Request $request): JsonResponse
    {
        $target = $request->query('target', 'all');
        $count = User::byTargetRecipient($target)->count();

        return response()->json([
            'target' => $target,
            'count'  => $count,
        ]);
    }

    /**
     * Action: Mengirim Broadcast Pesan Kustom ke Target Pengguna (Secara Bertahap/Anti-Ban).
     */
    public function sendBroadcast(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'title'         => 'nullable|string|max:255',
            'target_type'   => 'required|in:all,teacher,student_10,student_11,student_12',
            'message'       => 'required|string|min:3|max:4000',
            'delay_seconds' => 'required|integer|min:1|max:60',
        ]);

        $targetType = $request->input('target_type');
        $message = $request->input('message');
        $delaySeconds = (int) $request->input('delay_seconds', 3);
        $title = $request->input('title') ?: 'Pesan Broadcast Kustom';

        // Cek status koneksi WhatsApp Bot
        $botStatus = WhatsAppBotService::getStatus();
        if (empty($botStatus['is_connected'])) {
            $errMsg = 'Pesan broadcast tidak dapat dikirim karena Bot WhatsApp belum aktif / terhubung. Silakan aktifkan bot dan pindai QR Code terlebih dahulu.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errMsg], 422);
            }
            return redirect()->back()->with('error', $errMsg);
        }

        // Ambil data users sesuai target
        $users = User::byTargetRecipient($targetType)->get();

        if ($users->isEmpty()) {
            $errMsg = 'Tidak ada pengguna dengan nomor WhatsApp terisi/valid pada target yang dipilih.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errMsg], 422);
            }
            return redirect()->back()->with('error', $errMsg);
        }

        // Buat record Campaign Broadcast
        $broadcast = WhatsAppBroadcast::create([
            'title'            => $title,
            'target_type'      => $targetType,
            'message'          => $message,
            'total_recipients' => $users->count(),
            'sent_count'       => 0,
            'failed_count'     => 0,
            'delay_seconds'    => $delaySeconds,
            'status'           => 'pending',
            'created_by'       => Auth::id(),
        ]);

        // Inisialisasi Log Penerima
        $logEntries = [];
        $now = now();
        foreach ($users as $user) {
            $logEntries[] = [
                'whatsapp_broadcast_id' => $broadcast->id,
                'user_id'               => $user->id,
                'phone'                 => $user->phone,
                'user_name'             => $user->username ?: 'Pengguna',
                'recipient_group'       => $user->isTeacher() ? 'Guru' : ($user->class_room ?: 'Siswa'),
                'status'                => 'pending',
                'created_at'            => $now,
                'updated_at'            => $now,
            ];
        }

        foreach (array_chunk($logEntries, 250) as $chunk) {
            WhatsAppBroadcastLog::insert($chunk);
        }

        // Jalankan runner di background
        $this->launchBackgroundBroadcast($broadcast->id);

        $successMsg = "Broadcast #{$broadcast->id} berhasil dimulai untuk {$broadcast->total_recipients} penerima dengan jeda {$delaySeconds} detik per pesan!";

        if ($request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'message'      => $successMsg,
                'broadcast_id' => $broadcast->id,
            ]);
        }

        return redirect()->route('admin.whatsapp.index')->with('success', $successMsg);
    }

    /**
     * Endpoint API JSON untuk polling status realtime progress broadcast.
     */
    public function getBroadcastStatus(int $id): JsonResponse
    {
        $broadcast = WhatsAppBroadcast::with(['logs' => function ($q) {
            $q->latest()->take(50);
        }])->find($id);

        if (!$broadcast) {
            return response()->json(['error' => 'Broadcast tidak ditemukan'], 404);
        }

        return response()->json([
            'id'               => $broadcast->id,
            'title'            => $broadcast->title,
            'target_label'     => $broadcast->target_label,
            'target_type'      => $broadcast->target_type,
            'status'           => $broadcast->status,
            'total_recipients' => $broadcast->total_recipients,
            'sent_count'       => $broadcast->sent_count,
            'failed_count'     => $broadcast->failed_count,
            'delay_seconds'    => $broadcast->delay_seconds,
            'progress_percent' => $broadcast->progress_percentage,
            'started_at'       => $broadcast->started_at?->format('d M Y H:i:s'),
            'completed_at'     => $broadcast->completed_at?->format('d M Y H:i:s'),
            'logs'             => $broadcast->logs->map(fn ($log) => [
                'id'          => $log->id,
                'user_name'   => $log->user_name,
                'phone'       => $log->phone,
                'group'       => $log->recipient_group,
                'status'      => $log->status,
                'error'       => $log->error_message,
                'sent_at'     => $log->sent_at?->format('H:i:s'),
            ]),
        ]);
    }

    /**
     * Action: Batalkan Broadcast yang sedang berjalan.
     */
    public function cancelBroadcast(int $id, Request $request): JsonResponse|RedirectResponse
    {
        $broadcast = WhatsAppBroadcast::find($id);

        if ($broadcast && in_array($broadcast->status, ['pending', 'processing'])) {
            $broadcast->update(['status' => 'cancelled']);
            $msg = "Broadcast #{$id} berhasil dibatalkan.";
        } else {
            $msg = "Broadcast #{$id} tidak sedang berjalan atau tidak ditemukan.";
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.whatsapp.index')->with('success', $msg);
    }

    /**
     * Run background CLI command for processing broadcast asynchronously.
     */
    protected function launchBackgroundBroadcast(int $broadcastId): void
    {
        $artisan = base_path('artisan');
        $php = PHP_BINARY ?: 'php';

        if (str_starts_with(PHP_OS, 'WIN')) {
            pclose(popen("start /B {$php} \"{$artisan}\" whatsapp:send-broadcast {$broadcastId} > NUL 2>&1", "r"));
        } else {
            exec("{$php} \"{$artisan}\" whatsapp:send-broadcast {$broadcastId} > /dev/null 2>&1 &");
        }
    }
}
