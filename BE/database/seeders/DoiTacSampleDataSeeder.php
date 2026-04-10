<?php

namespace Database\Seeders;

use App\Models\ChiTietTour;
use App\Models\DiaDiem;
use App\Models\DoiTac;
use App\Models\Tour;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DoiTacSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $doiTacs = DoiTac::query()
            ->orderBy('ma_doi_tac')
            ->get(['ma_doi_tac', 'ten_doi_tac']);

        foreach ($doiTacs as $index => $doiTac) {
            $partnerIndex = $index + 1;
            $diaDiemIds = $this->seedDiaDiemForPartner($doiTac->ma_doi_tac, $partnerIndex);
            $this->seedTourForPartner($doiTac->ma_doi_tac, $partnerIndex, $diaDiemIds);
        }
    }

    private function seedDiaDiemForPartner(string $maDoiTac, int $partnerIndex): array
    {
        $diaDiemIds = [];

        for ($stt = 1; $stt <= 10; $stt++) {
            $maDiaDiem = (string) (300000 + ($partnerIndex * 100) + $stt);
            $status = $this->locationStatusByIndex($stt);
            $tenDiaDiem = "Điểm đến đối tác {$maDoiTac} - {$stt}";
            $diaChi = "Số {$stt} Đường Du Lịch, Quận {$partnerIndex}, TP Hồ Chí Minh";

            $diaDiem = DiaDiem::updateOrCreate(
                ['ma_dia_diem' => $maDiaDiem],
                [
                    'ma_doi_tac_tao' => $maDoiTac,
                    'ten_dia_diem' => $tenDiaDiem,
                    'ten_dia_diem_normalized' => $this->normalizeValue($tenDiaDiem),
                    'loai' => (($stt - 1) % 3) + 1,
                    'dia_chi' => $diaChi,
                    'dia_chi_normalized' => $this->normalizeValue($diaChi),
                    'sdt' => sprintf('09%08d', ($partnerIndex * 100) + $stt),
                    'kinh_do' => 106.60 + ($partnerIndex * 0.01) + ($stt * 0.001),
                    'vi_do' => 10.70 + ($partnerIndex * 0.01) + ($stt * 0.001),
                    'gio_mo_cua' => '08:00',
                    'gio_dong_cua' => '21:00',
                    'gia_giao_dong' => 70000 + ($stt * 10000),
                    'hinh_anh' => "https://picsum.photos/seed/partner-location-{$maDiaDiem}/900/600",
                    'mo_ta' => "Địa điểm mẫu số {$stt} của đối tác {$maDoiTac}.",
                    'thoi_gian_tham_quan' => (($stt % 4) + 1) . ' giờ',
                    'nguon_tao' => 'doi_tac',
                    'trang_thai_duyet' => $status['status'],
                    'ly_do_tu_choi' => $status['reject_reason'],
                ]
            );

            $diaDiemIds[] = $diaDiem->ma_dia_diem;
        }

        return $diaDiemIds;
    }

    private function seedTourForPartner(string $maDoiTac, int $partnerIndex, array $diaDiemIds): void
    {
        for ($stt = 1; $stt <= 10; $stt++) {
            $maTour = (string) (200000 + ($partnerIndex * 100) + $stt);
            $status = $this->tourStatusByIndex($stt);

            $tour = Tour::updateOrCreate(
                ['ma_tour' => $maTour],
                [
                    'ma_doi_tac' => $maDoiTac,
                    'ten_tour' => "Tour mẫu đối tác {$maDoiTac} - {$stt}",
                    'mo_ta' => "Lịch trình mẫu {$stt} dành cho đối tác {$maDoiTac}.",
                    'hinh_anh' => "https://picsum.photos/seed/partner-tour-{$maTour}/900/600",
                    'so_tien' => 1500000 + ($stt * 350000),
                    'so_ngay' => (($stt - 1) % 4) + 2,
                    'so_nguoi' => 10 + ($stt * 2),
                    'ma_tag' => 'TAG0' . (($stt % 5) + 1),
                    'nguon_tao' => 'doi_tac',
                    'trang_thai_duyet' => $status['status'],
                    'trang_thai_hien_thi' => $status['visible'],
                    'ly_do_tu_choi' => $status['reject_reason'],
                ]
            );

            $selectedDiaDiems = $this->pickThreeLocations($diaDiemIds, $stt);

            foreach ($selectedDiaDiems as $order => $maDiaDiem) {
                ChiTietTour::updateOrCreate(
                    [
                        'ma_tour' => $tour->ma_tour,
                        'ma_dia_diem' => $maDiaDiem,
                    ],
                    [
                        'ngay_hanh_trinh' => (int) floor($order / 2) + 1,
                        'thu_tu_hanh_trinh' => $order + 1,
                        'ghi_chu_hanh_trinh' => "Điểm dừng " . ($order + 1) . " cho tour {$tour->ma_tour}.",
                    ]
                );
            }

            ChiTietTour::query()
                ->where('ma_tour', $tour->ma_tour)
                ->whereNotIn('ma_dia_diem', $selectedDiaDiems)
                ->delete();
        }
    }

    private function pickThreeLocations(array $diaDiemIds, int $stt): array
    {
        $count = count($diaDiemIds);
        if ($count < 3) {
            return $diaDiemIds;
        }

        $start = ($stt - 1) % $count;

        return [
            $diaDiemIds[$start % $count],
            $diaDiemIds[($start + 1) % $count],
            $diaDiemIds[($start + 2) % $count],
        ];
    }

    private function locationStatusByIndex(int $stt): array
    {
        if ($stt <= 4) {
            return ['status' => 'approved', 'reject_reason' => null];
        }

        if ($stt <= 8) {
            return ['status' => 'pending_approval', 'reject_reason' => null];
        }

        return [
            'status' => 'rejected',
            'reject_reason' => 'Thiếu thông tin mô tả chi tiết, vui lòng cập nhật và gửi lại.',
        ];
    }

    private function tourStatusByIndex(int $stt): array
    {
        if ($stt <= 4) {
            return [
                'status' => 'approved',
                'visible' => $stt <= 2,
                'reject_reason' => null,
            ];
        }

        if ($stt <= 7) {
            return [
                'status' => 'pending_approval',
                'visible' => false,
                'reject_reason' => null,
            ];
        }

        if ($stt <= 9) {
            return [
                'status' => 'draft',
                'visible' => false,
                'reject_reason' => null,
            ];
        }

        return [
            'status' => 'rejected',
            'visible' => false,
            'reject_reason' => 'Nội dung tour chưa đầy đủ, vui lòng bổ sung chi tiết lịch trình.',
        ];
    }

    private function normalizeValue(?string $value): string
    {
        $value = trim((string) $value);
        $value = mb_strtolower($value, 'UTF-8');
        $value = Str::ascii($value);
        $value = preg_replace('/[^a-z0-9\s]/', ' ', $value) ?? '';
        $value = preg_replace('/\s+/', ' ', $value) ?? '';

        return trim($value);
    }
}
