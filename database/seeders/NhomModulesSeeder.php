<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\KhachHang;
use App\Models\DiaDiem;
use App\Models\Nhom;
use App\Models\ThanhVienNhom;
use App\Models\DanhGiaKeHoach;
use App\Models\HoaDon;
use Carbon\Carbon;

class NhomModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Lấy dữ liệu KH & Địa điểm có sẵn để làm FK
        $khs = KhachHang::take(5)->pluck('Ma_khach_hang')->toArray();
        $dds = DiaDiem::take(5)->pluck('ma_dia_diem')->toArray();

        if (empty($khs) || empty($dds)) {
            $this->command->warn('Không đủ Khách Hàng hoặc Địa Điểm để tạo dữ liệu mẫu. Hãy seed KH/Địa điểm trước!');
            return;
        }

        // Xóa data cũ cho sạch
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        HoaDon::truncate();
        DanhGiaKeHoach::truncate();
        ThanhVienNhom::truncate();
        Nhom::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // == 1. SEED 10 Nhóm ==
        $truongNhoms = [];
        for ($i = 1; $i <= 10; $i++) {
            $maNhom = 'NHOM_' . str_pad($i, 3, '0', STR_PAD_LEFT);
            Nhom::create([
                'Ma_nhom' => $maNhom,
                'ten_nhom' => 'Nhóm Du Lịch Số ' . $i,
            ]);
            $truongNhoms[] = $maNhom;
        }

        // == 2. SEED Thành Viên Nhóm (Mỗi nhóm 1 Trưởng, 1-2 Thành viên) ==
        $thanhVienCount = 1;
        foreach ($truongNhoms as $index => $maNhom) {
            // Trưởng nhóm
            $khTruong = $khs[$index % count($khs)];
            ThanhVienNhom::create([
                'Ma_thanh_vien' => 'TV_' . str_pad($thanhVienCount++, 3, '0', STR_PAD_LEFT),
                'Ma_nhom' => $maNhom,
                'Ma_khach_hang' => $khTruong,
                'vai_tro' => 1, // Trưởng nhóm
            ]);

            // Thành viên
            $khTV = $khs[($index + 1) % count($khs)];
            ThanhVienNhom::create([
                'Ma_thanh_vien' => 'TV_' . str_pad($thanhVienCount++, 3, '0', STR_PAD_LEFT),
                'Ma_nhom' => $maNhom,
                'Ma_khach_hang' => $khTV,
                'vai_tro' => 0, // Thành viên
            ]);
        }

        // == 3. SEED 10 Đánh Giá Kế Hoạch ==
        for ($i = 1; $i <= 10; $i++) {
            DanhGiaKeHoach::create([
                'Ma_danh_gia' => 'DG_' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'Ma_khach_hang' => $khs[$i % count($khs)],
                'ma_dia_diem' => $dds[$i % count($dds)],
                'so_sao' => rand(3, 5),
                'noi_dung' => 'Review mẫu tự động số ' . $i,
            ]);
        }

        // == 4. SEED 10 Hóa Đơn ==
        for ($i = 1; $i <= 10; $i++) {
            HoaDon::create([
                'Ma_hoa_don' => 'HD_' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'Ma_khach_hang' => $khs[$i % count($khs)],
                'Ma_nhom' => $i % 2 == 0 ? $truongNhoms[$i % count($truongNhoms)] : null, // 50% có nhóm, 50% rỗng
                'ma_dia_diem' => $dds[$i % count($dds)],
                'tong_tien' => rand(100, 5000) * 1000,
                'trang_thai' => rand(0, 2),
                'ngay_dat' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }

        $this->command->info('Đã seed thành công Nhom, ThanhVienNhom, DanhGiaKeHoach, HoaDon! Mỗi bảng ~10 records.');
    }
}
