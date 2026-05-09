<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SoThichSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('so_thich')->insert([
            [
                'ma_so_thich' => '001',
                'ma_khach_hang' => '1',
                'ma_tag' => '001',
                'muc_do' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_so_thich' => '002',
                'ma_khach_hang' => '1',
                'ma_tag' => '002',
                'muc_do' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_so_thich' => '003',
                'ma_khach_hang' => '2',
                'ma_tag' => '001',
                'muc_do' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_so_thich' => '004',
                'ma_khach_hang' => '2',
                'ma_tag' => '003',
                'muc_do' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_so_thich' => '005',
                'ma_khach_hang' => '3',
                'ma_tag' => '005',
                'muc_do' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
