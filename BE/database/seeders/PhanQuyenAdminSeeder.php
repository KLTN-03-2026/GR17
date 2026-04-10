<?php

namespace Database\Seeders;

use App\Models\PhanQuyenAdmin;
use Illuminate\Database\Seeder;

class PhanQuyenAdminSeeder extends Seeder
{
    public function run(): void
    {
        $phanQuyenData = [
            // Giám đốc
            ['ma_phan_quyen'=>'001','ma_chuc_nang'=>'001','ma_chuc_vu'=>'001'],
            ['ma_phan_quyen'=>'002','ma_chuc_nang'=>'002','ma_chuc_vu'=>'001'],
            ['ma_phan_quyen'=>'003','ma_chuc_nang'=>'003','ma_chuc_vu'=>'001'],
            ['ma_phan_quyen'=>'004','ma_chuc_nang'=>'004','ma_chuc_vu'=>'001'],
            ['ma_phan_quyen'=>'005','ma_chuc_nang'=>'005','ma_chuc_vu'=>'001'],

            // Quản lý
            ['ma_phan_quyen'=>'006','ma_chuc_nang'=>'001','ma_chuc_vu'=>'002'],
            ['ma_phan_quyen'=>'007','ma_chuc_nang'=>'002','ma_chuc_vu'=>'002'],
            ['ma_phan_quyen'=>'008','ma_chuc_nang'=>'003','ma_chuc_vu'=>'002'],
            ['ma_phan_quyen'=>'009','ma_chuc_nang'=>'005','ma_chuc_vu'=>'002'],

            // Kỹ sư
            ['ma_phan_quyen'=>'010','ma_chuc_nang'=>'001','ma_chuc_vu'=>'003'],
            ['ma_phan_quyen'=>'011','ma_chuc_nang'=>'002','ma_chuc_vu'=>'003'],
            ['ma_phan_quyen'=>'012','ma_chuc_nang'=>'005','ma_chuc_vu'=>'003'],

            // Thiết kế viên
            ['ma_phan_quyen'=>'013','ma_chuc_nang'=>'001','ma_chuc_vu'=>'004'],
            ['ma_phan_quyen'=>'014','ma_chuc_nang'=>'002','ma_chuc_vu'=>'004'],
            ['ma_phan_quyen'=>'015','ma_chuc_nang'=>'003','ma_chuc_vu'=>'004'],

            // Hỗ trợ khách hàng
            ['ma_phan_quyen'=>'016','ma_chuc_nang'=>'001','ma_chuc_vu'=>'005'],
            ['ma_phan_quyen'=>'017','ma_chuc_nang'=>'005','ma_chuc_vu'=>'005'],
        ];

        foreach ($phanQuyenData as $data) {
            PhanQuyenAdmin::firstOrCreate($data);
        }
    }
}
