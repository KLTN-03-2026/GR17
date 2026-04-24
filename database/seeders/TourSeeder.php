<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TourSeeder extends Seeder
{
    public function run(): void
    {
        $destinations = [
            [
                'ma_tour' => 'CENT001',
                'ten' => 'Hành trình Di sản Miền Trung (Đà Nẵng - Hội An - Huế)',
                'mo_ta' => 'Hành trình 4 ngày 3 đêm khám phá những vẻ đẹp tinh túy nhất của miền Trung Việt Nam. Từ sự hiện đại của Đà Nẵng, vẻ hoài cổ của Hội An đến nét trầm mặc của cố đô Huế.',
                'hinh_anh' => 'https://vcdn1-dulich.vnecdn.net/2022/06/03/ngu-hanh-son-1-1654247443.jpg',
                'so_tien' => 4500000,
                'so_ngay' => 4,
                'so_nguoi' => 20,
                'ma_tag' => 'TAG01'
            ],
            [
                'ma_tour' => 'CENT002',
                'ten' => 'Đà Nẵng - Thành phố của những cây cầu',
                'mo_ta' => 'Trải nghiệm trọn vẹn vẻ đẹp của Đà Nẵng trong 3 ngày 2 đêm. Tham quan Bà Nà Hills, Bán đảo Sơn Trà và thưởng thức màn trình diễn Cầu Rồng phun lửa.',
                'hinh_anh' => 'https://statics.vinpearl.com/cau-rong-da-nang_1629255799.jpg',
                'so_tien' => 3200000,
                'so_ngay' => 3,
                'so_nguoi' => 15,
                'ma_tag' => 'TAG02'
            ],
            [
                'ma_tour' => 'CENT003',
                'ten' => 'Khám phá Cố đô Huế - Vang bóng một thời',
                'mo_ta' => 'Chuyến đi 1 ngày tìm về lịch sử triều Nguyễn với Đại Nội, các lăng tẩm uy nghi và ngôi chùa Thiên Mụ cổ kính.',
                'hinh_anh' => 'https://khamphahue.com.vn/Portals/0/Images/HinhAnhDiemDen/Hue/CacLangTam/LangKhaiDinh/LangKhaiDinh-01.jpg',
                'so_tien' => 850000,
                'so_ngay' => 1,
                'so_nguoi' => 30,
                'ma_tag' => 'TAG01'
            ],
            [
                'ma_tour' => 'CENT004',
                'ten' => 'Hội An Hoài Cổ & Rừng Dừa Bảy Mẫu',
                'mo_ta' => 'Hành trình 2 ngày 1 đêm đắm mình trong không gian yên bình của phố cổ Hội An và trải nghiệm văn hóa sông nước tại rừng dừa Bảy Mẫu.',
                'hinh_anh' => 'https://statics.vinpearl.com/rung-dua-bay-mau-hoi-an-1_1629452044.jpg',
                'so_tien' => 1950000,
                'so_ngay' => 2,
                'so_nguoi' => 12,
                'ma_tag' => 'TAG05'
            ],
        ];

        $data = array_map(function (array $tour): array {
            return [
                'ma_tour' => $tour['ma_tour'],
                'ten_tour' => $tour['ten'],
                'mo_ta' => $tour['mo_ta'],
                'hinh_anh' => $tour['hinh_anh'],
                'so_tien' => 10000,
                'so_ngay' => $tour['so_ngay'],
                'so_nguoi' => $tour['so_nguoi'],
                'ma_tag' => $tour['ma_tag'],
                'nguon_tao' => 'he_thong',
                'trang_thai_duyet' => 'approved',
                'trang_thai_hien_thi' => true,
                'ly_do_tu_choi' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $destinations);

        DB::table('tours')->upsert(
            $data,
            ['ma_tour'],
            [
                'ten_tour',
                'mo_ta',
                'hinh_anh',
                'so_tien',
                'so_ngay',
                'so_nguoi',
                'ma_tag',
                'nguon_tao',
                'trang_thai_duyet',
                'trang_thai_hien_thi',
                'ly_do_tu_choi',
                'updated_at',
            ]
        );
    }
}
