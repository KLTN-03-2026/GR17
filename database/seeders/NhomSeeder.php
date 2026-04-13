<?php

namespace Database\Seeders;

use App\Models\Nhom;
use Illuminate\Database\Seeder;

class NhomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                $nhomData = [
            [
                'Ma_nhom' => '001',
                'ten_nhom' => 'Gia đình chị Hoa',
                'mo_ta' => 'Du lịch hè 2026',
            ],
            [
                'Ma_nhom' => '002',
                'ten_nhom' => 'Công ty ABC',
                'mo_ta' => 'Team building thường niên',
            ]
        ];

        $faker = \Faker\Factory::create('vi_VN');
        for ($i = 3; $i <= 10; $i++) {
            $nhomData[] = [
                'Ma_nhom' => str_pad($i, 3, '0', STR_PAD_LEFT),
                'ten_nhom' => 'Nhóm ' . $faker->lastName . ' ' . $faker->firstName,
                'mo_ta' => 'Nhóm du lịch ' . $faker->realText(20),
            ];
        };

        foreach ($nhomData as $data) {
            Nhom::firstOrCreate(
                ['Ma_nhom' => $data['Ma_nhom']],
                ['ten_nhom' => $data['ten_nhom']]
            );
        }
    }
}
