<?php

namespace Tests\Feature;

use App\Models\DoiTac;
use App\Models\Tour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DoiTacTourSubmitTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_can_submit_draft_tour_to_pending_approval(): void
    {
        $doiTac = DoiTac::create([
            'ma_doi_tac' => '101',
            'ten_doi_tac' => 'Công ty A',
            'ten_nguoi_dai_dien' => 'Đại diện A',
            'email' => 'partner-a@example.com',
            'mat_khau' => Hash::make('123456'),
            'is_block' => false,
        ]);

        Tour::create([
            'ma_tour' => '900001',
            'ma_doi_tac' => $doiTac->ma_doi_tac,
            'ten_tour' => 'Tour bản nháp',
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'draft',
            'trang_thai_hien_thi' => false,
        ]);

        Sanctum::actingAs($doiTac);

        $response = $this->postJson('/api/doi-tac/tour/900001/submit');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.trang_thai_duyet', 'pending_approval')
            ->assertJsonPath('data.trang_thai_hien_thi', false);

        $this->assertDatabaseHas('tours', [
            'ma_tour' => '900001',
            'trang_thai_duyet' => 'pending_approval',
            'trang_thai_hien_thi' => 0,
        ]);
    }

    public function test_partner_can_resubmit_rejected_tour_to_pending_approval(): void
    {
        $doiTac = DoiTac::create([
            'ma_doi_tac' => '102',
            'ten_doi_tac' => 'Công ty B',
            'ten_nguoi_dai_dien' => 'Đại diện B',
            'email' => 'partner-b@example.com',
            'mat_khau' => Hash::make('123456'),
            'is_block' => false,
        ]);

        Tour::create([
            'ma_tour' => '900002',
            'ma_doi_tac' => $doiTac->ma_doi_tac,
            'ten_tour' => 'Tour bị từ chối',
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'rejected',
            'trang_thai_hien_thi' => false,
            'ly_do_tu_choi' => 'Thiếu thông tin',
        ]);

        Sanctum::actingAs($doiTac);

        $response = $this->postJson('/api/doi-tac/tour/900002/submit');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.trang_thai_duyet', 'pending_approval')
            ->assertJsonPath('data.trang_thai_hien_thi', false);

        $this->assertDatabaseHas('tours', [
            'ma_tour' => '900002',
            'trang_thai_duyet' => 'pending_approval',
            'trang_thai_hien_thi' => 0,
        ]);
    }

    public function test_partner_cannot_submit_tour_of_another_partner(): void
    {
        $owner = DoiTac::create([
            'ma_doi_tac' => '103',
            'ten_doi_tac' => 'Công ty Chủ sở hữu',
            'ten_nguoi_dai_dien' => 'Đại diện Chủ sở hữu',
            'email' => 'partner-owner@example.com',
            'mat_khau' => Hash::make('123456'),
            'is_block' => false,
        ]);

        $attacker = DoiTac::create([
            'ma_doi_tac' => '104',
            'ten_doi_tac' => 'Công ty Không sở hữu',
            'ten_nguoi_dai_dien' => 'Đại diện Không sở hữu',
            'email' => 'partner-attacker@example.com',
            'mat_khau' => Hash::make('123456'),
            'is_block' => false,
        ]);

        Tour::create([
            'ma_tour' => '900003',
            'ma_doi_tac' => $owner->ma_doi_tac,
            'ten_tour' => 'Tour của đối tác khác',
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'draft',
            'trang_thai_hien_thi' => false,
        ]);

        Sanctum::actingAs($attacker);

        $response = $this->postJson('/api/doi-tac/tour/900003/submit');

        $response
            ->assertStatus(403)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Bạn không có quyền gửi duyệt tour này');

        $this->assertDatabaseHas('tours', [
            'ma_tour' => '900003',
            'trang_thai_duyet' => 'draft',
        ]);
    }
}
