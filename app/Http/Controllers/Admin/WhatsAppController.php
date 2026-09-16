<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppController extends Controller
{
    /**
     * Tampilan utama Pengelolaan WhatsApp Bot di Admin Panel.
     */
    public function index(): View
    {
        $status = WhatsAppBotService::getStatus();

        return view('admin.whatsapp.index', compact('status'));
    }

    /**
     * Endpoint API JSON untuk polling status realtime.
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
}
