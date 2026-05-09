<?php

namespace Tests\Feature;

use App\Models\DoiTac;
use App\Models\Tour;
use App\Models\TourKhoiHanh;
use Database\Seeders\DoiTacSampleDataSeeder;
use Database\Seeders\TourKhoiHanhSeeder;
use Database\Seeders\TourSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TourKhoiHanhSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_facing_tours_receive_multiple_departure_schedules(): void
    {
        $this->seed(TourSeeder::class);

        DoiTac::create([
            'ma_doi_tac' => 'DT901',
            'ten_doi_tac' => 'Đối tác lịch khởi hành',
            'ten_nguoi_dai_dien' => 'Đại diện lịch khởi hành',
            'email' => 'partner-schedule-demo@example.com',
            'mat_khau' => Hash::make('123456'),
            'is_block' => false,
            'trang_thai_duyet' => 'approved',
        ]);

        $this->seed(DoiTacSampleDataSeeder::class);
        $this->seed(TourKhoiHanhSeeder::class);

        $publicTours = Tour::query()
            ->where('trang_thai_duyet', 'approved')
            ->where('trang_thai_hien_thi', true)
            ->pluck('ma_tour');

        $this->assertNotEmpty($publicTours, 'Phải có tour công khai để kiểm tra lịch khởi hành.');

        foreach ($publicTours as $maTour) {
            $count = TourKhoiHanh::query()
                ->where('ma_tour', $maTour)
                ->count();

            $this->assertGreaterThanOrEqual(
                3,
                $count,
                "Tour {$maTour} phải có ít nhất 3 lịch khởi hành để hiển thị đầy đủ."
            );
        }
    }
}
