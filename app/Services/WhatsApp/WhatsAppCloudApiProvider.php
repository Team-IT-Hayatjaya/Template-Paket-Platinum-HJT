<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppCloudApiProvider implements WhatsAppProvider
{
    protected string $token;
    protected string $phoneNumberId;
    protected string $verifyToken;
    protected string $appSecret;

    public function __construct()
    {
        $this->token = (string) config('services.whatsapp.token', env('WHATSAPP_TOKEN', ''));
        $this->phoneNumberId = (string) config('services.whatsapp.phone_number_id', env('WHATSAPP_PHONE_NUMBER_ID', ''));
        $this->verifyToken = (string) config('services.whatsapp.verify_token', env('WHATSAPP_VERIFY_TOKEN', ''));
        $this->appSecret = (string) config('services.whatsapp.app_secret', env('WHATSAPP_APP_SECRET', ''));
    }

    /**
     * Kirim pesan teks via Meta WhatsApp Cloud API.
     */
    public function sendMessage(string $to, string $message): array
    {
        $cleanTo = preg_replace('/[^0-9]/', '', $to);

        if (empty($this->token) || empty($this->phoneNumberId)) {
            Log::warning('[WhatsApp] Kredensial belum lengkap, pesan disimulasikan.', ['to' => $cleanTo]);
            return [
                'status' => 'simulated',
                'message_id' => 'sim_' . uniqid(),
            ];
        }

        $url = "https://graph.facebook.com/v21.0/{$this->phoneNumberId}/messages";

        try {
            $response = Http::withToken($this->token)
                ->timeout(10)
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $cleanTo,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => false,
                        'body' => $message,
                    ],
                ]);

            if ($response->successful()) {
                return [
                    'status' => 'success',
                    'response' => $response->json(),
                ];
            }

            Log::error('[WhatsApp] Gagal mengirim pesan via Cloud API', ['response' => $response->body()]);
            return [
                'status' => 'error',
                'error' => $response->json(),
            ];
        } catch (\Throwable $e) {
            Log::error('[WhatsApp] Exception saat kirim pesan', ['error' => $e->getMessage()]);
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verifikasi tanda tangan X-Hub-Signature-256 Meta.
     */
    public function verifyWebhookSignature(Request $request): bool
    {
        $signature = $request->header('X-Hub-Signature-256');

        // Jika app_secret tidak diset di env, fallback to true pada local/testing
        if (empty($this->appSecret)) {
            return app()->environment('local', 'testing');
        }

        if (empty($signature)) {
            return false;
        }

        $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $this->appSecret);

        return hash_equals($expected, $signature);
    }

    /**
     * Tangani verifikasi awal URL webhook Meta (Hub Challenge).
     */
    public function verifyHubChallenge(Request $request): ?string
    {
        $mode = $request->query('hub_mode') ?? $request->query('hub.mode');
        $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');

        if ($mode === 'subscribe' && ! empty($token) && ! empty($this->verifyToken)) {
            if (hash_equals($this->verifyToken, $token)) {
                return (string) $challenge;
            }
        }

        return null;
    }

    /**
     * Ekstrak pesan masuk dari webhook Cloud API.
     */
    public function parseIncomingMessage(Request $request): ?array
    {
        // 1. Dukungan format uji sederhana (Testing / Simulator)
        if ($request->has('from') && $request->has('text')) {
            return [
                'from' => (string) $request->input('from'),
                'text' => trim((string) $request->input('text')),
                'message_id' => (string) ($request->input('message_id') ?: 'sim_' . uniqid()),
            ];
        }

        // 2. Format resmi Meta WhatsApp Cloud API
        $payload = $request->json()->all();
        $entry = $payload['entry'][0] ?? null;
        $change = $entry['changes'][0] ?? null;
        $value = $change['value'] ?? null;
        $message = $value['messages'][0] ?? null;

        if (! $message) {
            return null;
        }

        $from = (string) ($message['from'] ?? '');
        $text = '';

        if (($message['type'] ?? '') === 'text') {
            $text = trim((string) ($message['text']['body'] ?? ''));
        } elseif (($message['type'] ?? '') === 'button') {
            $text = trim((string) ($message['button']['text'] ?? ''));
        } elseif (($message['type'] ?? '') === 'interactive') {
            $text = trim((string) ($message['interactive']['button_reply']['id'] ?? ($message['interactive']['list_reply']['id'] ?? '')));
        }

        if (empty($from) || empty($text)) {
            return null;
        }

        return [
            'from' => $from,
            'text' => $text,
            'message_id' => (string) ($message['id'] ?? uniqid()),
        ];
    }
}
