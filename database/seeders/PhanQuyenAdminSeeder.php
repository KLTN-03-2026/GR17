<?php

namespace Database\Seeders;

use App\Models\PhanQuyenAdmin;
use Illuminate\Database\Seeder;

class PhanQuyenAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phanQuyenData = [
            // Giám đốc - có quyền tất cả
            ['id_chuc_nang' => 1, 'id_chuc_vu' => 1], // Giám đốc - Xem danh sách
            ['id_chuc_nang' => 2, 'id_chuc_vu' => 1], // Giám đốc - Tạo mới
            ['id_chuc_nang' => 3, 'id_chuc_vu' => 1], // Giám đốc - Cập nhật
            ['id_chuc_nang' => 4, 'id_chuc_vu' => 1], // Giám đốc - Xóa
            ['id_chuc_nang' => 5, 'id_chuc_vu' => 1], // Giám đốc - Tìm kiếm

            // Quản lý - có quyền xem, tạo, cập nhật, tìm kiếm
            ['id_chuc_nang' => 1, 'id_chuc_vu' => 2], // Quản lý - Xem danh sách
            ['id_chuc_nang' => 2, 'id_chuc_vu' => 2], // Quản lý - Tạo mới
            ['id_chuc_nang' => 3, 'id_chuc_vu' => 2], // Quản lý - Cập nhật
            ['id_chuc_nang' => 5, 'id_chuc_vu' => 2], // Quản lý - Tìm kiếm

            // Kỹ sư - có quyền xem, tạo, tìm kiếm
            ['id_chuc_nang' => 1, 'id_chuc_vu' => 3], // Kỹ sư - Xem danh sách
            ['id_chuc_nang' => 2, 'id_chuc_vu' => 3], // Kỹ sư - Tạo mới
            ['id_chuc_nang' => 5, 'id_chuc_vu' => 3], // Kỹ sư - Tìm kiếm

            // Thiết kế viên - có quyền xem, tạo, cập nhật
            ['id_chuc_nang' => 1, 'id_chuc_vu' => 4], // Thiết kế viên - Xem danh sách
            ['id_chuc_nang' => 2, 'id_chuc_vu' => 4], // Thiết kế viên - Tạo mới
            ['id_chuc_nang' => 3, 'id_chuc_vu' => 4], // Thiết kế viên - Cập nhật

            // Hỗ trợ khách hàng - có quyền xem, tìm kiếm
            ['id_chuc_nang' => 1, 'id_chuc_vu' => 5], // Hỗ trợ khách hàng - Xem danh sách
            ['id_chuc_nang' => 5, 'id_chuc_vu' => 5], // Hỗ trợ khách hàng - Tìm kiếm
        ];

        foreach ($phanQuyenData as $data) {
            PhanQuyenAdmin::firstOrCreate($data);
        }
    }
}
