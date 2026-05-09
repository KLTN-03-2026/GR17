<?php

namespace App\Services;

use App\Models\HoaDon;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SepayTransactionSyncService
{
    public function __construct(
        private readonly HoaDonPaymentSyncService $paymentSyncService,
    ) {
    }

    public function syncInvoicePaymentStatus(HoaDon $hoaDon): HoaDon
    {
        $hoaDon->loadMissing(['latestQrPayment', 'latestPendingQrPayment', 'tour', 'tourKhoiHanh']);

        if (!$this->shouldSync($hoaDon)) {
            return $hoaDon->fresh(['tour', 'tourKhoiHanh', 'latestQrPayment']) ?? $hoaDon;
        }

        $transaction = $this->findMatchingTransaction($hoaDon);
        if (!$transaction) {
            return $hoaDon->fresh(['tour', 'tourKhoiHanh', 'latestQrPayment']) ?? $hoaDon;
        }

        $reference = $this->extractReference($transaction);
        $paidAt = $this->parsePaidAt($transaction);
        $qrPayment = $hoaDon->latestPendingQrPayment ?: $hoaDon->latestQrPayment;

        return $this->paymentSyncService->markAsPaid(
            $hoaDon,
            $reference !== '' ? $reference : null,
            $paidAt,
            $qrPayment,
            [
                'source' => 'sepay_api',
                'transaction' => $transaction,
            ],
        );
    }

    private function shouldSync(HoaDon $hoaDon): bool
    {
        $apiToken = trim((string) config('payment.sepay.api_token', ''));

        if ($apiToken === '') {
            return false;
        }

        if (($hoaDon->payment_method ?? '') !== 'vietqr_bank_transfer') {
            return false;
        }

        if (($hoaDon->payment_status ?? 'pending') !== 'pending' || (int) $hoaDon->trang_thai_thanh_toan !== 0) {
            return false;
        }

        return true;
    }

    private function findMatchingTransaction(HoaDon $hoaDon): ?array
    {
        $invoiceCode = strtoupper(trim((string) $hoaDon->ma_hoa_don));
        if ($invoiceCode === '') {
            return null;
        }

        $baseUrl = rtrim((string) config('payment.sepay.api_base_url', 'https://userapi.sepay.vn/v2'), '/');
        $apiToken = trim((string) config('payment.sepay.api_token', ''));
        $accountNumber = trim((string) config('payment.bank.account_no', ''));

        try {
            $response = Http::acceptJson()
                ->withToken($apiToken)
                ->timeout(10)
                ->get("{$baseUrl}/transactions", [
                    'q' => $invoiceCode,
                    'transfer_type' => 'in',
                    'page' => 1,
                    'per_page' => 20,
                ]);
        } catch (Throwable $exception) {
            Log::warning('Không thể đồng bộ giao dịch SePay khi kiểm tra trạng thái hóa đơn.', [
                'invoice_code' => $invoiceCode,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }

        if (!$response->successful()) {
            Log::warning('SePay API trả về lỗi khi kiểm tra trạng thái hóa đơn.', [
                'invoice_code' => $invoiceCode,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return null;
        }

        $transactions = $response->json('data');
        if (!is_array($transactions)) {
            return null;
        }

        return collect($transactions)
            ->filter(fn ($transaction) => is_array($transaction))
            ->filter(fn (array $transaction) => $this->matchesInvoice($transaction, $hoaDon, $invoiceCode, $accountNumber))
            ->sortByDesc(fn (array $transaction) => $this->parsePaidAt($transaction)?->getTimestamp() ?? 0)
            ->values()
            ->first();
    }

    private function matchesInvoice(array $transaction, HoaDon $hoaDon, string $invoiceCode, string $accountNumber): bool
    {
        $transferType = strtolower(trim((string) ($transaction['transfer_type'] ?? $transaction['transferType'] ?? '')));
        if ($transferType !== '' && $transferType !== 'in') {
            return false;
        }

        $amount = (int) round((float) (
            $transaction['amount_in']
            ?? $transaction['transferAmount']
            ?? $transaction['transfer_amount']
            ?? $transaction['amount']
            ?? 0
        ));

        if ($amount !== (int) round((float) $hoaDon->tong_tien)) {
            return false;
        }

        $transactionAccount = trim((string) ($transaction['account_number'] ?? $transaction['accountNumber'] ?? ''));
        if ($accountNumber !== '' && $transactionAccount !== '' && $transactionAccount !== $accountNumber) {
            return false;
        }

        foreach ($this->extractContentCandidates($transaction) as $candidate) {
            if (str_contains(strtoupper($candidate), $invoiceCode)) {
                return true;
            }
        }

        return false;
    }

    private function extractContentCandidates(array $transaction): array
    {
        return array_values(array_filter([
            trim((string) ($transaction['transaction_content'] ?? '')),
            trim((string) ($transaction['content'] ?? '')),
            trim((string) ($transaction['description'] ?? '')),
            trim((string) ($transaction['code'] ?? '')),
        ], fn (string $value) => $value !== ''));
    }

    private function extractReference(array $transaction): string
    {
        return trim((string) (
            $transaction['reference_number']
            ?? $transaction['referenceCode']
            ?? $transaction['reference_code']
            ?? $transaction['id']
            ?? ''
        ));
    }

    private function parsePaidAt(array $transaction): ?CarbonInterface
    {
        $value = $transaction['transaction_date'] ?? $transaction['transactionDate'] ?? null;
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse((string) $value);
        } catch (Throwable) {
            return null;
        }
    }
}
