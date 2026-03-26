<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiaDiemSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('dia_diem')->insert([
            [
                'ma_dia_diem' => '001',
                'ten_dia_diem' => 'Nhà thờ Đức Bà Sài Gòn',
                'loai' => 1, // Địa điểm du lịch
                'dia_chi' => '1st District, Ho Chi Minh City',
                'sdt' => '0123456789',
                'kinh_do' => 106.6982,
                'vi_do' => 10.7860,
                'gio_mo_cua' => '08:00',
                'gio_dong_cua' => '17:00',
                'gia_giao_dong' => 50000,
                'hinh_anh' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Notre%20dame%20saigon.jpg',
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
                'hinh_anh' => 'https://commons.wikimedia.org/wiki/Special:FilePath/INDEPENCE%20PALACE%20SAIGON%20HO%20CHI%20MINH%20CITY%20VIETNAM%20JAN%202012%20%287041476497%29.jpg',
                'mo_ta' => 'Dinh Độc Lập - Tòa nhà lịch sử',
                'thoi_gian_tham_quan' => '1.5 giờ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_dia_diem' => '003',
                'ten_dia_diem' => 'Reverie Saigon Hotel',
                'loai' => 2, // Khách sạn
                'dia_chi' => '22-36 Nguyen Hue, Ho Chi Minh City',
                'sdt' => '0912345678',
                'kinh_do' => 106.7135,
                'vi_do' => 10.7910,
                'gio_mo_cua' => '00:00',
                'gio_dong_cua' => '23:59',
                'gia_giao_dong' => 2000000,
                'hinh_anh' => 'https://commons.wikimedia.org/wiki/Special:FilePath/T%C3%B2a%20nh%C3%A0%20Times%20Square%2C%202024.jpg',
                'mo_ta' => 'Khách sạn 5 sao sang trọng',
                'thoi_gian_tham_quan' => 'Lưu trú',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_dia_diem' => '004',
                'ten_dia_diem' => 'Nhà hàng Quán Ngon',
                'loai' => 3, // Nhà hàng
                'dia_chi' => '160 Pasteur, Ho Chi Minh City',
                'sdt' => '0866666666',
                'kinh_do' => 106.7026,
                'vi_do' => 10.7768,
                'gio_mo_cua' => '10:00',
                'gio_dong_cua' => '23:00',
                'gia_giao_dong' => 150000,
                'hinh_anh' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Vietnamese%20restaurant%2C%20Nguy%E1%BB%85n%20B%E1%BB%89nh%20Khi%C3%AAm%2C%20district1%2C%20HCMC.JPG',
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
                'hinh_anh' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Ben%20Nha%20Rong%203.JPG',
                'mo_ta' => 'Bến sông xinh đẹp',
                'thoi_gian_tham_quan' => '1 giờ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
