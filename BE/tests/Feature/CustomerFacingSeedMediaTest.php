<?php

namespace Tests\Feature;

use App\Models\DiaDiem;
use App\Models\DoiTac;
use App\Models\Tour;
use Database\Seeders\DoiTacSampleDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerFacingSeedMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_sample_seeder_uses_curated_images_for_customer_facing_records(): void
    {
        DoiTac::create([
            'ma_doi_tac' => 'DT900',
            'ten_doi_tac' => 'Đối tác demo',
            'ten_nguoi_dai_dien' => 'Đại diện demo',
            'email' => 'partner-media-demo@example.com',
            'mat_khau' => Hash::make('123456'),
            'is_block' => false,
            'trang_thai_duyet' => 'approved',
        ]);

        $this->seed(DoiTacSampleDataSeeder::class);

        $approvedLocations = DiaDiem::query()
            ->where('ma_doi_tac_tao', 'DT900')
            ->where('trang_thai_duyet', 'approved')
            ->pluck('hinh_anh');

        $visibleTours = Tour::query()
            ->where('ma_doi_tac', 'DT900')
            ->where('trang_thai_duyet', 'approved')
            ->where('trang_thai_hien_thi', true)
            ->pluck('hinh_anh');

        $this->assertNotEmpty($approvedLocations, 'Phải có địa điểm approved để kiểm tra ảnh hiển thị.');
        $this->assertNotEmpty($visibleTours, 'Phải có tour visible để kiểm tra ảnh hiển thị.');

        $approvedLocations->each(function (?string $url): void {
            $this->assertNotNull($url);
            $this->assertStringNotContainsString('picsum.photos', $url);
        });

        $visibleTours->each(function (?string $url): void {
            $this->assertNotNull($url);
            $this->assertStringNotContainsString('picsum.photos', $url);
        });
    }
}
