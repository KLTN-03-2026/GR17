<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class VietQrService
{
    public function createPaymentQr(string $invoiceCode, int|float $amount): array
    {
        $accountNo = (string) config('payment.bank.account_no');
        $accountName = (string) config('payment.bank.account_name');
        $bankBin = (string) config('payment.bank.bin');
        $template = (string) config('payment.vietqr.template', 'compact');

        $payload = [
            'accountNo' => $accountNo,
            'accountName' => $accountName,
            'acqId' => $bankBin,
            'amount' => (int) round($amount),
            'addInfo' => $invoiceCode,
            'template' => $template,
        ];

        $clientId = (string) config('payment.vietqr.client_id');
        $apiKey = (string) config('payment.vietqr.api_key');

        if ($clientId !== '' && $apiKey !== '') {
            $response = Http::withHeaders([
                'x-client-id' => $clientId,
                'x-api-key' => $apiKey,
                'Accept' => 'application/json',
            ])->post((string) config('payment.vietqr.generate_url'), $payload);

            if ($response->successful()) {
                $data = $response->json('data', []);
                $qrUrl = $data['qrDataURL'] ?? $data['qrCodeURL'] ?? $data['qr_url'] ?? null;

                if ($qrUrl) {
                    return [
                        'qr_url' => $qrUrl,
                        'qr_payload' => $payload + ['response' => $data],
                    ];
                }
            }
        }

        $baseImageUrl = rtrim((string) config('payment.vietqr.image_url'), '/');
        $encodedAccountName = rawurlencode($accountName);
        $encodedAddInfo = rawurlencode($invoiceCode);
        $qrUrl = "{$baseImageUrl}/{$bankBin}-{$accountNo}-{$template}.jpg?amount={$payload['amount']}&addInfo={$encodedAddInfo}&accountName={$encodedAccountName}";

        return [
            'qr_url' => $qrUrl,
            'qr_payload' => $payload + ['image_url' => $qrUrl],
        ];
    }
}
