<?php

namespace Database\Seeders;

use App\Models\HoaDon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HoaDonSeeder extends Seeder
{
    public function run(): void
    {
        $hoaDonData = [
            [
                'ma_hoa_don' => '001',
                'ma_nhom' => '001',
                'loai_hoa_don' => 0,
                'ma_doi_tuong' => '001',
                'tong_tien' => 5000000,
                'trang_thai_thanh_toan' => 1,
                'ma_giao_dich' => 'TX12345',
                'ngay_tao' => now()->subDays(5),
            ],
            [
                'ma_hoa_don' => '002',
                'ma_nhom' => '001',
                'loai_hoa_don' => 1,
                'ma_doi_tuong' => '001', // Example group plan
                'tong_tien' => 800000,
                'trang_thai_thanh_toan' => 0,
                'ma_giao_dich' => null,
                'ngay_tao' => now()->subDays(4),
            ],
            [
                'ma_hoa_don' => '003',
                'ma_nhom' => '002',
                'loai_hoa_don' => 0,
                'ma_doi_tuong' => '002',
                'tong_tien' => 12000000,
                'trang_thai_thanh_toan' => 1,
                'ma_giao_dich' => 'TX98765',
                'ngay_tao' => now()->subDays(3),
            ]
        ];

        foreach ($hoaDonData as $data) {
            HoaDon::firstOrCreate(
                ['ma_hoa_don' => $data['ma_hoa_don']],
                [
                    'ma_nhom' => $data['ma_nhom'],
                    'loai_hoa_don' => $data['loai_hoa_don'],
                    'ma_doi_tuong' => $data['ma_doi_tuong'],
                    'tong_tien' => $data['tong_tien'],
                    'trang_thai_thanh_toan' => $data['trang_thai_thanh_toan'],
                    'ma_giao_dich' => $data['ma_giao_dich'],
                    'ngay_tao' => $data['ngay_tao'],
                ]
            );
        }
    }
}
