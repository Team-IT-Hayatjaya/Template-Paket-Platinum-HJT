<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface WhatsAppProvider
{
    /**
     * Kirim pesan teks ke nomor WhatsApp tujuan.
     */
    public function sendMessage(string $to, string $message): array;

    /**
     * Verifikasi tanda tangan kriptografi webhook (HMAC-SHA256).
     */
    public function verifyWebhookSignature(Request $request): bool;

    /**
     * Verifikasi Hub Challenge pada saat registrasi URL webhook pertama kali.
     */
    public function verifyHubChallenge(Request $request): ?string;

    /**
     * Parsing dan ekstrak pesan masuk dari payload webhook.
     *
     * @return array{from: string, text: string, message_id: string}|null
     */
    public function parseIncomingMessage(Request $request): ?array;
}
