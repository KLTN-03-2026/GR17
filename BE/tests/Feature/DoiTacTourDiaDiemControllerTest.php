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

class DoiTacTourDiaDiemControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_can_manage_itinerary_items_for_own_tour(): void
    {
        $doiTac = $this->createPartner('401', 'partner-401@example.com');
        $tour = $this->createTour($doiTac, '940001');
        $diaDiemCoSan = $this->createLocation($doiTac, '940101', 'Điểm có sẵn', '10 Trần Hưng Đạo, TP Hồ Chí Minh');

        Sanctum::actingAs($doiTac);

        $attachResponse = $this->postJson("/api/doi-tac/tour/{$tour->ma_tour}/dia-diem/existing", [
            'ma_dia_diem' => $diaDiemCoSan->ma_dia_diem,
            'ngay_hanh_trinh' => 1,
            'thu_tu_hanh_trinh' => 1,
            'ghi_chu_hanh_trinh' => 'Điểm dừng đầu tiên',
        ]);

        $attachResponse
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.ma_tour', $tour->ma_tour)
            ->assertJsonPath('data.ma_dia_diem', $diaDiemCoSan->ma_dia_diem)
            ->assertJsonPath('data.ngay_hanh_trinh', 1);

        $maChiTietTour = (string) $attachResponse->json('data.ma_chi_tiet_tour');

        $this->getJson("/api/doi-tac/tour/{$tour->ma_tour}/dia-diem")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');

        $createAndAttachResponse = $this->postJson("/api/doi-tac/tour/{$tour->ma_tour}/dia-diem/create-and-attach", [
            'ten_dia_diem' => 'Điểm mới gắn tour',
            'loai' => 1,
            'dia_chi' => '22 Lê Lợi, TP Hồ Chí Minh',
            'kinh_do' => 106.7023456,
            'vi_do' => 10.7823456,
            'ngay_hanh_trinh' => 2,
            'thu_tu_hanh_trinh' => 2,
            'ghi_chu_hanh_trinh' => 'Điểm dừng thứ hai',
        ]);

        $createAndAttachResponse
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.chi_tiet_tour.ma_tour', $tour->ma_tour)
            ->assertJsonPath('data.chi_tiet_tour.ngay_hanh_trinh', 2)
            ->assertJsonPath('data.chi_tiet_tour.thu_tu_hanh_trinh', 2);

        $this->putJson("/api/doi-tac/tour/{$tour->ma_tour}/dia-diem/{$maChiTietTour}", [
            'ngay_hanh_trinh' => 3,
            'thu_tu_hanh_trinh' => 3,
            'ghi_chu_hanh_trinh' => 'Đổi thứ tự hành trình',
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.ngay_hanh_trinh', 3)
            ->assertJsonPath('data.thu_tu_hanh_trinh', 3);

        $this->deleteJson("/api/doi-tac/tour/{$tour->ma_tour}/dia-diem/{$maChiTietTour}")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('chi_tiet_tours', [
            'ma_chi_tiet_tour' => $maChiTietTour,
            'ma_tour' => $tour->ma_tour,
        ]);
    }

    public function test_partner_cannot_manage_itinerary_of_other_partner_tour(): void
    {
        $owner = $this->createPartner('402', 'partner-402@example.com');
        $attacker = $this->createPartner('403', 'partner-403@example.com');

        $tour = $this->createTour($owner, '940002');
        $diaDiem = $this->createLocation($owner, '940102', 'Điểm của chủ tour', '50 Nguyễn Huệ, TP Hồ Chí Minh');
        $chiTiet = ChiTietTour::create([
            'ma_tour' => $tour->ma_tour,
            'ma_dia_diem' => $diaDiem->ma_dia_diem,
            'ngay_hanh_trinh' => 1,
            'thu_tu_hanh_trinh' => 1,
            'ghi_chu_hanh_trinh' => 'Điểm của đối tác sở hữu',
        ]);

        Sanctum::actingAs($attacker);

        $this->getJson("/api/doi-tac/tour/{$tour->ma_tour}/dia-diem")
            ->assertStatus(403)
            ->assertJsonPath('success', false);

        $this->postJson("/api/doi-tac/tour/{$tour->ma_tour}/dia-diem/existing", [
            'ma_dia_diem' => $diaDiem->ma_dia_diem,
            'ngay_hanh_trinh' => 1,
            'thu_tu_hanh_trinh' => 2,
        ])->assertStatus(403)
            ->assertJsonPath('success', false);

        $this->putJson("/api/doi-tac/tour/{$tour->ma_tour}/dia-diem/{$chiTiet->ma_chi_tiet_tour}", [
            'ngay_hanh_trinh' => 1,
            'thu_tu_hanh_trinh' => 5,
        ])->assertStatus(403)
            ->assertJsonPath('success', false);

        $this->deleteJson("/api/doi-tac/tour/{$tour->ma_tour}/dia-diem/{$chiTiet->ma_chi_tiet_tour}")
            ->assertStatus(403)
            ->assertJsonPath('success', false);
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

    private function createTour(DoiTac $doiTac, string $maTour): Tour
    {
        return Tour::create([
            'ma_tour' => $maTour,
            'ma_doi_tac' => $doiTac->ma_doi_tac,
            'ten_tour' => "Tour {$maTour}",
            'mo_ta' => 'Tour dùng để test hành trình đối tác',
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'draft',
            'trang_thai_hien_thi' => false,
        ]);
    }

    private function createLocation(DoiTac $doiTac, string $maDiaDiem, string $tenDiaDiem, string $diaChi): DiaDiem
    {
        return DiaDiem::create([
            'ma_dia_diem' => $maDiaDiem,
            'ma_doi_tac_tao' => $doiTac->ma_doi_tac,
            'ten_dia_diem' => $tenDiaDiem,
            'ten_dia_diem_normalized' => strtolower($tenDiaDiem),
            'loai' => 1,
            'dia_chi' => $diaChi,
            'dia_chi_normalized' => strtolower($diaChi),
            'kinh_do' => 106.7000000,
            'vi_do' => 10.7700000,
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'approved',
        ]);
    }
}
