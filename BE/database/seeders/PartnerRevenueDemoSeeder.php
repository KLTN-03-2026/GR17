<?php

namespace Database\Seeders;

use App\Models\DoiSoatHoaHong;
use App\Models\DoiTac;
use App\Models\HoaDon;
use App\Models\Nhom;
use App\Models\Tour;
use Illuminate\Database\Seeder;

class PartnerRevenueDemoSeeder extends Seeder
{
    public function run(): void
    {
        $partners = DoiTac::query()
            ->where('trang_thai_duyet', 'approved')
            ->where('is_block', false)
            ->orderBy('ma_doi_tac')
            ->limit(3)
            ->get(['ma_doi_tac']);

        if ($partners->isEmpty()) {
            return;
        }

        $nhomIds = Nhom::query()
            ->orderBy('Ma_nhom')
            ->pluck('Ma_nhom')
            ->values();

        if ($nhomIds->isEmpty()) {
            return;
        }

        $invoiceIndex = 1;

        foreach ($partners as $partnerIdx => $partner) {
            $tours = Tour::query()
                ->where('ma_doi_tac', $partner->ma_doi_tac)
                ->where('trang_thai_duyet', 'approved')
                ->orderBy('ma_tour')
                ->limit(4)
                ->get(['ma_tour', 'ten_tour']);

            if ($tours->isEmpty()) {
                continue;
            }

            foreach ($tours as $tourIdx => $tour) {
                $maHoaDon = sprintf('DT%08d', $invoiceIndex);
                $laDaThanhToan = $tourIdx !== 3; // mỗi đối tác có 1 đơn chưa thanh toán
                $tongTien = 1800000 + ($partnerIdx * 550000) + ($tourIdx * 350000);
                $nhomId = $nhomIds[($invoiceIndex - 1) % $nhomIds->count()];

                HoaDon::query()->updateOrCreate(
                    ['ma_hoa_don' => $maHoaDon],
                    [
                        'ma_nhom' => $nhomId,
                        'loai_hoa_don' => 0, // hóa đơn tour
                        'ma_doi_tuong' => $tour->ma_tour,
                        'tong_tien' => $tongTien,
                        'trang_thai_thanh_toan' => $laDaThanhToan ? 1 : 0,
                        'ma_giao_dich' => $laDaThanhToan ? 'GDT'.str_pad((string) $invoiceIndex, 6, '0', STR_PAD_LEFT) : null,
                        'ngay_tao' => now()->subDays(15 - $invoiceIndex),
                    ]
                );

                if ($laDaThanhToan) {
                    $tienHoaHongAdmin = round($tongTien * 0.1, 2);
                    $tienDoiTacThucNhan = round($tongTien - $tienHoaHongAdmin, 2);
                    $trangThaiDoiSoat = $tourIdx % 2 === 0 ? 'da_chuyen_khoan' : 'chua_doi_soat';

                    DoiSoatHoaHong::query()->updateOrCreate(
                        ['ma_hoa_don' => $maHoaDon],
                        [
                            'ma_doi_tac' => $partner->ma_doi_tac,
                            'loai_giao_dich' => 'tour',
                            'tong_tien_giao_dich' => $tongTien,
                            'phan_tram_hoa_hong' => 10,
                            'tien_hoa_hong_admin' => $tienHoaHongAdmin,
                            'tien_doi_tac_thuc_nhan' => $tienDoiTacThucNhan,
                            'trang_thai_thanh_toan' => $trangThaiDoiSoat,
                            'mo_ta' => 'Seeder demo doanh thu đối tác cho tour '.$tour->ma_tour,
                            'created_at' => now()->subDays(15 - $invoiceIndex),
                            'updated_at' => now(),
                        ]
                    );
                } else {
                    DoiSoatHoaHong::query()->where('ma_hoa_don', $maHoaDon)->delete();
                }

                $invoiceIndex++;
            }
        }
    }
}
