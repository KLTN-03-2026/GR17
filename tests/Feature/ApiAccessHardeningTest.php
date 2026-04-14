<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\Nhom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiAccessHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_customers_cannot_access_admin_invoice_endpoints(): void
    {
        $hoaDon = $this->createInvoice();
        $customer = $this->createCustomer('KH000001', 'customer-one@example.com', '0901000001');

        $this->getJson('/api/admin/hoa-don')
            ->assertStatus(401);

        $this->getJson("/api/admin/hoa-don/{$hoaDon->ma_hoa_don}")
            ->assertStatus(401);

        Sanctum::actingAs($customer);

        $this->getJson('/api/admin/hoa-don')
            ->assertStatus(403);

        $this->getJson("/api/admin/hoa-don/{$hoaDon->ma_hoa_don}")
            ->assertStatus(403);
    }

    public function test_customer_profile_endpoints_require_auth_and_enforce_owner_or_admin_access(): void
    {
        $customerA = $this->createCustomer('KH000001', 'customer-a@example.com', '0901000001');
        $customerB = $this->createCustomer('KH000002', 'customer-b@example.com', '0901000002');
        $admin = $this->createAdmin();

        $this->getJson("/api/khach-hang/profile/{$customerA->Ma_khach_hang}")
            ->assertStatus(401);

        Sanctum::actingAs($customerA);

        $this->getJson("/api/khach-hang/profile/{$customerA->Ma_khach_hang}")
            ->assertOk()
            ->assertJsonPath('data.Ma_khach_hang', $customerA->Ma_khach_hang);

        $this->getJson("/api/khach-hang/profile/{$customerB->Ma_khach_hang}")
            ->assertStatus(403);

        $this->putJson("/api/khach-hang/profile/{$customerB->Ma_khach_hang}", [
            'Ho_va_ten' => 'Changed By Other Customer',
            'Email' => 'customer-b@example.com',
            'Ngay_sinh' => '01/01/2000',
            'Gioi_tinh' => true,
            'so_dien_thoai' => '0901111222',
        ])->assertStatus(403);

        Sanctum::actingAs($admin);

        $this->getJson("/api/khach-hang/profile/{$customerB->Ma_khach_hang}")
            ->assertOk()
            ->assertJsonPath('data.Ma_khach_hang', $customerB->Ma_khach_hang);

        $this->putJson("/api/khach-hang/profile/{$customerB->Ma_khach_hang}", [
            'Ho_va_ten' => 'Updated By Admin',
            'Email' => 'customer-b@example.com',
            'Ngay_sinh' => '01/01/2000',
            'Gioi_tinh' => true,
            'so_dien_thoai' => '0901111222',
        ])->assertOk();

        $this->assertDatabaseHas('khach_hang', [
            'Ma_khach_hang' => $customerB->Ma_khach_hang,
            'Ho_va_ten' => 'Updated By Admin',
        ]);
    }

    public function test_customer_index_requires_authentication(): void
    {
        $this->createCustomer('KH000001', 'customer-a@example.com', '0901000001');

        $this->getJson('/api/khach-hang')
            ->assertStatus(401);
    }

    private function createInvoice(): HoaDon
    {
        Nhom::create([
            'Ma_nhom' => 'NHOMTEST01',
            'ten_nhom' => 'Nhom Test',
        ]);

        return HoaDon::create([
            'ma_hoa_don' => 'HD99990001',
            'ma_nhom' => 'NHOMTEST01',
            'loai_hoa_don' => 0,
            'ma_doi_tuong' => 'TOURTEST01',
            'tong_tien' => 1000000,
            'trang_thai_thanh_toan' => 0,
            'payment_method' => 'vietqr_bank_transfer',
            'payment_status' => 'pending',
            'ngay_tao' => now(),
        ]);
    }

    private function createCustomer(string $maKhachHang, string $email, string $phone): KhachHang
    {
        return KhachHang::create([
            'Ma_khach_hang' => $maKhachHang,
            'Ho_va_ten' => "Customer {$maKhachHang}",
            'Mat_khau' => Hash::make('demo123'),
            'Email' => $email,
            'Ngay_sinh' => '2000-01-01',
            'Gioi_tinh' => true,
            'so_dien_thoai' => $phone,
            'is_block' => true,
        ]);
    }

    private function createAdmin(): Admin
    {
        DB::table('chuc_vu')->insert([
            'ma_chuc_vu' => 'CVHARD01',
            'ten_chuc_vu' => 'Admin Hardening',
            'tinh_trang' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Admin::create([
            'Ma_admin' => 'ADHARD01',
            'Ho_va_ten' => 'Admin Hardening',
            'Mat_khau' => Hash::make('demo123'),
            'Email' => 'admin.hardening@example.com',
            'Ngay_sinh' => '1990-01-01',
            'Gioi_tinh' => true,
            'ma_chuc_vu' => 'CVHARD01',
            'is_block' => false,
            'so_dien_thoai' => '0901000009',
        ]);
    }
}
