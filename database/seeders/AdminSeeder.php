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
                'Ma_admin' => '001',
                'Ho_va_ten' => 'Admin User',
                'Mat_khau' => Hash::make('111111'),
                'Email' => 'admin@example.com',
                'Ngay_sinh' => '1990-01-01',
                'Gioi_tinh' => 1,
                'ma_chuc_vu' => 'CV001',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456780',
            ],
            [
                'Ma_admin' => '002',
                'Ho_va_ten' => 'Nguyen Van B',
                'Mat_khau' => Hash::make('password123'),
                'Email' => 'admin2@example.com',
                'Ngay_sinh' => '1991-02-01',
                'Gioi_tinh' => 1,
                'ma_chuc_vu' => 'CV002',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456781',
            ],
            [
                'Ma_admin' => '003',
                'Ho_va_ten' => 'Nguyen Van C',
                'Mat_khau' => Hash::make('password123'),
                'Email' => 'admin3@example.com',
                'Ngay_sinh' => '1992-03-01',
                'Gioi_tinh' => 1,
                'ma_chuc_vu' => 'CV003',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456782',
            ],
            [
                'Ma_admin' => '004',
                'Ho_va_ten' => 'Nguyen Van D',
                'Mat_khau' => Hash::make('password123'),
                'Email' => 'admin4@example.com',
                'Ngay_sinh' => '1993-04-01',
                'Gioi_tinh' => 1,
                'ma_chuc_vu' => 'CV004',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456783',
            ],
            [
                'Ma_admin' => '005',
                'Ho_va_ten' => 'Nguyen Van E',
                'Mat_khau' => Hash::make('password123'),
                'Email' => 'admin5@example.com',
                'Ngay_sinh' => '1994-05-01',
                'Gioi_tinh' => 1,
                'ma_chuc_vu' => 'CV005',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456784',
            ],
            [
                'Ma_admin' => '006',
                'Ho_va_ten' => 'Nguyen Van F',
                'Mat_khau' => Hash::make('password123'),
                'Email' => 'admin6@example.com',
                'Ngay_sinh' => '1995-06-01',
                'Gioi_tinh' => 1,
                'ma_chuc_vu' => 'CV001',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456785',
            ],
            [
                'Ma_admin' => '007',
                'Ho_va_ten' => 'Nguyen Van G',
                'Mat_khau' => Hash::make('password123'),
                'Email' => 'admin7@example.com',
                'Ngay_sinh' => '1996-07-01',
                'Gioi_tinh' => 1,
                'ma_chuc_vu' => 'CV002',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456786',
            ],
            [
                'Ma_admin' => '008',
                'Ho_va_ten' => 'Nguyen Van H',
                'Mat_khau' => Hash::make('password123'),
                'Email' => 'admin8@example.com',
                'Ngay_sinh' => '1997-08-01',
                'Gioi_tinh' => 1,
                'ma_chuc_vu' => 'CV003',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456787',
            ],
            [
                'Ma_admin' => '009',
                'Ho_va_ten' => 'Nguyen Van I',
                'Mat_khau' => Hash::make('password123'),
                'Email' => 'admin9@example.com',
                'Ngay_sinh' => '1998-09-01',
                'Gioi_tinh' => 1,
                'ma_chuc_vu' => 'CV004',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456788',
            ],
            [
                'Ma_admin' => '010',
                'Ho_va_ten' => 'Nguyen Van J',
                'Mat_khau' => Hash::make('password123'),
                'Email' => 'admin10@example.com',
                'Ngay_sinh' => '1999-10-01',
                'Gioi_tinh' => 1,
                'ma_chuc_vu' => 'CV005',
                'is_block' => 0,
                'hash_reset' => null,
                'so_dien_thoai' => '0123456789',
            ]
        ];

        foreach ($admins as $admin) {
            Admin::create($admin);
        }
    }
}
