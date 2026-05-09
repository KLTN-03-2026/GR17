<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagDiaDiemSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tag_dia_diem')->insert([
            [
                'ma_tag_dia_diem' => '001',
                'ma_dia_diem' => 'DN_TQ_001',
                'ma_tag' => '001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_tag_dia_diem' => '002',
                'ma_dia_diem' => 'DN_TQ_001',
                'ma_tag' => '004',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_tag_dia_diem' => '003',
                'ma_dia_diem' => 'DN_TQ_002',
                'ma_tag' => '001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_tag_dia_diem' => '004',
                'ma_dia_diem' => 'DN_TQ_002',
                'ma_tag' => '003',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_tag_dia_diem' => '005',
                'ma_dia_diem' => 'DN_KS_001',
                'ma_tag' => '004',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_tag_dia_diem' => '006',
                'ma_dia_diem' => 'DN_KS_002',
                'ma_tag' => '003',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_tag_dia_diem' => '007',
                'ma_dia_diem' => 'HA_TQ_001',
                'ma_tag' => '001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_tag_dia_diem' => '008',
                'ma_dia_diem' => 'HA_TQ_001',
                'ma_tag' => '005',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
