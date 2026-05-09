<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\DoiSoatHoaHong;
use App\Models\DoiTac;
use App\Models\GiaoDichQr;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\Nhom;
use App\Models\ThanhVienNhom;
use App\Models\Tour;
use App\Models\TourKhoiHanh;
use App\Services\HoaDonPaymentSyncService;
use App\Services\VietQrService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PaymentQrDemoSeeder extends Seeder
{
    private const DEMO_ADMIN_ID = 'DMOADM01';
    private const DEMO_CUSTOMER_ID = 'DMOKH001';
    private const DEMO_PARTNER_ID = 'DMODT001';
    private const DEMO_TOUR_ID = 'DMT001';
    private const DEMO_SCHEDULE_IDS = ['DMS001', 'DMS002'];
    private const DEMO_INVOICE_CODES = ['HD99010001', 'HD99010002', 'HD99010003', 'HD99010004'];
    private const DEMO_GROUP_IDS = ['DMON001', 'DMON002', 'DMON003', 'DMON004'];
    private const DEMO_MEMBER_IDS = ['DMOTV001', 'DMOTV002', 'DMOTV003', 'DMOTV004'];
    private const DEMO_PASSWORD = 'demo123';

    public function run(): void
    {
        DB::transaction(function (): void {
            $this->resetDemoInvoices();

            $this->seedDemoAdmin();
            $customer = $this->seedDemoCustomer();
            $partner = $this->seedDemoPartner();
            $tour = $this->seedDemoTour($partner);
            $schedules = $this->seedDemoSchedules($tour);

            $this->seedInvoiceBundles($customer, $tour, $schedules);
        });
    }

    private function resetDemoInvoices(): void
    {
        DoiSoatHoaHong::query()
            ->whereIn('ma_hoa_don', self::DEMO_INVOICE_CODES)
            ->delete();

        GiaoDichQr::query()
            ->whereIn('ma_hoa_don', self::DEMO_INVOICE_CODES)
            ->delete();

        HoaDon::query()
            ->whereIn('ma_hoa_don', self::DEMO_INVOICE_CODES)
            ->delete();

        ThanhVienNhom::query()
            ->whereIn('Ma_thanh_vien', self::DEMO_MEMBER_IDS)
            ->orWhereIn('Ma_nhom', self::DEMO_GROUP_IDS)
            ->delete();

        Nhom::query()
            ->whereIn('Ma_nhom', self::DEMO_GROUP_IDS)
            ->delete();
    }

    private function seedDemoAdmin(): void
    {
        DB::table('chuc_vu')->updateOrInsert(
            ['ma_chuc_vu' => 'CVDEMO'],
            [
                'ten_chuc_vu' => 'Admin Demo QR',
                'tinh_trang' => 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        Admin::query()->updateOrCreate(
            ['Ma_admin' => self::DEMO_ADMIN_ID],
            [
                'Ho_va_ten' => 'Admin QR Demo',
                'Mat_khau' => Hash::make(self::DEMO_PASSWORD),
                'Email' => 'admin.qr.demo@example.com',
                'Ngay_sinh' => '1995-01-01',
                'Gioi_tinh' => true,
                'ma_chuc_vu' => 'CVDEMO',
                'is_block' => false,
                'hash_reset' => null,
                'so_dien_thoai' => '0901234500',
            ]
        );
    }

    private function seedDemoCustomer(): KhachHang
    {
        return KhachHang::query()->updateOrCreate(
            ['Ma_khach_hang' => self::DEMO_CUSTOMER_ID],
            [
                'Ho_va_ten' => 'Demo Customer QR',
                'Mat_khau' => Hash::make(self::DEMO_PASSWORD),
                'Email' => 'customer.qr.demo@example.com',
                'Ngay_sinh' => '2000-01-01',
                'Gioi_tinh' => true,
                'so_dien_thoai' => '0901234567',
                'is_block' => true,
                'hash_reset' => null,
            ]
        );
    }

    private function seedDemoPartner(): DoiTac
    {
        return DoiTac::query()->updateOrCreate(
            ['ma_doi_tac' => self::DEMO_PARTNER_ID],
            [
                'ten_doi_tac' => 'Doi Tac QR Demo',
                'ten_nguoi_dai_dien' => 'Demo Partner Owner',
                'email' => 'partner.qr.demo@example.com',
                'mat_khau' => Hash::make(self::DEMO_PASSWORD),
                'so_dien_thoai' => '0901234568',
                'dia_chi' => 'Quan 1, TP.HCM',
                'is_block' => false,
                'trang_thai_duyet' => 'approved',
                'ly_do_tu_choi' => null,
            ]
        );
    }

    private function seedDemoTour(DoiTac $partner): Tour
    {
        return Tour::query()->updateOrCreate(
            ['ma_tour' => self::DEMO_TOUR_ID],
            [
                'ma_doi_tac' => $partner->ma_doi_tac,
                'ten_tour' => 'Tour QR Demo Sai Gon - Vung Tau',
                'mo_ta' => 'Tour demo cho do an: tao hoa don, hien QR, webhook doi chieu va retry payment.',
                'hinh_anh' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80',
                'so_tien' => 1890000,
                'so_ngay' => 2,
                'so_nguoi' => 20,
                'ma_tag' => null,
                'nguon_tao' => 'doi_tac',
                'trang_thai_duyet' => 'approved',
                'trang_thai_hien_thi' => true,
                'ly_do_tu_choi' => null,
            ]
        );
    }

    /**
     * @return array<string, TourKhoiHanh>
     */
    private function seedDemoSchedules(Tour $tour): array
    {
        $schedules = [
            self::DEMO_SCHEDULE_IDS[0] => [
                'ma_tour' => $tour->ma_tour,
                'ngay_bat_dau' => '2026-07-10',
                'ngay_ket_thuc' => '2026-07-11',
                'so_cho' => 25,
                'tinh_trang' => true,
            ],
            self::DEMO_SCHEDULE_IDS[1] => [
                'ma_tour' => $tour->ma_tour,
                'ngay_bat_dau' => '2026-07-24',
                'ngay_ket_thuc' => '2026-07-25',
                'so_cho' => 18,
                'tinh_trang' => true,
            ],
        ];

        $result = [];
        foreach ($schedules as $maThoiGianTour => $payload) {
            $result[$maThoiGianTour] = TourKhoiHanh::query()->updateOrCreate(
                ['ma_thoi_gian_tour' => $maThoiGianTour],
                $payload
            );
        }

        return $result;
    }

    /**
     * @param array<string, TourKhoiHanh> $schedules
     */
    private function seedInvoiceBundles(KhachHang $customer, Tour $tour, array $schedules): void
    {
        $vietQrService = app(VietQrService::class);
        $paymentSyncService = app(HoaDonPaymentSyncService::class);
        $now = now();

        $bundles = [
            [
                'invoice_code' => 'HD99010001',
                'group_id' => 'DMON001',
                'member_id' => 'DMOTV001',
                'schedule_id' => self::DEMO_SCHEDULE_IDS[0],
                'status' => 'pending',
                'reference' => null,
                'created_at' => $now->copy()->subHours(2),
                'expires_at' => $now->copy()->addMinutes(25),
                'paid_at' => null,
            ],
            [
                'invoice_code' => 'HD99010002',
                'group_id' => 'DMON002',
                'member_id' => 'DMOTV002',
                'schedule_id' => self::DEMO_SCHEDULE_IDS[0],
                'status' => 'paid',
                'reference' => 'DEMO-PAID-0002',
                'created_at' => $now->copy()->subDay(),
                'expires_at' => $now->copy()->subDay()->addMinutes(15),
                'paid_at' => $now->copy()->subDay()->addMinutes(8),
            ],
            [
                'invoice_code' => 'HD99010003',
                'group_id' => 'DMON003',
                'member_id' => 'DMOTV003',
                'schedule_id' => self::DEMO_SCHEDULE_IDS[1],
                'status' => 'expired',
                'reference' => null,
                'created_at' => $now->copy()->subDays(2),
                'expires_at' => $now->copy()->subDay(),
                'paid_at' => null,
            ],
            [
                'invoice_code' => 'HD99010004',
                'group_id' => 'DMON004',
                'member_id' => 'DMOTV004',
                'schedule_id' => self::DEMO_SCHEDULE_IDS[1],
                'status' => 'failed',
                'reference' => 'DEMO-FAILED-0004',
                'created_at' => $now->copy()->subHours(5),
                'expires_at' => $now->copy()->subHours(4)->addMinutes(10),
                'paid_at' => null,
            ],
        ];

        foreach ($bundles as $bundle) {
            Nhom::query()->updateOrCreate(
                ['Ma_nhom' => $bundle['group_id']],
                ['ten_nhom' => "Demo QR {$bundle['invoice_code']}"]
            );

            ThanhVienNhom::query()->updateOrCreate(
                ['Ma_thanh_vien' => $bundle['member_id']],
                [
                    'Ma_nhom' => $bundle['group_id'],
                    'Ma_khach_hang' => $customer->Ma_khach_hang,
                    'vai_tro' => 1,
                ]
            );

            $hoaDon = HoaDon::query()->create([
                'ma_hoa_don' => $bundle['invoice_code'],
                'ma_nhom' => $bundle['group_id'],
                'ma_khach_hang_dat' => $customer->Ma_khach_hang,
                'loai_hoa_don' => 0,
                'ma_doi_tuong' => $tour->ma_tour,
                'ma_thoi_gian_tour' => $bundle['schedule_id'],
                'tong_tien' => (float) $tour->so_tien,
                'trang_thai_thanh_toan' => 0,
                'payment_method' => 'vietqr_bank_transfer',
                'payment_status' => 'pending',
                'payment_reference' => null,
                'ma_giao_dich' => null,
                'ten_nguoi_dat' => $customer->Ho_va_ten,
                'email_nguoi_dat' => $customer->Email,
                'so_dien_thoai_nguoi_dat' => $customer->so_dien_thoai,
                'dia_chi_nguoi_dat' => '12 Nguyen Hue, Quan 1, TP.HCM',
                'ngay_tao' => $bundle['created_at'],
            ]);

            $qrData = $vietQrService->createPaymentQr($bundle['invoice_code'], (float) $tour->so_tien);

            $qrPayment = GiaoDichQr::query()->create([
                'ma_giao_dich_qr' => (string) Str::ulid(),
                'ma_hoa_don' => $bundle['invoice_code'],
                'provider' => 'vietqr',
                'so_tien' => (float) $tour->so_tien,
                'noi_dung_chuyen_khoan' => $bundle['invoice_code'],
                'qr_url' => $qrData['qr_url'],
                'qr_payload' => $qrData['qr_payload'] ?? null,
                'trang_thai' => 'pending',
                'reference_code' => $bundle['reference'],
                'expires_at' => $bundle['expires_at'],
                'created_at' => $bundle['created_at'],
                'updated_at' => $bundle['created_at'],
            ]);

            if ($bundle['status'] === 'paid') {
                $paymentSyncService->markAsPaid(
                    $hoaDon,
                    $bundle['reference'],
                    $bundle['paid_at'],
                    $qrPayment,
                    [
                        'id' => 'DEMO-SEPAY-0002',
                        'content' => $bundle['invoice_code'],
                        'transferAmount' => (float) $tour->so_tien,
                        'referenceCode' => $bundle['reference'],
                    ]
                );

                continue;
            }

            if ($bundle['status'] === 'failed') {
                $paymentSyncService->markAsFailed(
                    $hoaDon,
                    $bundle['reference'],
                    $qrPayment,
                    [
                        'id' => 'DEMO-SEPAY-0004',
                        'content' => $bundle['invoice_code'],
                        'transferAmount' => (float) $tour->so_tien,
                        'referenceCode' => $bundle['reference'],
                    ]
                );

                continue;
            }

            if ($bundle['status'] === 'expired') {
                $qrPayment->forceFill([
                    'trang_thai' => 'expired',
                    'updated_at' => $bundle['expires_at'],
                ])->save();
            }
        }
    }
}
