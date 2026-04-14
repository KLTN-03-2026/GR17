<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\DiaDiem;
use App\Models\DoiTac;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DoiTacDiaDiemModerationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_partner_locations_with_filters_and_pagination(): void
    {
        $admin = $this->createAdmin('A901', 'admin-901@example.com');
        $partnerA = $this->createPartner('901', 'partner-901@example.com');
        $partnerB = $this->createPartner('902', 'partner-902@example.com');

        $pending = $this->createLocation([
            'ma_dia_diem' => '990001',
            'ma_doi_tac_tao' => $partnerA->ma_doi_tac,
            'ten_dia_diem' => 'Địa điểm pending miền biển',
            'ten_dia_diem_normalized' => 'dia diem pending mien bien',
            'loai' => 1,
            'dia_chi' => '12 Đường Biển, Vũng Tàu',
            'dia_chi_normalized' => '12 duong bien vung tau',
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'pending_approval',
        ]);

        $approved = $this->createLocation([
            'ma_dia_diem' => '990002',
            'ma_doi_tac_tao' => $partnerB->ma_doi_tac,
            'ten_dia_diem' => 'Địa điểm đã duyệt trung tâm',
            'ten_dia_diem_normalized' => 'dia diem da duyet trung tam',
            'loai' => 2,
            'dia_chi' => '99 Nguyễn Huệ, TP Hồ Chí Minh',
            'dia_chi_normalized' => '99 nguyen hue tp ho chi minh',
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'approved',
        ]);

        $rejected = $this->createLocation([
            'ma_dia_diem' => '990003',
            'ma_doi_tac_tao' => $partnerA->ma_doi_tac,
            'ten_dia_diem' => 'Địa điểm bị từ chối vùng núi',
            'ten_dia_diem_normalized' => 'dia diem bi tu choi vung nui',
            'loai' => 3,
            'dia_chi' => '7 Đường Đèo, Đà Lạt',
            'dia_chi_normalized' => '7 duong deo da lat',
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'rejected',
            'ly_do_tu_choi' => 'Thiếu mô tả chi tiết',
        ]);

        $adminLocation = $this->createLocation([
            'ma_dia_diem' => '990004',
            'ma_doi_tac_tao' => null,
            'ten_dia_diem' => 'Địa điểm hệ thống',
            'ten_dia_diem_normalized' => 'dia diem he thong',
            'loai' => 1,
            'dia_chi' => '1 Đường Hệ Thống',
            'dia_chi_normalized' => '1 duong he thong',
            'nguon_tao' => 'admin',
            'trang_thai_duyet' => 'approved',
        ]);

        Sanctum::actingAs($admin);

        $listResponse = $this->getJson('/api/admin/doi-tac/dia-diem?per_page=10&page=1');

        $listResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total', 3)
            ->assertJsonCount(3, 'data.data');

        $listedIds = collect($listResponse->json('data.data'))->pluck('ma_dia_diem')->all();
        $this->assertContains($pending->ma_dia_diem, $listedIds);
        $this->assertContains($approved->ma_dia_diem, $listedIds);
        $this->assertContains($rejected->ma_dia_diem, $listedIds);
        $this->assertNotContains($adminLocation->ma_dia_diem, $listedIds);

        $filterResponse = $this->getJson('/api/admin/doi-tac/dia-diem?trang_thai_duyet=pending_approval&loai=1&search=miền%20biển');

        $filterResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath('data.data.0.ma_dia_diem', $pending->ma_dia_diem);
    }

    public function test_non_admin_cannot_access_partner_location_moderation_index(): void
    {
        $partner = $this->createPartner('903', 'partner-903@example.com');
        Sanctum::actingAs($partner);

        $this->getJson('/api/admin/doi-tac/dia-diem')
            ->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    public function test_approve_and_reject_still_only_work_for_pending_partner_locations(): void
    {
        $admin = $this->createAdmin('A902', 'admin-902@example.com');
        $partner = $this->createPartner('904', 'partner-904@example.com');

        $pending = $this->createLocation([
            'ma_dia_diem' => '990010',
            'ma_doi_tac_tao' => $partner->ma_doi_tac,
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'pending_approval',
        ]);

        $approved = $this->createLocation([
            'ma_dia_diem' => '990011',
            'ma_doi_tac_tao' => $partner->ma_doi_tac,
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'approved',
        ]);

        Sanctum::actingAs($admin);

        $this->patchJson("/api/admin/doi-tac/dia-diem/{$pending->ma_dia_diem}/approve")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.trang_thai_duyet', 'approved');

        $this->assertDatabaseHas('dia_diem', [
            'ma_dia_diem' => $pending->ma_dia_diem,
            'trang_thai_duyet' => 'approved',
            'ly_do_tu_choi' => null,
        ]);

        $this->patchJson("/api/admin/doi-tac/dia-diem/{$approved->ma_dia_diem}/approve")
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->patchJson("/api/admin/doi-tac/dia-diem/{$approved->ma_dia_diem}/reject", [
            'ly_do_tu_choi' => 'Nội dung cần cập nhật lại',
        ])->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    private function createAdmin(string $maAdmin, string $email): Admin
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
            'so_dien_thoai' => '09' . substr($maAdmin . '12345678', 0, 8),
        ]);
    }

    private function createPartner(string $maDoiTac, string $email): DoiTac
    {
        return DoiTac::create([
            'ma_doi_tac' => $maDoiTac,
            'ten_doi_tac' => "Công ty {$maDoiTac}",
            'ten_nguoi_dai_dien' => "Đại diện {$maDoiTac}",
            'email' => $email,
            'mat_khau' => Hash::make('123456'),
            'is_block' => false,
        ]);
    }

    private function createLocation(array $override = []): DiaDiem
    {
        static $counter = 0;
        $counter++;

        return DiaDiem::create(array_merge([
            'ma_dia_diem' => sprintf('98%04d', $counter),
            'ma_doi_tac_tao' => null,
            'ten_dia_diem' => "Địa điểm {$counter}",
            'ten_dia_diem_normalized' => "dia diem {$counter}",
            'loai' => 1,
            'dia_chi' => "Số {$counter} Đường Test",
            'dia_chi_normalized' => "so {$counter} duong test",
            'sdt' => null,
            'kinh_do' => 106.7000000,
            'vi_do' => 10.7700000,
            'gio_mo_cua' => null,
            'gio_dong_cua' => null,
            'gia_giao_dong' => null,
            'hinh_anh' => null,
            'mo_ta' => null,
            'thoi_gian_tham_quan' => null,
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'pending_approval',
            'ly_do_tu_choi' => null,
        ], $override));
    }
}

