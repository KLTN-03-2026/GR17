<?php

namespace Database\Seeders;
use Database\Seeders\AdminSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
                // Independence / Foundation
            ChucVuSeeder::class,
            ChucNangSeeder::class,
            TagSeeder::class,
            CauHinhNgaySeeder::class,
            CauHinhAiSeeder::class,

                // Core Users & Permissions
            AdminSeeder::class,
            PhanQuyenAdminSeeder::class,
            KhachHangSeeder::class,
            DoiTacSeeder::class,
            SoThichSeeder::class,
            VoucherSeeder::class,

                // Core Data
            DiaDiemSeeder::class,
            TourSeeder::class,
            XeSeeder::class,
            KeHoachSeeder::class,
            XeKeHoachSeeder::class,
            NhomSeeder::class,

                // Relations & Operational Data
            TagDiaDiemSeeder::class,
            DichVuDiaDiemSeeder::class,
            ThanhVienNhomSeeder::class,
            KeHoachSeeder::class,
            ChiTietTourSeeder::class,
            // DoiTacSampleDataSeeder::class,
            TourKhoiHanhSeeder::class,
            XeKeHoachSeeder::class,
            HoatDongChiTietSeeder::class,
            DanhGiaKeHoachSeeder::class,
            HoaDonSeeder::class,
            PartnerRevenueDemoSeeder::class,
            DanhSachYeuThichSeeder::class,
            PhanHoiSeeder::class,
        ]);
    }
}
