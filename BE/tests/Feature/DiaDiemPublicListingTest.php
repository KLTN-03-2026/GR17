<?php

namespace Tests\Feature;

use App\Models\DiaDiem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiaDiemPublicListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_locations_accept_per_page_query_up_to_cap(): void
    {
        foreach (range(1, 12) as $index) {
            DiaDiem::create([
                'ma_dia_diem' => sprintf('DD%03d', $index),
                'ten_dia_diem' => "Địa điểm {$index}",
                'ten_dia_diem_normalized' => "dia diem {$index}",
                'loai' => 1,
                'dia_chi' => "Số {$index} Đường Test, Việt Nam",
                'dia_chi_normalized' => "so {$index} duong test viet nam",
                'kinh_do' => 106.7 + ($index / 1000),
                'vi_do' => 10.7 + ($index / 1000),
                'hinh_anh' => 'https://example.com/location.jpg',
                'mo_ta' => 'Mô tả địa điểm test',
                'thoi_gian_tham_quan' => '2 giờ',
                'nguon_tao' => 'he_thong',
                'trang_thai_duyet' => 'approved',
            ]);
        }

        $this->getJson('/api/dia-diem?per_page=100')
            ->assertOk()
            ->assertJsonPath('data.per_page', 100)
            ->assertJsonCount(12, 'data.data');
    }

    public function test_public_locations_cap_per_page_to_one_hundred(): void
    {
        foreach (range(1, 3) as $index) {
            DiaDiem::create([
                'ma_dia_diem' => sprintf('DP%03d', $index),
                'ten_dia_diem' => "Điểm {$index}",
                'ten_dia_diem_normalized' => "diem {$index}",
                'loai' => 1,
                'dia_chi' => "Địa chỉ {$index}",
                'dia_chi_normalized' => "dia chi {$index}",
                'kinh_do' => 106.7 + ($index / 1000),
                'vi_do' => 10.7 + ($index / 1000),
                'hinh_anh' => 'https://example.com/location.jpg',
                'mo_ta' => 'Mô tả địa điểm test',
                'thoi_gian_tham_quan' => '2 giờ',
                'nguon_tao' => 'he_thong',
                'trang_thai_duyet' => 'approved',
            ]);
        }

        $this->getJson('/api/dia-diem?per_page=500')
            ->assertOk()
            ->assertJsonPath('data.per_page', 100);
    }
}
