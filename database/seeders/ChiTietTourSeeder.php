<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChiTietTourSeeder extends Seeder
{
    public function run(): void
    {
        $allLocations = [
            'DN_TQ_001', 'DN_TQ_002', 'DN_TQ_003', 'DN_TQ_004', 'DN_KS_001', 'DN_KS_002', 'DN_NA_001', 'DN_NA_002',
            'HUE_TQ_001', 'HUE_TQ_002', 'HUE_TQ_003', 'HUE_KS_001', 'HUE_KS_002', 'HUE_NA_001', 'HUE_NA_002',
            'HA_TQ_001', 'HA_TQ_002', 'HA_TQ_003', 'HA_KS_001', 'HA_KS_002', 'HA_NA_001', 'HA_NA_002'
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('chi_tiet_tours')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [];
        $maChiTiet = 1;
        
        $tours = DB::table('tours')->get(['ma_tour', 'so_ngay', 'ten_tour']);
        mt_srand(12345);
        
        foreach ($tours as $tour) {
            $locationsForTour = [];
            if (str_contains($tour->ten_tour, 'Đà Nẵng') && str_contains($tour->ten_tour, 'Hội An')) {
                $locationsForTour = array_filter($allLocations, fn($l) => str_starts_with($l, 'DN_') || str_starts_with($l, 'HA_'));
            } elseif (str_contains($tour->ten_tour, 'Huế')) {
                $locationsForTour = array_filter($allLocations, fn($l) => str_starts_with($l, 'HUE_') || str_starts_with($l, 'DN_'));
            } else {
                $locationsForTour = $allLocations;
            }
            $locationsForTour = array_values($locationsForTour);
            
            $locationsCount = $tour->so_ngay * rand(2, 3);
            if ($locationsCount > count($locationsForTour)) {
                $locationsCount = count($locationsForTour);
            }
            
            $keys = (array) array_rand($locationsForTour, $locationsCount);
            // If only 1 item is returned from array_rand, it's not an array, so we ensure it is
            if (!is_array($keys)) {
                $keys = [$keys];
            }
            
            $selectedLocations = array_map(fn($k) => $locationsForTour[$k], $keys);
            
            foreach ($selectedLocations as $index => $loc) {
                $data[] = [
                    'ma_chi_tiet_tour' => 'CT' . str_pad($maChiTiet++, 4, '0', STR_PAD_LEFT),
                    'ma_tour' => $tour->ma_tour,
                    'ma_dia_diem' => $loc,
                    'ngay_hanh_trinh' => (int) floor($index / 3) + 1,
                    'thu_tu_hanh_trinh' => $index + 1,
                    'ghi_chu_hanh_trinh' => "Khám phá địa danh nổi bật trong hành trình.",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        $chunks = array_chunk($data, 100);
        foreach ($chunks as $chunk) {
            DB::table('chi_tiet_tours')->insert($chunk);
        }
    }
}
