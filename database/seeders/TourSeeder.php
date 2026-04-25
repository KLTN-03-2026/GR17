<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TourSeeder extends Seeder
{
    public function run(): void
    {
        $tours = [
            [
                'ma_tour' => 'TDNHA01',
                'ma_doi_tac' => '1',
                'ten' => 'Đà Nẵng - Hội An: Thành phố đáng sống và Phố cổ rêu phong (2 Ngày 1 Đêm)',
                'mo_ta' => 'Khám phá Đà Nẵng với Bán đảo Sơn Trà, Ngũ Hành Sơn, thưởng thức hải sản và dạo bước trong Phố cổ Hội An lung linh đèn lồng.',
                'hinh_anh' => 'https://vcdn1-dulich.vnecdn.net/2022/06/03/ngu-hanh-son-1-1654247443.jpg',
                'so_tien' => 2500000,
                'so_ngay' => 2,
                'so_nguoi' => 15,
                'ma_tag' => 'TAG01' // Du lịch văn hóa
            ],
            [
                'ma_tour' => 'THUE01',
                'ma_doi_tac' => '2',
                'ten' => 'Huế Mộng Mơ: Hành trình Tìm Về Di Sản (1 Ngày)',
                'mo_ta' => 'Trải nghiệm 1 ngày sống trong không gian cung đình triều Nguyễn với Đại Nội, Lăng Khải Định và thưởng thức Bún Bò Huế nức tiếng.',
                'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9c/Hue_Imperial_City_6.jpg/800px-Hue_Imperial_City_6.jpg',
                'so_tien' => 850000,
                'so_ngay' => 1,
                'so_nguoi' => 25,
                'ma_tag' => 'TAG01'
            ],
            [
                'ma_tour' => 'TCENTRAL01',
                'ma_doi_tac' => '1',
                'ten' => 'Hành Trình Di Sản Miền Trung: Đà Nẵng - Huế - Hội An (4 Ngày 3 Đêm)',
                'mo_ta' => 'Chuyến đi hoàn hảo kết nối 3 miền di sản. Từ Bà Nà Hills sương mù, Cố đô Huế cổ kính đến nét lãng mạn của Rừng Dừa Bảy Mẫu.',
                'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Golden_Bridge_in_Ba_Na_Hills_2.jpg/800px-Golden_Bridge_in_Ba_Na_Hills_2.jpg',
                'so_tien' => 5500000,
                'so_ngay' => 4,
                'so_nguoi' => 20,
                'ma_tag' => 'TAG02' // Du lịch nghỉ dưỡng
            ],
            [
                'ma_tour' => 'THA01',
                'ma_doi_tac' => '2',
                'ten' => 'Khám Phá Hội An - Đảo Ngọc Cù Lao Chàm (2 Ngày 1 Đêm)',
                'mo_ta' => 'Lặn ngắm san hô tại Cù Lao Chàm, trải nghiệm thuyền thúng Rừng Dừa và thưởng thức ẩm thực Cơm Gà Hội An.',
                'hinh_anh' => 'https://vcdn1-dulich.vnecdn.net/2022/04/27/cu-lao-cham-1.jpg',
                'so_tien' => 2200000,
                'so_ngay' => 2,
                'so_nguoi' => 12,
                'ma_tag' => 'TAG03' // Du lịch sinh thái
            ],
        ];

        $data = array_map(function (array $tour): array {
            return [
                'ma_tour' => $tour['ma_tour'],
                'ma_doi_tac' => $tour['ma_doi_tac'],
                'ten_tour' => $tour['ten'],
                'mo_ta' => $tour['mo_ta'],
                'hinh_anh' => $tour['hinh_anh'],
                'so_tien' => $tour['so_tien'],
                'so_ngay' => $tour['so_ngay'],
                'so_nguoi' => $tour['so_nguoi'],
                'ma_tag' => $tour['ma_tag'],
                'nguon_tao' => 'doi_tac',
                'trang_thai_duyet' => 'approved',
                'trang_thai_hien_thi' => true,
                'ly_do_tu_choi' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $tours);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('tours')->truncate();
        DB::table('tours')->insert($data);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
