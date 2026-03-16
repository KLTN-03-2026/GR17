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
            ['ten_chuc_nang' => 'Xem danh sách', 'mo_ta' => 'Quyền xem danh sách các bản ghi'],
            ['ten_chuc_nang' => 'Tạo mới', 'mo_ta' => 'Quyền tạo mới các bản ghi'],
            ['ten_chuc_nang' => 'Cập nhật', 'mo_ta' => 'Quyền chỉnh sửa các bản ghi'],
            ['ten_chuc_nang' => 'Xóa', 'mo_ta' => 'Quyền xóa các bản ghi'],
            ['ten_chuc_nang' => 'Tìm kiếm', 'mo_ta' => 'Quyền tìm kiếm các bản ghi'],
        ];

        foreach ($chucNangData as $data) {
            ChucNang::firstOrCreate(
                ['ten_chuc_nang' => $data['ten_chuc_nang']],
                ['mo_ta' => $data['mo_ta']]
            );
        }
    }
}
