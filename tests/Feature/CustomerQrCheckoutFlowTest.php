<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\DoiTac;
use App\Models\KhachHang;
use App\Models\Nhom;
use App\Models\ThanhVienNhom;
use App\Models\Tour;
use App\Models\TourKhoiHanh;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerQrCheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('payment.bank.account_no', '0399620355');
        config()->set('payment.bank.account_name', 'VU HA THAI SON');
        config()->set('payment.vietqr.template', 'PtCU367');
        config()->set('payment.vietqr.client_id', null);
        config()->set('payment.vietqr.api_key', null);
        config()->set('payment.sepay.webhook_api_key', 'test-sepay-key');
    }

    public function test_customer_can_create_qr_payment_for_tour_checkout(): void
    {
        $khachHang = $this->createCustomer('1001', 'customer1@example.com', '0911111111');
        $tour = $this->createTour('900001', 1500000);
        $lich = $this->createSchedule('500001', $tour->ma_tour, 5, true);

        Sanctum::actingAs($khachHang);

        $response = $this->postJson("/api/khach-hang/tour/{$tour->ma_tour}/thanh-toan/qr", [
            'ma_thoi_gian_tour' => $lich->ma_thoi_gian_tour,
            'thong_tin_nguoi_dat' => [
                'ho_ten' => 'Vu Ha Thai Son',
                'so_dien_thoai' => '0911111111',
                'email' => 'customer1@example.com',
                'dia_chi' => '1 Duong Demo, TP.HCM',
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.ma_tour', $tour->ma_tour)
            ->assertJsonPath('data.ma_thoi_gian_tour', $lich->ma_thoi_gian_tour)
            ->assertJsonPath('data.so_tien', 1500000)
            ->assertJsonPath('data.payment_method', 'vietqr_bank_transfer')
            ->assertJsonPath('data.payment_status', 'pending');

        $maHoaDon = (string) $response->json('data.ma_hoa_don');
        $maNhom = (string) $response->json('data.ma_nhom');

        $this->assertMatchesRegularExpression('/^HD\d{8}$/', $maHoaDon);
        $this->assertSame($maHoaDon, $response->json('data.noi_dung_chuyen_khoan'));
        $this->assertStringContainsString("amount=1500000", (string) $response->json('data.qr_url'));
        $this->assertStringContainsString("addInfo={$maHoaDon}", (string) $response->json('data.qr_url'));
        $this->assertStringContainsString("/970422-0399620355-", (string) $response->json('data.qr_url'));

        $this->assertDatabaseHas('nhom', [
            'Ma_nhom' => $maNhom,
            'ten_nhom' => "Đơn tour {$maHoaDon}",
        ]);

        $this->assertDatabaseHas('thanh_vien_nhom', [
            'Ma_nhom' => $maNhom,
            'Ma_khach_hang' => $khachHang->Ma_khach_hang,
            'vai_tro' => 1,
        ]);

        $this->assertDatabaseHas('hoa_don', [
            'ma_hoa_don' => $maHoaDon,
            'ma_nhom' => $maNhom,
            'ma_khach_hang_dat' => $khachHang->Ma_khach_hang,
            'ma_thoi_gian_tour' => $lich->ma_thoi_gian_tour,
            'ma_doi_tuong' => $tour->ma_tour,
            'loai_hoa_don' => 0,
            'payment_method' => 'vietqr_bank_transfer',
            'payment_status' => 'pending',
            'trang_thai_thanh_toan' => 0,
        ]);

        $this->assertDatabaseHas('giao_dich_qr', [
            'ma_hoa_don' => $maHoaDon,
            'so_tien' => 1500000,
            'noi_dung_chuyen_khoan' => $maHoaDon,
            'trang_thai' => 'pending',
        ]);

        $qrAttempt = \App\Models\GiaoDichQr::query()
            ->where('ma_hoa_don', $maHoaDon)
            ->latest('created_at')
            ->firstOrFail();

        $this->assertTrue(
            $qrAttempt->expires_at->betweenIncluded(now()->addMinutes(4), now()->addMinutes(5)->addSeconds(5)),
            'QR payment phai het han trong khoang 5 phut.'
        );
    }

    public function test_customer_cannot_create_qr_payment_when_schedule_is_closed(): void
    {
        $khachHang = $this->createCustomer('1002', 'customer2@example.com', '0922222222');
        $tour = $this->createTour('900002', 990000);
        $lich = $this->createSchedule('500002', $tour->ma_tour, 0, false);

        Sanctum::actingAs($khachHang);

        $this->postJson("/api/khach-hang/tour/{$tour->ma_tour}/thanh-toan/qr", [
            'ma_thoi_gian_tour' => $lich->ma_thoi_gian_tour,
            'thong_tin_nguoi_dat' => [
                'ho_ten' => 'Khach Hang Hai',
                'so_dien_thoai' => '0922222222',
                'email' => 'customer2@example.com',
                'dia_chi' => '2 Duong Demo, TP.HCM',
            ],
        ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertDatabaseCount('hoa_don', 0);
        $this->assertDatabaseCount('giao_dich_qr', 0);
    }

    public function test_customer_can_view_own_invoice_history_and_payment_status(): void
    {
        $khachHang = $this->createCustomer('1003', 'customer3@example.com', '0933333333');
        $tour = $this->createTour('900003', 1100000);
        $lich = $this->createSchedule('500003', $tour->ma_tour, 3, true);

        Sanctum::actingAs($khachHang);

        $createResponse = $this->postJson("/api/khach-hang/tour/{$tour->ma_tour}/thanh-toan/qr", [
            'ma_thoi_gian_tour' => $lich->ma_thoi_gian_tour,
            'thong_tin_nguoi_dat' => [
                'ho_ten' => 'Khach Hang Ba',
                'so_dien_thoai' => '0933333333',
                'email' => 'customer3@example.com',
                'dia_chi' => '3 Duong Demo, TP.HCM',
            ],
        ])->assertCreated();

        $maHoaDon = (string) $createResponse->json('data.ma_hoa_don');

        $this->getJson('/api/khach-hang/hoa-don')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.ma_hoa_don', $maHoaDon)
            ->assertJsonPath('data.0.ma_tour', $tour->ma_tour)
            ->assertJsonPath('data.0.ten_tour', $tour->ten_tour)
            ->assertJsonPath('data.0.payment_status', 'pending');

        $this->getJson("/api/khach-hang/hoa-don/{$maHoaDon}/payment-status")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.ma_hoa_don', $maHoaDon)
            ->assertJsonPath('data.ma_thoi_gian_tour', $lich->ma_thoi_gian_tour)
            ->assertJsonPath('data.payment_status', 'pending')
            ->assertJsonPath('data.noi_dung_chuyen_khoan', $maHoaDon);
    }

    public function test_sepay_webhook_marks_invoice_as_paid_idempotently_when_amount_and_content_match(): void
    {
        $khachHang = $this->createCustomer('1004', 'customer4@example.com', '0944444444');
        $this->createPartner('3001', 'partner3001@example.com');
        $tour = $this->createTour('900004', 2100000, '3001');
        $lich = $this->createSchedule('500004', $tour->ma_tour, 4, true);

        Sanctum::actingAs($khachHang);

        $createResponse = $this->postJson("/api/khach-hang/tour/{$tour->ma_tour}/thanh-toan/qr", [
            'ma_thoi_gian_tour' => $lich->ma_thoi_gian_tour,
            'thong_tin_nguoi_dat' => [
                'ho_ten' => 'Khach Hang Bon',
                'so_dien_thoai' => '0944444444',
                'email' => 'customer4@example.com',
                'dia_chi' => '4 Duong Demo, TP.HCM',
            ],
        ])->assertCreated();

        $maHoaDon = (string) $createResponse->json('data.ma_hoa_don');

        $payload = [
            'id' => 92704,
            'gateway' => 'VietinBank',
            'transactionDate' => '2026-04-13 14:02:37',
            'accountNumber' => '0399620355',
            'code' => null,
            'content' => $maHoaDon,
            'transferType' => 'in',
            'transferAmount' => 2100000,
            'accumulated' => 19077000,
            'subAccount' => null,
            'referenceCode' => 'MBVCB.3278907687',
            'description' => '',
        ];

        $headers = [
            'Authorization' => 'Apikey test-sepay-key',
        ];

        $this->postJson('/api/payments/sepay/webhook', $payload, $headers)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->postJson('/api/payments/sepay/webhook', $payload, $headers)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('hoa_don', [
            'ma_hoa_don' => $maHoaDon,
            'payment_status' => 'paid',
            'trang_thai_thanh_toan' => 1,
            'payment_reference' => 'MBVCB.3278907687',
            'ma_giao_dich' => 'MBVCB.3278907687',
        ]);

        $this->assertDatabaseHas('giao_dich_qr', [
            'ma_hoa_don' => $maHoaDon,
            'trang_thai' => 'paid',
            'webhook_transaction_id' => '92704',
            'reference_code' => 'MBVCB.3278907687',
        ]);

        $this->assertDatabaseHas('doi_soat_hoa_hong', [
            'ma_hoa_don' => $maHoaDon,
            'ma_doi_tac' => '3001',
            'trang_thai_thanh_toan' => 'chua_doi_soat',
        ]);

        $this->assertDatabaseCount('doi_soat_hoa_hong', 1);
        $this->assertDatabaseHas('tour_khoi_hanhs', [
            'ma_thoi_gian_tour' => $lich->ma_thoi_gian_tour,
            'so_cho' => 3,
        ]);
    }

    public function test_admin_invoice_payload_exposes_customer_and_latest_qr_summary(): void
    {
        $khachHang = $this->createCustomer('1005', 'customer5@example.com', '0955555555');
        $tour = $this->createTour('900005', 1750000);
        $lich = $this->createSchedule('500005', $tour->ma_tour, 6, true);
        $admin = $this->createAdmin('2005', 'admin2005@example.com', '0902005005');

        Sanctum::actingAs($khachHang);

        $createResponse = $this->postJson("/api/khach-hang/tour/{$tour->ma_tour}/thanh-toan/qr", [
            'ma_thoi_gian_tour' => $lich->ma_thoi_gian_tour,
            'thong_tin_nguoi_dat' => [
                'ho_ten' => 'Khach Hang Nam',
                'so_dien_thoai' => '0955555555',
                'email' => 'customer5@example.com',
                'dia_chi' => '5 Duong Demo, TP.HCM',
            ],
        ])->assertCreated();

        $maHoaDon = (string) $createResponse->json('data.ma_hoa_don');

        Sanctum::actingAs($admin);

        $this->getJson("/api/admin/hoa-don/{$maHoaDon}")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.ma_hoa_don', $maHoaDon)
            ->assertJsonPath('data.ma_tour', $tour->ma_tour)
            ->assertJsonPath('data.ma_thoi_gian_tour', $lich->ma_thoi_gian_tour)
            ->assertJsonPath('data.payment_method', 'vietqr_bank_transfer')
            ->assertJsonPath('data.payment_status', 'pending')
            ->assertJsonPath('data.customer_summary.ma_khach_hang', $khachHang->Ma_khach_hang)
            ->assertJsonPath('data.customer_summary.ho_ten', 'Khach Hang Nam')
            ->assertJsonPath('data.customer_summary.email', 'customer5@example.com')
            ->assertJsonPath('data.customer_summary.so_dien_thoai', '0955555555')
            ->assertJsonPath('data.latest_qr_payment_summary.trang_thai', 'pending')
            ->assertJsonPath('data.latest_qr_payment_summary.noi_dung_chuyen_khoan', $maHoaDon);
    }

    public function test_admin_cannot_manually_update_vietqr_invoice_status(): void
    {
        $khachHang = $this->createCustomer('1006', 'customer6@example.com', '0966666666');
        $tour = $this->createTour('900006', 1880000);
        $lich = $this->createSchedule('500006', $tour->ma_tour, 5, true);
        $admin = $this->createAdmin('2006', 'admin2006@example.com', '0902006006');

        Sanctum::actingAs($khachHang);

        $createResponse = $this->postJson("/api/khach-hang/tour/{$tour->ma_tour}/thanh-toan/qr", [
            'ma_thoi_gian_tour' => $lich->ma_thoi_gian_tour,
            'thong_tin_nguoi_dat' => [
                'ho_ten' => 'Khach Hang Sau',
                'so_dien_thoai' => '0966666666',
                'email' => 'customer6@example.com',
                'dia_chi' => '6 Duong Demo, TP.HCM',
            ],
        ])->assertCreated();

        $maHoaDon = (string) $createResponse->json('data.ma_hoa_don');

        Sanctum::actingAs($admin);

        $this->patchJson("/api/admin/hoa-don/{$maHoaDon}/status", [
            'trang_thai_thanh_toan' => 1,
        ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('hoa_don', [
            'ma_hoa_don' => $maHoaDon,
            'payment_status' => 'pending',
            'trang_thai_thanh_toan' => 0,
        ]);
    }

    public function test_partner_order_payload_is_normalized_and_scoped_to_partner_tour_orders(): void
    {
        $khachHang = $this->createCustomer('1007', 'customer7@example.com', '0977777777');
        $doiTacA = $this->createPartner('3007', 'partner3007@example.com');
        $this->createPartner('3008', 'partner3008@example.com');
        $tourA = $this->createTour('900007', 1990000, '3007');
        $tourB = $this->createTour('900008', 2090000, '3008');
        $lichA = $this->createSchedule('500007', $tourA->ma_tour, 5, true);
        $lichB = $this->createSchedule('500008', $tourB->ma_tour, 5, true);

        Sanctum::actingAs($khachHang);

        $this->postJson("/api/khach-hang/tour/{$tourA->ma_tour}/thanh-toan/qr", [
            'ma_thoi_gian_tour' => $lichA->ma_thoi_gian_tour,
            'thong_tin_nguoi_dat' => [
                'ho_ten' => 'Khach Hang Bay',
                'so_dien_thoai' => '0977777777',
                'email' => 'customer7@example.com',
                'dia_chi' => '7 Duong Demo, TP.HCM',
            ],
        ])->assertCreated();

        $this->postJson("/api/khach-hang/tour/{$tourB->ma_tour}/thanh-toan/qr", [
            'ma_thoi_gian_tour' => $lichB->ma_thoi_gian_tour,
            'thong_tin_nguoi_dat' => [
                'ho_ten' => 'Khach Hang Bay',
                'so_dien_thoai' => '0977777777',
                'email' => 'customer7@example.com',
                'dia_chi' => '7 Duong Demo, TP.HCM',
            ],
        ])->assertCreated();

        Sanctum::actingAs($doiTacA);

        $this->getJson('/api/doi-tac/don-hang')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.ma_tour', $tourA->ma_tour)
            ->assertJsonPath('data.0.ten_tour', $tourA->ten_tour)
            ->assertJsonPath('data.0.ma_thoi_gian_tour', $lichA->ma_thoi_gian_tour)
            ->assertJsonPath('data.0.payment_status', 'pending')
            ->assertJsonPath('data.0.customer_summary.ma_khach_hang', $khachHang->Ma_khach_hang)
            ->assertJsonPath('data.0.customer_summary.ho_ten', 'Khach Hang Bay')
            ->assertJsonPath('data.0.customer_summary.email', 'customer7@example.com')
            ->assertJsonPath('data.0.customer_summary.so_dien_thoai', '0977777777');
    }

    public function test_customer_can_retry_expired_invoice_without_creating_new_invoice(): void
    {
        $khachHang = $this->createCustomer('1008', 'customer8@example.com', '0988888888');
        $tour = $this->createTour('900009', 2220000);
        $lich = $this->createSchedule('500009', $tour->ma_tour, 5, true);

        Sanctum::actingAs($khachHang);

        $createResponse = $this->postJson("/api/khach-hang/tour/{$tour->ma_tour}/thanh-toan/qr", [
            'ma_thoi_gian_tour' => $lich->ma_thoi_gian_tour,
            'thong_tin_nguoi_dat' => [
                'ho_ten' => 'Khach Hang Tam',
                'so_dien_thoai' => '0988888888',
                'email' => 'customer8@example.com',
                'dia_chi' => '8 Duong Demo, TP.HCM',
            ],
        ])->assertCreated();

        $maHoaDon = (string) $createResponse->json('data.ma_hoa_don');

        $this->assertDatabaseHas('giao_dich_qr', [
            'ma_hoa_don' => $maHoaDon,
            'trang_thai' => 'pending',
        ]);

        \App\Models\GiaoDichQr::query()
            ->where('ma_hoa_don', $maHoaDon)
            ->update([
                'expires_at' => now()->subMinutes(6),
            ]);

        $this->postJson("/api/khach-hang/hoa-don/{$maHoaDon}/retry-payment")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.ma_hoa_don', $maHoaDon)
            ->assertJsonPath('data.payment_status', 'pending');

        $this->assertDatabaseHas('giao_dich_qr', [
            'ma_hoa_don' => $maHoaDon,
            'trang_thai' => 'expired',
        ]);

        $this->assertSame(2, \App\Models\GiaoDichQr::query()->where('ma_hoa_don', $maHoaDon)->count());
        $this->assertSame(1, \App\Models\GiaoDichQr::query()->where('ma_hoa_don', $maHoaDon)->where('trang_thai', 'pending')->count());
    }

    public function test_customer_can_cancel_pending_qr_payment(): void
    {
        $khachHang = $this->createCustomer('1013', 'customer13@example.com', '0904040404');
        $tour = $this->createTour('900113', 10000);
        $lich = $this->createSchedule('500113', $tour->ma_tour, 4, true);

        Sanctum::actingAs($khachHang);

        $createResponse = $this->postJson("/api/khach-hang/tour/{$tour->ma_tour}/thanh-toan/qr", [
            'ma_thoi_gian_tour' => $lich->ma_thoi_gian_tour,
            'thong_tin_nguoi_dat' => [
                'ho_ten' => 'Khach Hang Muoi Ba',
                'so_dien_thoai' => '0904040404',
                'email' => 'customer13@example.com',
                'dia_chi' => '13 Duong Demo, TP.HCM',
            ],
        ])->assertCreated();

        $maHoaDon = (string) $createResponse->json('data.ma_hoa_don');

        $this->postJson("/api/khach-hang/hoa-don/{$maHoaDon}/cancel-payment")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.ma_hoa_don', $maHoaDon)
            ->assertJsonPath('data.payment_status', 'failed')
            ->assertJsonPath('data.payment_reference', 'customer_cancelled');

        $this->assertDatabaseHas('hoa_don', [
            'ma_hoa_don' => $maHoaDon,
            'payment_status' => 'failed',
            'trang_thai_thanh_toan' => 2,
            'payment_reference' => 'customer_cancelled',
        ]);

        $this->assertDatabaseHas('giao_dich_qr', [
            'ma_hoa_don' => $maHoaDon,
            'trang_thai' => 'failed',
            'reference_code' => 'customer_cancelled',
        ]);
    }

    public function test_customer_cannot_cancel_paid_qr_payment(): void
    {
        $khachHang = $this->createCustomer('1014', 'customer14@example.com', '0905050505');
        $tour = $this->createTour('900114', 10000);
        $lich = $this->createSchedule('500114', $tour->ma_tour, 4, true);

        Sanctum::actingAs($khachHang);

        $createResponse = $this->postJson("/api/khach-hang/tour/{$tour->ma_tour}/thanh-toan/qr", [
            'ma_thoi_gian_tour' => $lich->ma_thoi_gian_tour,
            'thong_tin_nguoi_dat' => [
                'ho_ten' => 'Khach Hang Muoi Bon',
                'so_dien_thoai' => '0905050505',
                'email' => 'customer14@example.com',
                'dia_chi' => '14 Duong Demo, TP.HCM',
            ],
        ])->assertCreated();

        $maHoaDon = (string) $createResponse->json('data.ma_hoa_don');

        $payload = [
            'id' => 92714,
            'gateway' => 'MBBank',
            'transactionDate' => '2026-04-13 14:12:37',
            'accountNumber' => '0399620355',
            'code' => null,
            'content' => $maHoaDon,
            'transferType' => 'in',
            'transferAmount' => 10000,
            'accumulated' => 10000,
            'subAccount' => null,
            'referenceCode' => 'MBVCB.92714',
            'description' => '',
        ];

        $headers = [
            'Authorization' => 'Apikey test-sepay-key',
        ];

        $this->postJson('/api/payments/sepay/webhook', $payload, $headers)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->postJson("/api/khach-hang/hoa-don/{$maHoaDon}/cancel-payment")
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('hoa_don', [
            'ma_hoa_don' => $maHoaDon,
            'payment_status' => 'paid',
            'payment_reference' => 'MBVCB.92714',
        ]);
    }

    public function test_customer_payment_status_returns_expired_after_five_minutes(): void
    {
        Carbon::setTestNow('2026-04-13 10:00:00');

        try {
            $khachHang = $this->createCustomer('1009', 'customer9@example.com', '0999999999');
            $tour = $this->createTour('900010', 990000);
            $lich = $this->createSchedule('500010', $tour->ma_tour, 4, true);

            Sanctum::actingAs($khachHang);

            $createResponse = $this->postJson("/api/khach-hang/tour/{$tour->ma_tour}/thanh-toan/qr", [
                'ma_thoi_gian_tour' => $lich->ma_thoi_gian_tour,
                'thong_tin_nguoi_dat' => [
                    'ho_ten' => 'Khach Hang Chin',
                    'so_dien_thoai' => '0999999999',
                    'email' => 'customer9@example.com',
                    'dia_chi' => '9 Duong Demo, TP.HCM',
                ],
            ])->assertCreated();

            $maHoaDon = (string) $createResponse->json('data.ma_hoa_don');

            Carbon::setTestNow('2026-04-13 10:06:00');

            $this->getJson("/api/khach-hang/hoa-don/{$maHoaDon}/payment-status")
                ->assertOk()
                ->assertJsonPath('success', true)
                ->assertJsonPath('data.ma_hoa_don', $maHoaDon)
                ->assertJsonPath('data.payment_status', 'expired')
                ->assertJsonPath('data.latest_qr_payment_summary.trang_thai', 'expired');
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_customer_payment_status_can_sync_paid_invoice_from_sepay_api_when_webhook_has_not_arrived(): void
    {
        config()->set('payment.sepay.api_token', 'test-sepay-api-token');
        config()->set('payment.sepay.api_base_url', 'https://userapi.sepay.vn/v2');

        $khachHang = $this->createCustomer('1010', 'customer10@example.com', '0901010101');
        $tour = $this->createTour('900110', 10000);
        $lich = $this->createSchedule('500110', $tour->ma_tour, 3, true);

        Sanctum::actingAs($khachHang);

        $createResponse = $this->postJson("/api/khach-hang/tour/{$tour->ma_tour}/thanh-toan/qr", [
            'ma_thoi_gian_tour' => $lich->ma_thoi_gian_tour,
            'thong_tin_nguoi_dat' => [
                'ho_ten' => 'Khach Hang Muoi',
                'so_dien_thoai' => '0901010101',
                'email' => 'customer10@example.com',
                'dia_chi' => '10 Duong Demo, TP.HCM',
            ],
        ])->assertCreated();

        $maHoaDon = (string) $createResponse->json('data.ma_hoa_don');

        Http::fake([
            'https://userapi.sepay.vn/v2/transactions*' => Http::response([
                'status' => 'success',
                'data' => [
                    [
                        'id' => 'a1b2c3d4-e5f6-7890-abcd-ef1234561010',
                        'transaction_date' => '2026-04-13 10:05:00',
                        'account_number' => '0399620355',
                        'va' => null,
                        'transfer_type' => 'in',
                        'amount_in' => 10000,
                        'amount_out' => 0,
                        'accumulated' => 30000,
                        'transaction_content' => "NAP TIEN {$maHoaDon}",
                        'reference_number' => 'MB-REF-10000',
                        'code' => null,
                        'bank_brand_name' => 'MBBank',
                        'bank_account_id' => 'bank-account-uuid-1010',
                        'va_id' => null,
                        'webhook_success' => 0,
                    ],
                ],
                'meta' => [
                    'pagination' => [
                        'total' => 1,
                        'per_page' => 20,
                        'current_page' => 1,
                        'last_page' => 1,
                        'has_more' => false,
                    ],
                ],
            ], 200),
        ]);

        $this->getJson("/api/khach-hang/hoa-don/{$maHoaDon}/payment-status")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.ma_hoa_don', $maHoaDon)
            ->assertJsonPath('data.payment_status', 'paid')
            ->assertJsonPath('data.payment_reference', 'MB-REF-10000');

        Http::assertSent(function ($request) use ($maHoaDon) {
            return $request->url() === "https://userapi.sepay.vn/v2/transactions?q={$maHoaDon}&transfer_type=in&page=1&per_page=20"
                && $request->hasHeader('Authorization', 'Bearer test-sepay-api-token');
        });

        $this->assertDatabaseHas('hoa_don', [
            'ma_hoa_don' => $maHoaDon,
            'payment_status' => 'paid',
            'payment_reference' => 'MB-REF-10000',
            'ma_giao_dich' => 'MB-REF-10000',
        ]);
    }

    public function test_customer_payment_status_does_not_mark_invoice_paid_when_sepay_amount_is_wrong(): void
    {
        config()->set('payment.sepay.api_token', 'test-sepay-api-token');
        config()->set('payment.sepay.api_base_url', 'https://userapi.sepay.vn/v2');

        $khachHang = $this->createCustomer('1011', 'customer11@example.com', '0902020202');
        $tour = $this->createTour('900111', 10000);
        $lich = $this->createSchedule('500111', $tour->ma_tour, 3, true);

        Sanctum::actingAs($khachHang);

        $createResponse = $this->postJson("/api/khach-hang/tour/{$tour->ma_tour}/thanh-toan/qr", [
            'ma_thoi_gian_tour' => $lich->ma_thoi_gian_tour,
            'thong_tin_nguoi_dat' => [
                'ho_ten' => 'Khach Hang Muoi Mot',
                'so_dien_thoai' => '0902020202',
                'email' => 'customer11@example.com',
                'dia_chi' => '11 Duong Demo, TP.HCM',
            ],
        ])->assertCreated();

        $maHoaDon = (string) $createResponse->json('data.ma_hoa_don');

        Http::fake([
            'https://userapi.sepay.vn/v2/transactions*' => Http::response([
                'status' => 'success',
                'data' => [
                    [
                        'id' => 'a1b2c3d4-e5f6-7890-abcd-ef1234561011',
                        'transaction_date' => '2026-04-13 10:06:00',
                        'account_number' => '0399620355',
                        'va' => null,
                        'transfer_type' => 'in',
                        'amount_in' => 9000,
                        'amount_out' => 0,
                        'accumulated' => 39000,
                        'transaction_content' => $maHoaDon,
                        'reference_number' => 'MB-REF-WRONG-AMOUNT',
                        'code' => null,
                        'bank_brand_name' => 'MBBank',
                        'bank_account_id' => 'bank-account-uuid-1011',
                        'va_id' => null,
                        'webhook_success' => 0,
                    ],
                ],
                'meta' => [
                    'pagination' => [
                        'total' => 1,
                        'per_page' => 20,
                        'current_page' => 1,
                        'last_page' => 1,
                        'has_more' => false,
                    ],
                ],
            ], 200),
        ]);

        $this->getJson("/api/khach-hang/hoa-don/{$maHoaDon}/payment-status")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.ma_hoa_don', $maHoaDon)
            ->assertJsonPath('data.payment_status', 'pending');

        $this->assertDatabaseHas('hoa_don', [
            'ma_hoa_don' => $maHoaDon,
            'payment_status' => 'pending',
        ]);

        Http::assertSent(function ($request) use ($maHoaDon) {
            return $request->url() === "https://userapi.sepay.vn/v2/transactions?q={$maHoaDon}&transfer_type=in&page=1&per_page=20"
                && $request->hasHeader('Authorization', 'Bearer test-sepay-api-token');
        });
    }

    public function test_customer_payment_status_does_not_mark_invoice_paid_when_sepay_transaction_lacks_invoice_code(): void
    {
        config()->set('payment.sepay.api_token', 'test-sepay-api-token');
        config()->set('payment.sepay.api_base_url', 'https://userapi.sepay.vn/v2');

        $khachHang = $this->createCustomer('1012', 'customer12@example.com', '0903030303');
        $tour = $this->createTour('900112', 10000);
        $lich = $this->createSchedule('500112', $tour->ma_tour, 3, true);

        Sanctum::actingAs($khachHang);

        $createResponse = $this->postJson("/api/khach-hang/tour/{$tour->ma_tour}/thanh-toan/qr", [
            'ma_thoi_gian_tour' => $lich->ma_thoi_gian_tour,
            'thong_tin_nguoi_dat' => [
                'ho_ten' => 'Khach Hang Muoi Hai',
                'so_dien_thoai' => '0903030303',
                'email' => 'customer12@example.com',
                'dia_chi' => '12 Duong Demo, TP.HCM',
            ],
        ])->assertCreated();

        $maHoaDon = (string) $createResponse->json('data.ma_hoa_don');

        Http::fake([
            'https://userapi.sepay.vn/v2/transactions*' => Http::response([
                'status' => 'success',
                'data' => [
                    [
                        'id' => 'a1b2c3d4-e5f6-7890-abcd-ef1234561012',
                        'transaction_date' => '2026-04-13 10:07:00',
                        'account_number' => '0399620355',
                        'va' => null,
                        'transfer_type' => 'in',
                        'amount_in' => 10000,
                        'amount_out' => 0,
                        'accumulated' => 49000,
                        'transaction_content' => 'NAP TIEN TU DO',
                        'reference_number' => 'MB-REF-NO-CODE',
                        'code' => null,
                        'bank_brand_name' => 'MBBank',
                        'bank_account_id' => 'bank-account-uuid-1012',
                        'va_id' => null,
                        'webhook_success' => 0,
                    ],
                ],
                'meta' => [
                    'pagination' => [
                        'total' => 1,
                        'per_page' => 20,
                        'current_page' => 1,
                        'last_page' => 1,
                        'has_more' => false,
                    ],
                ],
            ], 200),
        ]);

        $this->getJson("/api/khach-hang/hoa-don/{$maHoaDon}/payment-status")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.ma_hoa_don', $maHoaDon)
            ->assertJsonPath('data.payment_status', 'pending');

        $this->assertDatabaseHas('hoa_don', [
            'ma_hoa_don' => $maHoaDon,
            'payment_status' => 'pending',
        ]);

        Http::assertSent(function ($request) use ($maHoaDon) {
            return $request->url() === "https://userapi.sepay.vn/v2/transactions?q={$maHoaDon}&transfer_type=in&page=1&per_page=20"
                && $request->hasHeader('Authorization', 'Bearer test-sepay-api-token');
        });
    }

    private function createCustomer(string $maKhachHang, string $email, string $phone): KhachHang
    {
        return KhachHang::create([
            'Ma_khach_hang' => $maKhachHang,
            'Ho_va_ten' => "Khach {$maKhachHang}",
            'Mat_khau' => Hash::make('123456'),
            'Email' => $email,
            'Ngay_sinh' => '2000-01-01',
            'Gioi_tinh' => true,
            'so_dien_thoai' => $phone,
            'is_block' => false,
        ]);
    }

    private function createPartner(string $maDoiTac, string $email): DoiTac
    {
        return DoiTac::create([
            'ma_doi_tac' => $maDoiTac,
            'ten_doi_tac' => "Doi tac {$maDoiTac}",
            'ten_nguoi_dai_dien' => "Nguoi dai dien {$maDoiTac}",
            'email' => $email,
            'mat_khau' => Hash::make('123456'),
            'is_block' => false,
            'trang_thai_duyet' => 'approved',
        ]);
    }

    private function createAdmin(string $maAdmin, string $email, string $phone): Admin
    {
        return Admin::create([
            'Ma_admin' => $maAdmin,
            'Ho_va_ten' => "Admin {$maAdmin}",
            'Mat_khau' => Hash::make('123456'),
            'Email' => $email,
            'Ngay_sinh' => '1995-01-01',
            'Gioi_tinh' => true,
            'ma_chuc_vu' => 'CV001',
            'is_block' => false,
            'so_dien_thoai' => $phone,
        ]);
    }

    private function createTour(string $maTour, int $gia, ?string $maDoiTac = null): Tour
    {
        return Tour::create([
            'ma_tour' => $maTour,
            'ma_doi_tac' => $maDoiTac,
            'ten_tour' => "Tour {$maTour}",
            'mo_ta' => 'Mo ta test',
            'so_tien' => $gia,
            'so_ngay' => 3,
            'so_nguoi' => 20,
            'nguon_tao' => $maDoiTac ? 'doi_tac' : 'admin',
            'trang_thai_duyet' => 'approved',
            'trang_thai_hien_thi' => true,
        ]);
    }

    private function createSchedule(string $maThoiGianTour, string $maTour, int $soCho, bool $tinhTrang): TourKhoiHanh
    {
        return TourKhoiHanh::create([
            'ma_thoi_gian_tour' => $maThoiGianTour,
            'ma_tour' => $maTour,
            'ngay_bat_dau' => '2026-06-20',
            'ngay_ket_thuc' => '2026-06-22',
            'so_cho' => $soCho,
            'tinh_trang' => $tinhTrang,
        ]);
    }
}
