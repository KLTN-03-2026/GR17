<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChiTietTourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('chi_tiet_tours')->insert([
            [
                'ma_chi_tiet_tour' => '001',
                'ma_tour' => '001',
                'ma_dia_diem' => '001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_chi_tiet_tour' => '002',
                'ma_tour' => '001',
                'ma_dia_diem' => '002',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_chi_tiet_tour' => '003',
                'ma_tour' => '002',
                'ma_dia_diem' => '003',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
