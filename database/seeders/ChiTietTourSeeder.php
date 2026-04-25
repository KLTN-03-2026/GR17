<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChiTietTourSeeder extends Seeder
{
    public function run(): void
    {
        $itineraries = [
            // ================= Tour 1: TDNHA01 (Đà Nẵng - Hội An 2N1Đ) =================
            // Ngày 1: Đà Nẵng
            ['ma_tour' => 'TDNHA01', 'ma_dia_diem' => 'DN_TQ_001', 'order' => 1], // Bán đảo Sơn Trà
            ['ma_tour' => 'TDNHA01', 'ma_dia_diem' => 'DN_NA_001', 'order' => 2], // Ăn trưa hải sản Bé Mặn
            ['ma_tour' => 'TDNHA01', 'ma_dia_diem' => 'DN_TQ_002', 'order' => 3], // Chiều: Ngũ Hành Sơn
            ['ma_tour' => 'TDNHA01', 'ma_dia_diem' => 'DN_KS_001', 'order' => 4], // Tối: Lưu trú Mường Thanh
            // Ngày 2: Hội An
            ['ma_tour' => 'TDNHA01', 'ma_dia_diem' => 'HA_TQ_001', 'order' => 5], // Sáng: Phố cổ Hội An
            ['ma_tour' => 'TDNHA01', 'ma_dia_diem' => 'HA_NA_002', 'order' => 6], // Trưa: Cơm Gà Bà Buội
            
            // ================= Tour 2: THUE01 (Huế 1 Ngày) =================
            ['ma_tour' => 'THUE01', 'ma_dia_diem' => 'HUE_TQ_001', 'order' => 1], // Sáng: Đại Nội Huế
            ['ma_tour' => 'THUE01', 'ma_dia_diem' => 'HUE_NA_001', 'order' => 2], // Trưa: Bún Bò Huế
            ['ma_tour' => 'THUE01', 'ma_dia_diem' => 'HUE_TQ_002', 'order' => 3], // Chiều: Lăng Khải Định
            ['ma_tour' => 'THUE01', 'ma_dia_diem' => 'HUE_TQ_003', 'order' => 4], // Chiều muộn: Chùa Thiên Mụ

            // ================= Tour 3: TCENTRAL01 (Đà Nẵng - Huế - Hội An 4N3Đ) =================
            // Ngày 1: Đà Nẵng
            ['ma_tour' => 'TCENTRAL01', 'ma_dia_diem' => 'DN_TQ_003', 'order' => 1], // Sáng: Bà Nà Hills
            ['ma_tour' => 'TCENTRAL01', 'ma_dia_diem' => 'DN_NA_002', 'order' => 2], // Trưa: Mì Quảng Bà Mua
            ['ma_tour' => 'TCENTRAL01', 'ma_dia_diem' => 'DN_KS_002', 'order' => 3], // Tối: InterContinental Resort
            // Ngày 2: Huế
            ['ma_tour' => 'TCENTRAL01', 'ma_dia_diem' => 'HUE_TQ_001', 'order' => 4], // Sáng: Đại Nội
            ['ma_tour' => 'TCENTRAL01', 'ma_dia_diem' => 'HUE_NA_002', 'order' => 5], // Trưa: Cơm Niêu Khải Hoàn
            ['ma_tour' => 'TCENTRAL01', 'ma_dia_diem' => 'HUE_KS_001', 'order' => 6], // Tối: Saigon Morin Hotel
            // Ngày 3 & 4: Hội An
            ['ma_tour' => 'TCENTRAL01', 'ma_dia_diem' => 'HA_TQ_002', 'order' => 7], // Sáng: Rừng Dừa Bảy Mẫu
            ['ma_tour' => 'TCENTRAL01', 'ma_dia_diem' => 'HA_KS_001', 'order' => 8], // Lưu trú: Hoi An Historic
            ['ma_tour' => 'TCENTRAL01', 'ma_dia_diem' => 'HA_NA_001', 'order' => 9], // Trưa: Bông Hồng Trắng

            // ================= Tour 4: THA01 (Hội An - Cù Lao Chàm 2N1Đ) =================
            ['ma_tour' => 'THA01', 'ma_dia_diem' => 'HA_TQ_003', 'order' => 1], // Ngày 1: Đảo Cù Lao Chàm
            ['ma_tour' => 'THA01', 'ma_dia_diem' => 'HA_NA_002', 'order' => 2], // Trưa: Cơm Gà Hội An
            ['ma_tour' => 'THA01', 'ma_dia_diem' => 'HA_KS_002', 'order' => 3], // Tối: Mường Thanh Hội An
            ['ma_tour' => 'THA01', 'ma_dia_diem' => 'HA_TQ_002', 'order' => 4], // Ngày 2: Rừng dừa Bảy Mẫu
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('chi_tiet_tours')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [];
        $maChiTiet = 1;
        foreach ($itineraries as $item) {
            $data[] = [
                'ma_chi_tiet_tour' => 'CT' . str_pad($maChiTiet++, 3, '0', STR_PAD_LEFT),
                'ma_tour' => $item['ma_tour'],
                'ma_dia_diem' => $item['ma_dia_diem'],
                'thu_tu_hanh_trinh' => $item['order'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('chi_tiet_tours')->insert($data);
    }
}
