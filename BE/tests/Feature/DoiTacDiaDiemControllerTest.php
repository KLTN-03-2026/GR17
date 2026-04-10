<?php

namespace Tests\Feature;

use App\Models\ChiTietTour;
use App\Models\DiaDiem;
use App\Models\DoiTac;
use App\Models\Tour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DoiTacDiaDiemControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_can_create_and_update_own_location(): void
    {
        $doiTac = $this->createPartner('301', 'partner-301@example.com');
        Sanctum::actingAs($doiTac);

        $createResponse = $this->postJson('/api/doi-tac/dia-diem', $this->locationPayload());

        $createResponse
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('is_duplicate', false)
            ->assertJsonPath('data.ma_doi_tac_tao', $doiTac->ma_doi_tac)
            ->assertJsonPath('data.trang_thai_duyet', 'pending_approval');

        $maDiaDiem = (string) $createResponse->json('data.ma_dia_diem');

        $updateResponse = $this->putJson("/api/doi-tac/dia-diem/{$maDiaDiem}", [
            'ten_dia_diem' => 'Điểm đến đối tác đã cập nhật',
            'dia_chi' => '99 Nguyễn Huệ, Quận 1, TP Hồ Chí Minh',
            'kinh_do' => 106.7012,
            'vi_do' => 10.7789,
        ]);

        $updateResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('is_duplicate', false)
            ->assertJsonPath('data.ten_dia_diem', 'Điểm đến đối tác đã cập nhật')
            ->assertJsonPath('data.trang_thai_duyet', 'pending_approval');

        $this->assertDatabaseHas('dia_diem', [
            'ma_dia_diem' => $maDiaDiem,
            'ma_doi_tac_tao' => $doiTac->ma_doi_tac,
            'ten_dia_diem' => 'Điểm đến đối tác đã cập nhật',
            'dia_chi' => '99 Nguyễn Huệ, Quận 1, TP Hồ Chí Minh',
        ]);
    }

    public function test_partner_cannot_view_or_update_location_of_another_partner(): void
    {
        $owner = $this->createPartner('302', 'partner-302@example.com');
        $attacker = $this->createPartner('303', 'partner-303@example.com');

        $diaDiem = DiaDiem::create([
            'ma_dia_diem' => '930001',
            'ma_doi_tac_tao' => $owner->ma_doi_tac,
            'ten_dia_diem' => 'Địa điểm của đối tác khác',
            'ten_dia_diem_normalized' => 'dia diem cua doi tac khac',
            'loai' => 1,
            'dia_chi' => '10 Đường A, TP Hồ Chí Minh',
            'dia_chi_normalized' => '10 duong a tp ho chi minh',
            'kinh_do' => 106.7000000,
            'vi_do' => 10.7700000,
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'approved',
        ]);

        Sanctum::actingAs($attacker);

        $this->getJson("/api/doi-tac/dia-diem/{$diaDiem->ma_dia_diem}")
            ->assertStatus(403)
            ->assertJsonPath('success', false);

        $this->putJson("/api/doi-tac/dia-diem/{$diaDiem->ma_dia_diem}", [
            'ten_dia_diem' => 'Cố sửa dữ liệu không thuộc quyền',
            'dia_chi' => '11 Đường B, TP Hồ Chí Minh',
            'kinh_do' => 106.71,
            'vi_do' => 10.78,
        ])->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    public function test_partner_store_location_returns_duplicate_when_location_already_exists(): void
    {
        $doiTac = $this->createPartner('304', 'partner-304@example.com');
        Sanctum::actingAs($doiTac);

        $payload = $this->locationPayload([
            'ten_dia_diem' => 'Khu du lịch Biển Xanh',
            'dia_chi' => '25 Đường Biển, Vũng Tàu',
        ]);

        $this->postJson('/api/doi-tac/dia-diem', $payload)
            ->assertCreated()
            ->assertJsonPath('is_duplicate', false);

        $duplicateResponse = $this->postJson('/api/doi-tac/dia-diem', $payload);

        $duplicateResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('is_duplicate', true);

        $this->assertSame(1, DiaDiem::query()
            ->where('ten_dia_diem_normalized', 'khu du lich bien xanh')
            ->where('dia_chi_normalized', '25 duong bien vung tau')
            ->count());
    }

    public function test_partner_can_delete_own_location_and_cleanup_itinerary_rows(): void
    {
        $doiTac = $this->createPartner('305', 'partner-305@example.com');
        Sanctum::actingAs($doiTac);

        $diaDiem = DiaDiem::create([
            'ma_dia_diem' => '930101',
            'ma_doi_tac_tao' => $doiTac->ma_doi_tac,
            'ten_dia_diem' => 'Dia diem doi tac can xoa',
            'ten_dia_diem_normalized' => 'dia diem doi tac can xoa',
            'loai' => 1,
            'dia_chi' => '101 Duong Test',
            'dia_chi_normalized' => '101 duong test',
            'kinh_do' => 106.7000000,
            'vi_do' => 10.7700000,
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'approved',
        ]);

        $tour = Tour::create([
            'ma_tour' => '920101',
            'ma_doi_tac' => $doiTac->ma_doi_tac,
            'ten_tour' => 'Tour lien ket dia diem xoa',
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'draft',
            'trang_thai_hien_thi' => false,
        ]);

        ChiTietTour::create([
            'ma_chi_tiet_tour' => '980101',
            'ma_tour' => $tour->ma_tour,
            'ma_dia_diem' => $diaDiem->ma_dia_diem,
            'ngay_hanh_trinh' => 1,
            'thu_tu_hanh_trinh' => 1,
            'ghi_chu_hanh_trinh' => 'Diem se bi xoa',
        ]);

        $this->deleteJson("/api/doi-tac/dia-diem/{$diaDiem->ma_dia_diem}")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('dia_diem', [
            'ma_dia_diem' => $diaDiem->ma_dia_diem,
        ]);
        $this->assertDatabaseMissing('chi_tiet_tours', [
            'ma_chi_tiet_tour' => '980101',
        ]);
    }

    public function test_partner_cannot_delete_location_of_another_partner(): void
    {
        $owner = $this->createPartner('306', 'partner-306@example.com');
        $attacker = $this->createPartner('307', 'partner-307@example.com');

        $diaDiem = DiaDiem::create([
            'ma_dia_diem' => '930102',
            'ma_doi_tac_tao' => $owner->ma_doi_tac,
            'ten_dia_diem' => 'Dia diem khong thuoc quyen xoa',
            'ten_dia_diem_normalized' => 'dia diem khong thuoc quyen xoa',
            'loai' => 1,
            'dia_chi' => '102 Duong Test',
            'dia_chi_normalized' => '102 duong test',
            'kinh_do' => 106.7000000,
            'vi_do' => 10.7700000,
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'pending_approval',
        ]);

        Sanctum::actingAs($attacker);

        $this->deleteJson("/api/doi-tac/dia-diem/{$diaDiem->ma_dia_diem}")
            ->assertStatus(403)
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('dia_diem', [
            'ma_dia_diem' => $diaDiem->ma_dia_diem,
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

    private function locationPayload(array $override = []): array
    {
        return array_merge([
            'ten_dia_diem' => 'Điểm đến đối tác',
            'loai' => 1,
            'dia_chi' => '12 Nguyễn Trãi, Quận 1, TP Hồ Chí Minh',
            'sdt' => '0912345678',
            'kinh_do' => 106.7000000,
            'vi_do' => 10.7700000,
            'gio_mo_cua' => '08:00',
            'gio_dong_cua' => '21:00',
            'gia_giao_dong' => 120000,
            'hinh_anh' => 'https://example.com/location.jpg',
            'mo_ta' => 'Mô tả địa điểm cho đối tác.',
            'thoi_gian_tham_quan' => '2 giờ',
        ], $override);
    }
}
