<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\DoiTac;
use App\Models\KhachHang;
use Database\Seeders\PaymentQrDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentQrDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('payment.bank.bin', '970422');
        config()->set('payment.bank.account_no', '0399620355');
        config()->set('payment.bank.account_name', 'VU HA THAI SON');
        config()->set('payment.vietqr.template', 'PtCU367');
        config()->set('payment.vietqr.client_id', null);
        config()->set('payment.vietqr.api_key', null);
        config()->set('payment.qr_expire_minutes', 5);
        config()->set('payment.sepay.webhook_api_key', 'demo-sepay-key');
    }

    public function test_demo_qr_seeder_creates_visible_qr_data_for_all_roles(): void
    {
        $this->seed(PaymentQrDemoSeeder::class);

        $this->assertDatabaseHas('khach_hang', [
            'Ma_khach_hang' => 'DMOKH001',
            'Email' => 'customer.qr.demo@example.com',
        ]);
        $this->assertDatabaseHas('doi_tac', [
            'ma_doi_tac' => 'DMODT001',
            'email' => 'partner.qr.demo@example.com',
            'trang_thai_duyet' => 'approved',
        ]);
        $this->assertDatabaseHas('tours', [
            'ma_tour' => 'DMT001',
            'ma_doi_tac' => 'DMODT001',
        ]);
        $this->assertDatabaseCount('giao_dich_qr', 4);

        $customer = KhachHang::query()->findOrFail('DMOKH001');
        Sanctum::actingAs($customer);

        $this->getJson('/api/khach-hang/hoa-don')
            ->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJsonFragment([
                'ma_hoa_don' => 'HD99010001',
                'payment_status' => 'pending',
            ])
            ->assertJsonFragment([
                'ma_hoa_don' => 'HD99010002',
                'payment_status' => 'paid',
            ])
            ->assertJsonFragment([
                'ma_hoa_don' => 'HD99010003',
                'payment_status' => 'expired',
            ])
            ->assertJsonFragment([
                'ma_hoa_don' => 'HD99010004',
                'payment_status' => 'failed',
            ]);

        $admin = Admin::query()->findOrFail('DMOADM01');
        Sanctum::actingAs($admin);

        $this->getJson('/api/admin/hoa-don')
            ->assertOk()
            ->assertJsonFragment([
                'ma_hoa_don' => 'HD99010001',
                'payment_method' => 'vietqr_bank_transfer',
            ])
            ->assertJsonFragment([
                'ho_ten' => 'Demo Customer QR',
                'email' => 'customer.qr.demo@example.com',
                'so_dien_thoai' => '0901234567',
            ]);

        $partner = DoiTac::query()->findOrFail('DMODT001');
        Sanctum::actingAs($partner);

        $this->getJson('/api/doi-tac/don-hang')
            ->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJsonFragment([
                'ma_hoa_don' => 'HD99010002',
                'payment_status' => 'paid',
                'ma_tour' => 'DMT001',
            ]);
    }

    public function test_demo_qr_seeder_is_idempotent_for_demo_records(): void
    {
        $this->seed(PaymentQrDemoSeeder::class);
        $this->seed(PaymentQrDemoSeeder::class);

        $this->assertSame(4, \App\Models\HoaDon::query()->whereIn('ma_hoa_don', [
            'HD99010001',
            'HD99010002',
            'HD99010003',
            'HD99010004',
        ])->count());
        $this->assertSame(4, \App\Models\GiaoDichQr::query()->whereIn('ma_hoa_don', [
            'HD99010001',
            'HD99010002',
            'HD99010003',
            'HD99010004',
        ])->count());
        $this->assertSame(1, \App\Models\DoiSoatHoaHong::query()->where('ma_hoa_don', 'HD99010002')->count());
    }
}
