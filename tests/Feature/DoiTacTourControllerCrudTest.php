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

class DoiTacTourControllerCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_can_create_update_and_delete_own_tour_with_itinerary_cleanup(): void
    {
        $doiTac = $this->createPartner('401', 'partner-401@example.com');
        Sanctum::actingAs($doiTac);

        $createResponse = $this->postJson('/api/doi-tac/tour', [
            'ten_tour' => 'Tour doi tac moi',
            'mo_ta' => 'Mo ta tour',
            'so_tien' => 1200000,
            'so_ngay' => 2,
            'so_nguoi' => 10,
            'ma_tag' => 'TAG001',
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.ma_doi_tac', $doiTac->ma_doi_tac)
            ->assertJsonPath('data.nguon_tao', 'doi_tac')
            ->assertJsonPath('data.trang_thai_duyet', 'draft')
            ->assertJsonPath('data.trang_thai_hien_thi', false);

        $maTour = (string) $createResponse->json('data.ma_tour');

        $updateResponse = $this->putJson("/api/doi-tac/tour/{$maTour}", [
            'ten_tour' => 'Tour doi tac da sua',
            'so_ngay' => 3,
            'so_nguoi' => 15,
        ]);

        $updateResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.ten_tour', 'Tour doi tac da sua')
            ->assertJsonPath('data.trang_thai_duyet', 'draft')
            ->assertJsonPath('data.trang_thai_hien_thi', false);

        $diaDiem = DiaDiem::create([
            'ma_dia_diem' => '940001',
            'ma_doi_tac_tao' => $doiTac->ma_doi_tac,
            'ten_dia_diem' => 'Dia diem tour xoa',
            'ten_dia_diem_normalized' => 'dia diem tour xoa',
            'loai' => 1,
            'dia_chi' => '1 Duong Test',
            'dia_chi_normalized' => '1 duong test',
            'kinh_do' => 106.7000000,
            'vi_do' => 10.7700000,
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'approved',
        ]);

        ChiTietTour::create([
            'ma_chi_tiet_tour' => '970001',
            'ma_tour' => $maTour,
            'ma_dia_diem' => $diaDiem->ma_dia_diem,
            'ngay_hanh_trinh' => 1,
            'thu_tu_hanh_trinh' => 1,
            'ghi_chu_hanh_trinh' => 'Diem test',
        ]);

        $deleteResponse = $this->deleteJson("/api/doi-tac/tour/{$maTour}");

        $deleteResponse
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('tours', [
            'ma_tour' => $maTour,
        ]);
        $this->assertDatabaseMissing('chi_tiet_tours', [
            'ma_chi_tiet_tour' => '970001',
        ]);
    }

    public function test_partner_cannot_delete_tour_of_another_partner(): void
    {
        $owner = $this->createPartner('402', 'partner-402@example.com');
        $attacker = $this->createPartner('403', 'partner-403@example.com');

        $tour = Tour::create([
            'ma_tour' => '910003',
            'ma_doi_tac' => $owner->ma_doi_tac,
            'ten_tour' => 'Tour khong thuoc quyen',
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'approved',
            'trang_thai_hien_thi' => true,
        ]);

        Sanctum::actingAs($attacker);

        $this->deleteJson("/api/doi-tac/tour/{$tour->ma_tour}")
            ->assertStatus(403)
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('tours', [
            'ma_tour' => $tour->ma_tour,
        ]);
    }

    private function createPartner(string $maDoiTac, string $email): DoiTac
    {
        return DoiTac::create([
            'ma_doi_tac' => $maDoiTac,
            'ten_doi_tac' => "Cong ty {$maDoiTac}",
            'ten_nguoi_dai_dien' => "Dai dien {$maDoiTac}",
            'email' => $email,
            'mat_khau' => Hash::make('123456'),
            'is_block' => false,
        ]);
    }
}
