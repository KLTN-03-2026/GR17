<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use App\Services\HoaDonPaymentSyncService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SepayWebhookController extends Controller
{
    public function __construct(
        private readonly HoaDonPaymentSyncService $paymentSyncService,
    ) {
    }

    public function handle(Request $request): JsonResponse
    {
        if (!$this->authorizeWebhook($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Xác thực webhook thất bại.',
            ], 401);
        }

        $payload = $request->all();
        $transferType = strtolower((string) ($payload['transferType'] ?? $payload['transfer_type'] ?? ''));
        if ($transferType !== '' && $transferType !== 'in') {
            return response()->json([
                'success' => true,
                'message' => 'Webhook đã được bỏ qua vì không phải giao dịch nạp tiền.',
            ]);
        }

        $invoiceCode = $this->extractInvoiceCode($payload);
        if (!$invoiceCode) {
            return response()->json([
                'success' => true,
                'message' => 'Không tìm thấy mã hóa đơn trong nội dung chuyển khoản.',
            ]);
        }

        $hoaDon = HoaDon::query()
            ->with(['tour', 'tourKhoiHanh', 'latestPendingQrPayment', 'latestQrPayment'])
            ->find($invoiceCode);

        if (!$hoaDon) {
            return response()->json([
                'success' => true,
                'message' => 'Không tìm thấy hóa đơn khớp với webhook.',
            ]);
        }

        $transferAmount = (int) round((float) ($payload['transferAmount'] ?? $payload['transfer_amount'] ?? 0));
        if ($transferAmount !== (int) round((float) $hoaDon->tong_tien)) {
            return response()->json([
                'success' => true,
                'message' => 'Số tiền giao dịch không khớp với hóa đơn.',
            ]);
        }

        $qrPayment = $hoaDon->latestPendingQrPayment ?: $hoaDon->latestQrPayment;
        if ($qrPayment) {
            $qrPayment->forceFill([
                'webhook_transaction_id' => (string) ($payload['id'] ?? $qrPayment->webhook_transaction_id),
                'reference_code' => (string) ($payload['referenceCode'] ?? $payload['reference_code'] ?? $qrPayment->reference_code),
                'webhook_payload' => $payload,
            ])->save();
        }

        $paidAt = $this->parsePaidAt($payload['transactionDate'] ?? $payload['transaction_date'] ?? null);
        $reference = (string) ($payload['referenceCode'] ?? $payload['reference_code'] ?? $payload['id'] ?? '');

        $this->paymentSyncService->markAsPaid(
            $hoaDon,
            $reference !== '' ? $reference : null,
            $paidAt,
            $qrPayment,
            $payload
        );

        return response()->json([
            'success' => true,
            'message' => 'Webhook thanh toán đã được ghi nhận.',
        ]);
    }

    private function authorizeWebhook(Request $request): bool
    {
        $expectedKey = (string) config('payment.sepay.webhook_api_key');
        if ($expectedKey === '') {
            return true;
        }

        $authorization = (string) $request->header('Authorization', '');
        if ($authorization === "Apikey {$expectedKey}" || $authorization === "Bearer {$expectedKey}") {
            return true;
        }

        return (string) $request->header('X-SePay-Api-Key', '') === $expectedKey;
    }

    private function extractInvoiceCode(array $payload): ?string
    {
        $candidates = array_filter([
            $payload['code'] ?? null,
            $payload['content'] ?? null,
            $payload['description'] ?? null,
        ]);

        foreach ($candidates as $candidate) {
            $value = strtoupper(trim((string) $candidate));
            if ($value === '') {
                continue;
            }

            if (preg_match('/HD\d{8}/', $value, $matches) === 1) {
                return $matches[0];
            }

            if (preg_match('/^[A-Z0-9]+$/', $value) === 1) {
                return $value;
            }
        }

        return null;
    }

    private function parsePaidAt(mixed $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse((string) $value, 'Asia/Ho_Chi_Minh');
        } catch (\Throwable) {
            return null;
        }
    }
}
