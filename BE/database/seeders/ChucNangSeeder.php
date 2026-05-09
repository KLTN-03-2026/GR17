<?php

namespace Database\Seeders;

use App\Models\ChucNang;
use Illuminate\Database\Seeder;

class ChucNangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chucNangData = [
            ['ma_chuc_nang' => '101', 'ten_chuc_nang' => 'Quản lý Đối tác', 'mo_ta' => 'Quản lý thông tin và phê duyệt đối tác'],
            ['ma_chuc_nang' => '102', 'ten_chuc_nang' => 'Quản lý Khách hàng', 'mo_ta' => 'Quản lý khách hàng, hóa đơn và phản hồi'],
            ['ma_chuc_nang' => '103', 'ten_chuc_nang' => 'Quản lý Địa điểm', 'mo_ta' => 'Quản lý địa điểm, dịch vụ và tags'],
            ['ma_chuc_nang' => '104', 'ten_chuc_nang' => 'Quản lý Tour', 'mo_ta' => 'Quản lý tour du lịch, lịch trình và xe'],
            ['ma_chuc_nang' => '105', 'ten_chuc_nang' => 'Quản lý Voucher', 'mo_ta' => 'Quản lý mã giảm giá và đối soát tài chính'],
            ['ma_chuc_nang' => '106', 'ten_chuc_nang' => 'Thống kê & Báo cáo', 'mo_ta' => 'Xem báo cáo doanh thu và thống kê hệ thống'],
            ['ma_chuc_nang' => '107', 'ten_chuc_nang' => 'Quản lý Hệ thống', 'mo_ta' => 'Quản lý admin, chức vụ và phân quyền'],
            ['ma_chuc_nang' => '108', 'ten_chuc_nang' => 'Cấu hình AI', 'mo_ta' => 'Cấu hình Prompt và API cho AI Planner'],
        ];

        foreach ($chucNangData as $data) {
            ChucNang::updateOrCreate(
                ['ma_chuc_nang' => $data['ma_chuc_nang']],
                ['ten_chuc_nang' => $data['ten_chuc_nang'], 'mo_ta' => $data['mo_ta']]
            );
        }
    }
}
