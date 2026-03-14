<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tag')->insert([
            [
                'ma_tag' => '001',
                'ten_tag' => 'Lịch sử',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_tag' => '002',
                'ten_tag' => 'Mua sắm',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_tag' => '003',
                'ten_tag' => 'Giải trí',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_tag' => '004',
                'ten_tag' => 'Nghiệp vụ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_tag' => '005',
                'ten_tag' => 'Thiên nhiên',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
