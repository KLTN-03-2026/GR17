<?php

namespace Tests\Unit;

use App\Models\DoiSoatHoaHong;
use App\Models\DoiTac;
use App\Models\HoaDon;
use App\Models\Nhom;
use App\Services\PartnerMonthlyReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PartnerMonthlyReportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_builds_correct_monthly_totals_for_partner(): void
    {
        $doiTac = DoiTac::create([
            'ma_doi_tac' => '2001',
            'ten_doi_tac' => 'Doi Tac A',
            'ten_nguoi_dai_dien' => 'Nguoi Dai Dien A',
            'email' => 'partner-a@example.com',
            'mat_khau' => Hash::make('123456'),
            'is_block' => false,
            'trang_thai_duyet' => 'approved',
        ]);

        $nhom = Nhom::create([
            'Ma_nhom' => 'N2001',
            'ten_nhom' => 'Nhom test',
        ]);

        HoaDon::create([
            'ma_hoa_don' => 'HD2001001',
            'ma_nhom' => $nhom->Ma_nhom,
            'loai_hoa_don' => 0,
            'ma_doi_tuong' => '900001',
            'tong_tien' => 2000000,
            'trang_thai_thanh_toan' => 1,
        ]);

        HoaDon::create([
            'ma_hoa_don' => 'HD2001002',
            'ma_nhom' => $nhom->Ma_nhom,
            'loai_hoa_don' => 0,
            'ma_doi_tuong' => '900002',
            'tong_tien' => 1000000,
            'trang_thai_thanh_toan' => 1,
        ]);

        HoaDon::create([
            'ma_hoa_don' => 'HD2001003',
            'ma_nhom' => $nhom->Ma_nhom,
            'loai_hoa_don' => 0,
            'ma_doi_tuong' => '900003',
            'tong_tien' => 3000000,
            'trang_thai_thanh_toan' => 1,
        ]);

        DoiSoatHoaHong::create([
            'ma_hoa_don' => 'HD2001001',
            'ma_doi_tac' => $doiTac->ma_doi_tac,
            'loai_giao_dich' => 'tour',
            'tong_tien_giao_dich' => 2000000,
            'phan_tram_hoa_hong' => 10,
            'tien_hoa_hong_admin' => 200000,
            'tien_doi_tac_thuc_nhan' => 1800000,
            'trang_thai_thanh_toan' => 'da_chuyen_khoan',
            'created_at' => '2026-03-05 08:00:00',
        ]);

        DoiSoatHoaHong::create([
            'ma_hoa_don' => 'HD2001002',
            'ma_doi_tac' => $doiTac->ma_doi_tac,
            'loai_giao_dich' => 'tour',
            'tong_tien_giao_dich' => 1000000,
            'phan_tram_hoa_hong' => 10,
            'tien_hoa_hong_admin' => 100000,
            'tien_doi_tac_thuc_nhan' => 900000,
            'trang_thai_thanh_toan' => 'chua_doi_soat',
            'created_at' => '2026-03-20 08:00:00',
        ]);

        DoiSoatHoaHong::create([
            'ma_hoa_don' => 'HD2001003',
            'ma_doi_tac' => $doiTac->ma_doi_tac,
            'loai_giao_dich' => 'tour',
            'tong_tien_giao_dich' => 3000000,
            'phan_tram_hoa_hong' => 10,
            'tien_hoa_hong_admin' => 300000,
            'tien_doi_tac_thuc_nhan' => 2700000,
            'trang_thai_thanh_toan' => 'chua_doi_soat',
            'created_at' => '2026-04-02 08:00:00',
        ]);

        $service = app(PartnerMonthlyReportService::class);
        $data = $service->buildForMonth($doiTac->ma_doi_tac, '2026-03');

        $this->assertSame('2026-03', $data['month']);
        $this->assertSame(2, $data['metrics']['total_orders']);
        $this->assertSame(3000000.0, $data['metrics']['gross_revenue']);
        $this->assertSame(300000.0, $data['metrics']['platform_commission']);
        $this->assertSame(2700000.0, $data['metrics']['partner_revenue']);
        $this->assertSame(1800000.0, $data['metrics']['paid_to_partner']);
        $this->assertSame(900000.0, $data['metrics']['pending_to_partner']);
    }
}
