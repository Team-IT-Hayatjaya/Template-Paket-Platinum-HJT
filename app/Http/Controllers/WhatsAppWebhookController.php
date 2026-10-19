<?php

namespace App\Http\Controllers;

use App\Contracts\WhatsAppProvider;
use App\Services\WhatsAppBotService;
use App\Support\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        protected WhatsAppProvider $provider,
        protected WhatsAppBotService $botService
    ) {}

    /**
     * Verifikasi pendaftaran webhook Meta WhatsApp Cloud API (GET).
     */
    public function verify(Request $request): Response|JsonResponse
    {
        if (! Package::has('whatsapp_bot')) {
            abort(404, 'Fitur WhatsApp Bot tidak aktif.');
        }

        $challenge = $this->provider->verifyHubChallenge($request);

        if ($challenge !== null) {
            return response($challenge, 200, ['Content-Type' => 'text/plain']);
        }

        return response('Verifikasi Hub Token Tidak Valid', 403);
    }

    /**
     * Terima pesan webhook masuk dari Meta WhatsApp Cloud API (POST).
     */
    public function receive(Request $request): JsonResponse
    {
        if (! Package::has('whatsapp_bot')) {
            return response()->json(['status' => 'error', 'message' => 'Layanan WhatsApp Bot dinonaktifkan.'], 403);
        }

        // Verifikasi tanda tangan digital payload
        if (! $this->provider->verifyWebhookSignature($request)) {
            return response()->json(['status' => 'error', 'message' => 'Tanda tangan webhook tidak valid.'], 401);
        }

        $incoming = $this->provider->parseIncomingMessage($request);

        if ($incoming) {
            $this->botService->handleIncomingMessage($incoming['from'], $incoming['text']);
        }

        return response()->json(['status' => 'EVENT_RECEIVED'], 200);
    }

    /**
     * Mode Uji Simulator Bot (tanpa nomor asli, untuk pengujian lokal & CI).
     */
    public function testSimulate(Request $request): JsonResponse
    {
        if (! Package::has('whatsapp_bot')) {
            return response()->json(['status' => 'error', 'message' => 'Layanan WhatsApp Bot dinonaktifkan.'], 403);
        }

        $request->validate([
            'from' => ['required', 'string'],
            'text' => ['required', 'string'],
        ]);

        $result = $this->botService->handleIncomingMessage(
            (string) $request->input('from'),
            (string) $request->input('text')
        );

        return response()->json([
            'status' => 'simulated_success',
            'package' => Package::current(),
            'result' => $result,
        ], 200);
    }
}
