<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run()
    {

        $admins = [
            [
                'Ho_va_ten' => 'Trần Quản Trị',
                'Mat_khau' => Hash::make('111111'),
                'Email' => 'admin@example.com',
                'Ngay_sinh' => '1990-01-01',
                'Gioi_tinh' => 1,
                'ma_chuc_vu' => '001',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456780',
                'IsAdmin' => 1,
            ],
            [
                'Ho_va_ten' => 'Nguyễn Thế Vinh',
                'Mat_khau' => Hash::make('password123'),
                'Email' => 'vinh.nguyen@example.com',
                'Ngay_sinh' => '1991-02-01',
                'Gioi_tinh' => 1,
                'ma_chuc_vu' => '002',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456781',
                'IsAdmin' => 0,
            ],
            [
                'Ho_va_ten' => 'Phạm Thị Mai Trang',
                'Mat_khau' => Hash::make('password123'),
                'Email' => 'trang.pham@example.com',
                'Ngay_sinh' => '1992-03-01',
                'Gioi_tinh' => 0,
                'ma_chuc_vu' => '003',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456782',
                'IsAdmin' => 0,
            ],
            [
                'Ho_va_ten' => 'Hoàng Ngọc Ánh',
                'Mat_khau' => Hash::make('password123'),
                'Email' => 'anh.hoang@example.com',
                'Ngay_sinh' => '1993-04-01',
                'Gioi_tinh' => 0,
                'ma_chuc_vu' => '004',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456783',
                'IsAdmin' => 0,
            ],
            [
                'Ho_va_ten' => 'Đỗ Văn Thành',
                'Mat_khau' => Hash::make('password123'),
                'Email' => 'thanh.do@example.com',
                'Ngay_sinh' => '1994-05-01',
                'Gioi_tinh' => 1,
                'ma_chuc_vu' => '005',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456784',
                'IsAdmin' => 0,
            ],
            [
                'Ho_va_ten' => 'Lưu Phương Nhi',
                'Mat_khau' => Hash::make('password123'),
                'Email' => 'nhi.luu@example.com',
                'Ngay_sinh' => '1995-06-01',
                'Gioi_tinh' => 0,
                'ma_chuc_vu' => '001',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456785',
                'IsAdmin' => 0,
            ],
            [
                'Ho_va_ten' => 'Lý Gia Hân',
                'Mat_khau' => Hash::make('password123'),
                'Email' => 'han.ly@example.com',
                'Ngay_sinh' => '1996-07-01',
                'Gioi_tinh' => 0,
                'ma_chuc_vu' => '002',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456786',
                'IsAdmin' => 0,
            ],
            [
                'Ho_va_ten' => 'Bùi Trọng Hiếu',
                'Mat_khau' => Hash::make('password123'),
                'Email' => 'hieu.bui@example.com',
                'Ngay_sinh' => '1997-08-01',
                'Gioi_tinh' => 1,
                'ma_chuc_vu' => '003',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456787',
                'IsAdmin' => 0,
            ],
            [
                'Ho_va_ten' => 'Ông Minh Triết',
                'Mat_khau' => Hash::make('password123'),
                'Email' => 'triet.ong@example.com',
                'Ngay_sinh' => '1998-09-01',
                'Gioi_tinh' => 1,
                'ma_chuc_vu' => '004',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456788',
                'IsAdmin' => 0,
            ],
            [
                'Ho_va_ten' => 'Vũ Tuấn Kiệt',
                'Mat_khau' => Hash::make('password123'),
                'Email' => 'kiet.vu@example.com',
                'Ngay_sinh' => '1999-10-01',
                'Gioi_tinh' => 1,
                'ma_chuc_vu' => '005',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456789',
                'IsAdmin' => 0,
            ]
        ];

        foreach ($admins as $admin) {
            Admin::create($admin);
        }
    }
}
