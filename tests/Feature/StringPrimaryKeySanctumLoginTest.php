<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\DoiTac;
use App\Models\KhachHang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StringPrimaryKeySanctumLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_string_primary_key_accounts_can_login_and_receive_sanctum_tokens(): void
    {
        DB::table('chuc_vu')->insert([
            'ma_chuc_vu' => 'CVTST01',
            'ten_chuc_vu' => 'Test Role',
            'tinh_trang' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        KhachHang::create([
            'Ma_khach_hang' => 'KHTEST01',
            'Ho_va_ten' => 'Customer Token Test',
            'Mat_khau' => Hash::make('demo123'),
            'Email' => 'token.customer@example.com',
            'Ngay_sinh' => '2000-01-01',
            'Gioi_tinh' => true,
            'so_dien_thoai' => '0901000001',
            'is_block' => true,
        ]);

        DoiTac::create([
            'ma_doi_tac' => 'DTTEST01',
            'ten_doi_tac' => 'Partner Token Test',
            'ten_nguoi_dai_dien' => 'Partner Owner',
            'email' => 'token.partner@example.com',
            'mat_khau' => Hash::make('demo123'),
            'so_dien_thoai' => '0901000002',
            'dia_chi' => 'Quan 1, TP.HCM',
            'is_block' => false,
            'trang_thai_duyet' => 'approved',
        ]);

        Admin::create([
            'Ma_admin' => 'ADTEST01',
            'Ho_va_ten' => 'Admin Token Test',
            'Mat_khau' => Hash::make('demo123'),
            'Email' => 'token.admin@example.com',
            'Ngay_sinh' => '1995-01-01',
            'Gioi_tinh' => true,
            'ma_chuc_vu' => 'CVTST01',
            'is_block' => false,
            'so_dien_thoai' => '0901000003',
        ]);

        $this->postJson('/api/khach-hang/login', [
            'Email' => 'token.customer@example.com',
            'Mat_khau' => 'demo123',
        ])
            ->assertOk()
            ->assertJsonStructure(['token', 'user' => ['Ma_khach_hang', 'Email']]);

        $this->postJson('/api/doi-tac/login', [
            'email' => 'token.partner@example.com',
            'mat_khau' => 'demo123',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token']]);

        $this->postJson('/api/admin/login', [
            'email' => 'token.admin@example.com',
            'Mat_khau' => 'demo123',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token']]);
    }
}
