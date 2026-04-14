<?php

namespace Database\Seeders;

use App\Models\Tour;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TourKhoiHanhSeeder extends Seeder
{
    public function run(): void
    {
        $tours = Tour::query()
            ->orderBy('ma_tour')
            ->get(['ma_tour', 'so_ngay', 'so_nguoi', 'trang_thai_duyet', 'trang_thai_hien_thi']);

        if ($tours->isEmpty()) {
            return;
        }

        $baseDate = CarbonImmutable::create(2026, 5, 1, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $rows = [];

        foreach ($tours as $tourIndex => $tour) {
            $durationDays = max(2, (int) ($tour->so_ngay ?: 2));
            $capacityBase = max(12, (int) ($tour->so_nguoi ?: 20));

            foreach (range(1, 3) as $sequence) {
                $startDate = $baseDate
                    ->addDays(($tourIndex * 9) + (($sequence - 1) * 11));

                $rows[] = [
                    'ma_thoi_gian_tour' => $this->buildScheduleCode($tourIndex + 1, $sequence),
                    'ma_tour' => $tour->ma_tour,
                    'ngay_bat_dau' => $startDate->toDateString(),
                    'ngay_ket_thuc' => $startDate->addDays($durationDays - 1)->toDateString(),
                    'so_cho' => $capacityBase + (4 * (4 - $sequence)),
                    'tinh_trang' => $sequence !== 3 || ($tour->trang_thai_duyet === 'approved' && (bool) $tour->trang_thai_hien_thi),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('tour_khoi_hanhs')->upsert(
            $rows,
            ['ma_thoi_gian_tour'],
            ['ma_tour', 'ngay_bat_dau', 'ngay_ket_thuc', 'so_cho', 'tinh_trang', 'updated_at']
        );
    }

    private function buildScheduleCode(int $tourIndex, int $sequence): string
    {
        return sprintf('LKH%04d%02d', $tourIndex, $sequence);
    }
}
