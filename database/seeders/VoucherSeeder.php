<?php

namespace Database\Seeders;

use App\Models\Voucher;
use App\Models\DoiTac;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $nextMonth = Carbon::now()->addMonth();

        // 1. Voucher của Sàn (Admin)
        Voucher::create([
            'ma_voucher' => 'WELCOME10',
            'ma_doi_tac' => null, // Của toàn sàn
            'ten_voucher' => 'Giảm 10% khách hàng mới',
            'loai_giam_gia' => 'percent',
            'gia_tri_giam' => 10,
            'giam_toi_da' => 200000,
            'don_toi_thieu' => 500000,
            'so_luong' => 100,
            'da_su_dung' => 0,
            'ngay_bat_dau' => $now,
            'ngay_ket_thuc' => $nextMonth,
            'trang_thai' => true,
        ]);

        Voucher::create([
            'ma_voucher' => 'SIEUSALE500K',
            'ma_doi_tac' => null,
            'ten_voucher' => 'Giảm thẳng 500K cho đơn lớn',
            'loai_giam_gia' => 'fixed',
            'gia_tri_giam' => 500000,
            'giam_toi_da' => null,
            'don_toi_thieu' => 5000000,
            'so_luong' => 50,
            'da_su_dung' => 0,
            'ngay_bat_dau' => $now,
            'ngay_ket_thuc' => $nextMonth,
            'trang_thai' => true,
        ]);

        // 2. Voucher của Đối tác (Lấy đối tác đầu tiên)
        $doiTac = DoiTac::first();
        if ($doiTac) {
            Voucher::create([
                'ma_voucher' => 'DOITACVIP',
                'ma_doi_tac' => $doiTac->Ma_doi_tac,
                'ten_voucher' => 'Ưu đãi đặc biệt từ Đối tác',
                'loai_giam_gia' => 'fixed',
                'gia_tri_giam' => 50000,
                'giam_toi_da' => null,
                'don_toi_thieu' => 200000,
                'so_luong' => 200,
                'da_su_dung' => 0,
                'ngay_bat_dau' => $now,
                'ngay_ket_thuc' => $nextMonth,
                'trang_thai' => true,
            ]);

            Voucher::create([
                'ma_voucher' => 'DOITAC20',
                'ma_doi_tac' => $doiTac->Ma_doi_tac,
                'ten_voucher' => 'Giảm 20% các tour chọn lọc',
                'loai_giam_gia' => 'percent',
                'gia_tri_giam' => 20,
                'giam_toi_da' => 100000,
                'don_toi_thieu' => 0,
                'so_luong' => 100,
                'da_su_dung' => 0,
                'ngay_bat_dau' => $now,
                'ngay_ket_thuc' => $nextMonth,
                'trang_thai' => true,
            ]);
        }
    }
}
