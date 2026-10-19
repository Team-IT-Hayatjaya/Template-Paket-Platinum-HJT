<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppProvider;
use Illuminate\Http\Request;

class MockWhatsAppProvider implements WhatsAppProvider
{
    /**
     * @var array<array{to: string, message: string, timestamp: string}>
     */
    public static array $sentMessages = [];

    public static function clear(): void
    {
        self::$sentMessages = [];
    }

    public static function lastMessage(): ?array
    {
        if (empty(self::$sentMessages)) {
            return null;
        }

        return self::$sentMessages[count(self::$sentMessages) - 1];
    }

    public function sendMessage(string $to, string $message): array
    {
        $record = [
            'to' => $to,
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
        ];

        self::$sentMessages[] = $record;

        return [
            'status' => 'mock_sent',
            'message_id' => 'mock_' . uniqid(),
            'to' => $to,
        ];
    }

    public function verifyWebhookSignature(Request $request): bool
    {
        // Mock provider selalu lolos verifikasi dalam mode uji
        return true;
    }

    public function verifyHubChallenge(Request $request): ?string
    {
        return (string) ($request->query('hub_challenge') ?? $request->query('hub.challenge') ?? 'mock_challenge_ok');
    }

    public function parseIncomingMessage(Request $request): ?array
    {
        if ($request->has('from') && $request->has('text')) {
            return [
                'from' => (string) $request->input('from'),
                'text' => trim((string) $request->input('text')),
                'message_id' => (string) ($request->input('message_id') ?: 'mock_msg_' . uniqid()),
            ];
        }

        $payload = $request->json()->all();
        $entry = $payload['entry'][0] ?? null;
        $change = $entry['changes'][0] ?? null;
        $value = $change['value'] ?? null;
        $message = $value['messages'][0] ?? null;

        if (! $message) {
            return null;
        }

        return [
            'from' => (string) ($message['from'] ?? ''),
            'text' => trim((string) ($message['text']['body'] ?? '')),
            'message_id' => (string) ($message['id'] ?? 'mock_msg_' . uniqid()),
        ];
    }
}
