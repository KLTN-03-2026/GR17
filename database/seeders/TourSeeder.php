<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TourSeeder extends Seeder
{
    public function run(): void
    {
        $prefixes = ['Khám Phá', 'Hành Trình', 'Trải Nghiệm', 'Nghỉ Dưỡng', 'Tour Vui Vẻ', 'Tour Tiết Kiệm', 'Vi Vu', 'Thư Giãn Cùng', 'Trọn Vẹn', 'Kỳ Nghỉ'];
        $locations = ['Đà Nẵng', 'Huế', 'Hội An', 'Đà Nẵng - Hội An', 'Huế - Đà Nẵng', 'Đà Nẵng - Bà Nà Hills', 'Hội An - Cù Lao Chàm', 'Huế - Lăng Tẩm', 'Đà Nẵng - Bán Đảo Sơn Trà', 'Huế - Đà Nẵng - Hội An'];
        $suffixes = ['Trọn Gói', 'Cao Cấp', 'Giá Tốt', 'Trong Ngày', '2 Ngày 1 Đêm', '3 Ngày 2 Đêm', '4 Ngày 3 Đêm', 'Mùa Hè', 'Tuyệt Vời', 'Đáng Nhớ'];

        $images = [
            'https://th.bing.com/th/id/OIP.ANcNexyg6MtmWjcLGTaKnwHaE8?w=219&h=182&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3',
            'https://th.bing.com/th/id/OIP.7_6x6wcCNADeRFhpBLVwKAHaEK?w=281&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3',
            'https://th.bing.com/th/id/OIP.ZC365eH-x5qRoycYlJ6OaAHaDt?w=335&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3',
            'https://th.bing.com/th/id/OIP.E5v7g4SZV89G6TFK5P_ihQHaEE?w=335&h=184&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3',
            'https://th.bing.com/th/id/OIP.Jvbo6eGsUdvOKcSOSvsBDgHaE7?w=244&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3',
            'https://th.bing.com/th/id/OIP.-VeJDm4d4pGItJ2dW1sPhwHaEW?w=311&h=182&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3',
            'https://th.bing.com/th/id/OIP.7J-43Oa8c8vB6q4oYd8b_AHaE6?w=278&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3',
            'https://th.bing.com/th/id/OIP.16zHNK7XINYJybO1rp4xLAHaE6?w=221&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3',
            'https://th.bing.com/th/id/OIP.1zq4a7G007iHUBybiLxrTwHaEn?w=243&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3',
            'https://th.bing.com/th/id/OIP.5D3FSgg5q_5q2-MOBJmYSwHaB7?w=317&h=91&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'
        ];

        $tours = [];
        $partners = ['1', '2', '3', '4', '5'];

        // Random seed to ensure different results or consistent ones
        mt_srand(12345);

        for ($i = 1; $i <= 30; $i++) {
            $prefix = $prefixes[array_rand($prefixes)];
            $location = $locations[array_rand($locations)];
            $suffix = $suffixes[array_rand($suffixes)];

            $so_ngay = rand(1, 4);
            if (str_contains($suffix, '2 Ngày'))
                $so_ngay = 2;
            elseif (str_contains($suffix, '3 Ngày'))
                $so_ngay = 3;
            elseif (str_contains($suffix, '4 Ngày'))
                $so_ngay = 4;
            elseif (str_contains($suffix, 'Trong Ngày'))
                $so_ngay = 1;

            $so_tien = rand(8, 55) * 100000;
            if ($so_ngay == 1)
                $so_tien = rand(5, 12) * 100000;

            $tours[] = [
                'ma_tour' => 'T' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'ma_doi_tac' => $partners[($i - 1) % 5],
                'ten' => "$prefix $location $suffix",
                'mo_ta' => "Khám phá nét đẹp của $location với hành trình $so_ngay ngày. Trải nghiệm dịch vụ đẳng cấp, ẩm thực phong phú và những địa danh nổi tiếng bậc nhất miền Trung.",
                'hinh_anh' => $images[array_rand($images)],
                'so_tien' => $so_tien,
                'so_ngay' => $so_ngay,
                'so_nguoi' => rand(10, 30),
                'ma_tag' => 'TAG0' . rand(1, 5)
            ];
        }

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
