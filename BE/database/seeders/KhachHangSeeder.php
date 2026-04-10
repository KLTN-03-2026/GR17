<?php

namespace Database\Seeders;

use App\Models\KhachHang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KhachHangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $khachHangs = [
            [
                'Ma_khach_hang' => '1',
                'Ho_va_ten' => 'Nguyen Van A',
                'Mat_khau' => Hash::make('password1'),
                'Email' => 'nguyenvana@example.com',
                'Ngay_sinh' => '2000-01-01',
                'Gioi_tinh' => 1,
                'so_dien_thoai' => '0123456789',
                'is_block' => 1,
                'hash_reset' => null,
            ],
            [
                'Ma_khach_hang' => '2',
                'Ho_va_ten' => 'Tran Thi B',
                'Mat_khau' => Hash::make('password2'),
                'Email' => 'tranthib@example.com',
                'Ngay_sinh' => '1995-05-15',
                'Gioi_tinh' => 0,
                'so_dien_thoai' => '0987654321',
                'is_block' => 1,
                'hash_reset' => null,
            ],
            [
                'Ma_khach_hang' => '3',
                'Ho_va_ten' => 'Le Van C',
                'Mat_khau' => Hash::make('password3'),
                'Email' => 'levanc@example.com',
                'Ngay_sinh' => '1990-03-10',
                'Gioi_tinh' => 1,
                'so_dien_thoai' => '0912345678',
                'is_block' => 1,
                'hash_reset' => null,
            ],
            [
                'Ma_khach_hang' => '4',
                'Ho_va_ten' => 'Pham Thi D',
                'Mat_khau' => Hash::make('password4'),
                'Email' => 'phamthid@example.com',
                'Ngay_sinh' => '1988-12-20',
                'Gioi_tinh' => 0,
                'so_dien_thoai' => '0923456789',
                'is_block' => 1,
                'hash_reset' => null,
            ],
            [
                'Ma_khach_hang' => '5',
                'Ho_va_ten' => 'Hoang Van E',
                'Mat_khau' => Hash::make('password5'),
                'Email' => 'hoangvane@example.com',
                'Ngay_sinh' => '1992-07-07',
                'Gioi_tinh' => 1,
                'so_dien_thoai' => '0934567890',
                'is_block' => 1,
                'hash_reset' => null,
            ],
            [
                'Ma_khach_hang' => '6',
                'Ho_va_ten' => 'Vu Thi F',
                'Mat_khau' => Hash::make('password6'),
                'Email' => 'vuthif@example.com',
                'Ngay_sinh' => '1998-09-09',
                'Gioi_tinh' => 0,
                'so_dien_thoai' => '0945678901',
                'is_block' => 1,
                'hash_reset' => null,
            ],
            [
                'Ma_khach_hang' => '7',
                'Ho_va_ten' => 'Nguyen Van G',
                'Mat_khau' => Hash::make('password7'),
                'Email' => 'nguyenvang@example.com',
                'Ngay_sinh' => '1993-11-11',
                'Gioi_tinh' => 1,
                'so_dien_thoai' => '0956789012',
                'is_block' => 1,
                'hash_reset' => null,
            ],
            [
                'Ma_khach_hang' => '8',
                'Ho_va_ten' => 'Tran Thi H',
                'Mat_khau' => Hash::make('password8'),
                'Email' => 'tranthih@example.com',
                'Ngay_sinh' => '1997-04-04',
                'Gioi_tinh' => 0,
                'so_dien_thoai' => '0967890123',
                'is_block' => 1,
                'hash_reset' => null,
            ],
            [
                'Ma_khach_hang' => '9',
                'Ho_va_ten' => 'Le Van I',
                'Mat_khau' => Hash::make('password9'),
                'Email' => 'levani@example.com',
                'Ngay_sinh' => '1985-06-06',
                'Gioi_tinh' => 1,
                'so_dien_thoai' => '0978901234',
                'is_block' => 1,
                'hash_reset' => null,
            ],
            [
                'Ma_khach_hang' => '10',
                'Ho_va_ten' => 'Pham Thi J',
                'Mat_khau' => Hash::make('password10'),
                'Email' => 'phamthij@example.com',
                'Ngay_sinh' => '1991-08-08',
                'Gioi_tinh' => 0,
                'so_dien_thoai' => '0989012345',
                'is_block' => 1,
                'hash_reset' => null,
            ],
        ];

        foreach ($khachHangs as $khachHang) {
            KhachHang::create($khachHang);
        }
    }
}
