<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                                $faker = \Faker\Factory::create('vi_VN');
        $validImages = [
            'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/H%E1%BA%A1_Long_Bay_viewed_from_Ti_T%E1%BB%91p_Island.jpg/800px-H%E1%BA%A1_Long_Bay_viewed_from_Ti_T%E1%BB%91p_Island.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Golden_Bridge_in_Ba_Na_Hills_2.jpg/800px-Golden_Bridge_in_Ba_Na_Hills_2.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/7/75/Hoi_An_lanterns_at_night.jpg/800px-Hoi_An_lanterns_at_night.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/a/af/Terraced_rice_fields_in_Mu_Cang_Chai%2C_Yen_Bai%2C_Vietnam.jpg/800px-Terraced_rice_fields_in_Mu_Cang_Chai%2C_Yen_Bai%2C_Vietnam.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a7/HoanKiemLake.jpg/800px-HoanKiemLake.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ea/Nha_Trang_beach.jpg/800px-Nha_Trang_beach.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Tam_Coc%2C_Ninh_Binh.jpg/800px-Tam_Coc%2C_Ninh_Binh.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9c/Hue_Imperial_City_6.jpg/800px-Hue_Imperial_City_6.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/View_of_Da_Lat_from_Lang_Biang.jpg/800px-View_of_Da_Lat_from_Lang_Biang.jpg',
            'https://images.unsplash.com/photo-1540541338-2c8115682650?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=800&q=80'
        ];

        $data = [
            [
                'ma_tour' => '001',
                'ten_tour' => 'Tour Du Lịch Phú Quốc 3N2Đ',
                'mo_ta' => 'Khám phá ngọc đảo Phú Quốc với nhiều khuyến mãi',
                'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/77/Phu_Quoc_Island_Beach.jpg/800px-Phu_Quoc_Island_Beach.jpg',
                'so_tien' => 4500000,
                'so_ngay' => 3,
                'so_nguoi' => 20,
                'ma_tag' => 'TAG01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_tour' => '002',
                'ten_tour' => 'Tour Biển Đà Nẵng 4N3Đ',
                'mo_ta' => 'Nghỉ dưỡng 5 sao tại bãi biển Mỹ Khê',
                'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Golden_Bridge_in_Ba_Na_Hills_2.jpg/800px-Golden_Bridge_in_Ba_Na_Hills_2.jpg',
                'so_tien' => 6000000,
                'so_ngay' => 4,
                'so_nguoi' => 15,
                'ma_tag' => 'TAG02',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        $places = ['Hà Nội', 'Hồ Chí Minh', 'Đà Nẵng', 'Hội An', 'Nha Trang', 'Đà Lạt', 'Phú Quốc', 'Hạ Long', 'Sapa', 'Huế', 'Ninh Bình', 'Vũng Tàu', 'Mũi Né', 'Quy Nhơn', 'Đồng Hới', 'Mộc Châu', 'Cần Thơ', 'Đảo Nam Du'];

        for ($i = 3; $i <= 17; $i++) {
            $imgSrc = $faker->randomElement($validImages);
            $location = $faker->randomElement($places);
            $data[] = [
                'ma_tour' => str_pad($i, 3, '0', STR_PAD_LEFT),
                'ten_tour' => 'Tour Khám Phá ' . $location . ' ' . $faker->numberBetween(2, 5) . 'N' . $faker->numberBetween(1, 4) . 'Đ',
                'mo_ta' => 'Trải nghiệm du lịch tuyệt vời ở ' . $location . ' cùng chúng tôi. Lịch trình hấp dẫn.',
                'hinh_anh' => $imgSrc,
                'so_tien' => $faker->numberBetween(10, 100) * 100000,
                'so_ngay' => $faker->numberBetween(2, 5),
                'so_nguoi' => $faker->numberBetween(10, 40),
                'ma_tag' => 'TAG' . str_pad($faker->numberBetween(1, 5), 2, '0', STR_PAD_LEFT),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('tours')->insert($data);
    }
}
