<?php

namespace Tests\Feature;

use App\Models\KhachHang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LocalizationMessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_auth_and_invoice_messages_are_returned_with_diacritics(): void
    {
        $registerPayload = [
            'Ho_va_ten' => 'Nguyễn Văn Demo',
            'Mat_khau' => 'demo12345',
            'Email' => 'dangky@example.com',
            'Ngay_sinh' => '01/01/2000',
            'Gioi_tinh' => true,
            'so_dien_thoai' => '0912345678',
        ];

        $this->postJson('/api/khach-hang/register', $registerPayload)
            ->assertCreated()
            ->assertJsonPath('message', 'Đăng ký thành công');

        $this->postJson('/api/khach-hang/login', [
            'Email' => 'khong-ton-tai@example.com',
            'Mat_khau' => 'sai-mat-khau',
        ])
            ->assertStatus(401)
            ->assertJsonPath('message', 'Thông tin đăng nhập không chính xác');

        $khachHang = KhachHang::query()->create([
            'Ma_khach_hang' => 'KHLOC001',
            'Ho_va_ten' => 'Khách Hàng Kiểm Thử',
            'Mat_khau' => Hash::make('demo12345'),
            'Email' => 'khachhang@example.com',
            'Ngay_sinh' => '2000-01-01',
            'Gioi_tinh' => true,
            'so_dien_thoai' => '0912345679',
            'is_block' => true,
        ]);

        Sanctum::actingAs($khachHang);

        $this->getJson('/api/khach-hang/hoa-don')
            ->assertOk()
            ->assertJsonPath('message', 'Lấy danh sách hóa đơn thành công');
    }
}
