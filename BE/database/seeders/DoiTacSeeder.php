<?php

namespace Database\Seeders;

use App\Models\DoiTac;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoiTacSeeder extends Seeder
{
    public function run(): void
    {
        $doiTacs = [
            [
                'ma_doi_tac' => '1',
                'ten_doi_tac' => 'Công ty Du lịch Sài Gòn',
                'ten_nguoi_dai_dien' => 'Nguyễn Văn An',
                'email' => 'doitac1@example.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0900000001',
                'dia_chi' => 'Quận 1, TP Hồ Chí Minh',
                'is_block' => false,
                'trang_thai_duyet' => 'approved',
            ],
            [
                'ma_doi_tac' => '2',
                'ten_doi_tac' => 'Công ty Lữ hành Miền Nam',
                'ten_nguoi_dai_dien' => 'Trần Thị Bình',
                'email' => 'doitac2@example.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0900000002',
                'dia_chi' => 'Quận 3, TP Hồ Chí Minh',
                'is_block' => false,
                'trang_thai_duyet' => 'approved',
            ],
            [
                'ma_doi_tac' => '3',
                'ten_doi_tac' => 'Công ty Du lịch Hà Nội',
                'ten_nguoi_dai_dien' => 'Lê Quang Huy',
                'email' => 'doitac3@example.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0900000003',
                'dia_chi' => 'Cầu Giấy, Hà Nội',
                'is_block' => false,
                'trang_thai_duyet' => 'approved',
            ],
            [
                'ma_doi_tac' => '4',
                'ten_doi_tac' => 'Công ty Lữ hành Đà Nẵng',
                'ten_nguoi_dai_dien' => 'Phạm Thị Lan',
                'email' => 'doitac4@example.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0900000004',
                'dia_chi' => 'Hải Châu, Đà Nẵng',
                'is_block' => false,
                'trang_thai_duyet' => 'approved',
            ],
            [
                'ma_doi_tac' => '5',
                'ten_doi_tac' => 'Công ty Du lịch Nha Trang',
                'ten_nguoi_dai_dien' => 'Võ Minh Châu',
                'email' => 'doitac5@example.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0900000005',
                'dia_chi' => 'Nha Trang, Khánh Hòa',
                'is_block' => false,
                'trang_thai_duyet' => 'approved',
            ],
            [
                'ma_doi_tac' => '6',
                'ten_doi_tac' => 'Công ty Du lịch Phú Quốc',
                'ten_nguoi_dai_dien' => 'Lý Hữu Bằng',
                'email' => 'doitac6@example.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0900000006',
                'dia_chi' => 'Phú Quốc, Kiên Giang',
                'is_block' => false,
                'trang_thai_duyet' => 'pending',
            ],
            [
                'ma_doi_tac' => '7',
                'ten_doi_tac' => 'Khách sạn Viễn Đông',
                'ten_nguoi_dai_dien' => 'Nguyễn Thị Hoa',
                'email' => 'doitac7@example.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0900000007',
                'dia_chi' => 'Vũng Tàu',
                'is_block' => false,
                'trang_thai_duyet' => 'pending',
            ],
            [
                'ma_doi_tac' => '8',
                'ten_doi_tac' => 'Nhà hàng Ảo Mộng',
                'ten_nguoi_dai_dien' => 'Trương Vô Kỵ',
                'email' => 'doitac8@example.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0900000008',
                'dia_chi' => 'Ảo cảnh, Heaven',
                'is_block' => false,
                'trang_thai_duyet' => 'rejected',
                'ly_do_tu_choi' => 'Thông tin đăng ký không hợp lệ, vui lòng cung cấp giấy phép GPKD.',
            ],
        ];

        foreach ($doiTacs as $doiTac) {
            DoiTac::updateOrCreate(
                ['ma_doi_tac' => $doiTac['ma_doi_tac']],
                $doiTac
            );
        }
    }
}
