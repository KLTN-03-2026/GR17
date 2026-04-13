<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiaDiemSeeder extends Seeder
{
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
                'ma_dia_diem' => '001',
                'ten_dia_diem' => 'Nhà thờ Đức Bà Sài Gòn',
                'loai' => 1,
                'dia_chi' => '1st District, Ho Chi Minh City',
                'sdt' => '0123456789',
                'kinh_do' => 106.6982,
                'vi_do' => 10.7860,
                'gio_mo_cua' => '08:00',
                'gio_dong_cua' => '17:00',
                'gia_giao_dong' => 50000,
                'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Notre_Dame_de_Saigon_%286283733519%29.jpg/800px-Notre_Dame_de_Saigon_%286283733519%29.jpg',
                'mo_ta' => 'Nhà thờ Đức Bà tuyệt đẹp',
                'thoi_gian_tham_quan' => '2 giờ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_dia_diem' => '002',
                'ten_dia_diem' => 'Dinh Độc Lập',
                'loai' => 1,
                'dia_chi' => '135 Nguyen Hue, Ho Chi Minh City',
                'sdt' => '0987654321',
                'kinh_do' => 106.7129,
                'vi_do' => 10.7905,
                'gio_mo_cua' => '07:30',
                'gio_dong_cua' => '17:30',
                'gia_giao_dong' => 40000,
                'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f0/Independence_Palace%2C_Ho_Chi_Minh_City_%285%29.jpg/800px-Independence_Palace%2C_Ho_Chi_Minh_City_%285%29.jpg',
                'mo_ta' => 'Dinh Độc Lập - Tòa nhà lịch sử',
                'thoi_gian_tham_quan' => '1.5 giờ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_dia_diem' => '003',
                'ten_dia_diem' => 'Reverie Saigon Hotel',
                'loai' => 2,
                'dia_chi' => '22-36 Nguyen Hue, Ho Chi Minh City',
                'sdt' => '0912345678',
                'kinh_do' => 106.7135,
                'vi_do' => 10.7910,
                'gio_mo_cua' => '00:00',
                'gio_dong_cua' => '23:59',
                'gia_giao_dong' => 2000000,
                'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e9/Times_Square_HCM.jpg/800px-Times_Square_HCM.jpg',
                'mo_ta' => 'Khách sạn 5 sao sang trọng',
                'thoi_gian_tham_quan' => 'Lưu trú',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_dia_diem' => '004',
                'ten_dia_diem' => 'Nhà hàng Quán Ngon',
                'loai' => 3,
                'dia_chi' => '160 Pasteur, Ho Chi Minh City',
                'sdt' => '0866666666',
                'kinh_do' => 106.7026,
                'vi_do' => 10.7768,
                'gio_mo_cua' => '10:00',
                'gio_dong_cua' => '23:00',
                'gia_giao_dong' => 150000,
                'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/30/B%C3%BAn_Th%E1%BB%8Bt_N%C6%B0%E1%BB%9Bng.jpg/800px-B%C3%BAn_Th%E1%BB%8Bt_N%C6%B0%E1%BB%9Bng.jpg',
                'mo_ta' => 'Nhà hàng ẩm thực Việt Nam',
                'thoi_gian_tham_quan' => '1 giờ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_dia_diem' => '005',
                'ten_dia_diem' => 'Bến Nhân Đảo Ngọc',
                'loai' => 1,
                'dia_chi' => 'District 1, Ho Chi Minh City',
                'sdt' => '0911111111',
                'kinh_do' => 106.7456,
                'vi_do' => 10.7560,
                'gio_mo_cua' => '06:00',
                'gio_dong_cua' => '18:00',
                'gia_giao_dong' => 30000,
                'hinh_anh' => 'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=800&q=80',
                'mo_ta' => 'Bến sông xinh đẹp',
                'thoi_gian_tham_quan' => '1 giờ',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        $places = ['Hà Nội', 'Hồ Chí Minh', 'Đà Nẵng', 'Hội An', 'Nha Trang', 'Đà Lạt', 'Phú Quốc', 'Hạ Long', 'Sapa', 'Huế', 'Ninh Bình', 'Vũng Tàu', 'Mũi Né', 'Quy Nhơn', 'Đồng Hới', 'Mộc Châu', 'Cần Thơ', 'Đảo Nam Du'];
        $types = ['Khách sạn', 'Resort', 'Bãi biển', 'Khu du lịch', 'Nhà hàng', 'Cáp treo', 'Vườn quốc gia'];

        $counter = 6;
        for ($i = 0; $i < 40; $i++) {
            $imgSrc = $faker->randomElement($validImages);
            $type = $faker->randomElement($types);
            $location = $faker->randomElement($places);
            $name = $type . ' ' . $location . ' ' . $faker->lastName;

            $data[] = [
                'ma_dia_diem' => str_pad($counter, 3, '0', STR_PAD_LEFT),
                'ten_dia_diem' => $name,
                'loai' => $faker->numberBetween(1, 4),
                'dia_chi' => $faker->address,
                'sdt' => $faker->numerify('09########'),
                'kinh_do' => $faker->longitude(105, 107),
                'vi_do' => $faker->latitude(10, 11),
                'gio_mo_cua' => '08:00',
                'gio_dong_cua' => '22:00',
                'gia_giao_dong' => $faker->numberBetween(50, 500) * 1000,
                'hinh_anh' => $imgSrc,
                'mo_ta' => 'Điểm đến thú vị và nên được thêm vào lịch trình của bạn.',
                'thoi_gian_tham_quan' => $faker->numberBetween(1, 4) . ' giờ',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $counter++;
        }
        DB::table('dia_diem')->insert($data);
    }
}
