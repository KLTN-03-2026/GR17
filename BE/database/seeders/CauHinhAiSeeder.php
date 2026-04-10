<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CauHinhAiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('cau_hinh_ais')->insert([
            [
                'loai' => 'Gợi ý lịch trình',
                'noi_dung' => 'Bạn là một chuyên gia du lịch Việt Nam. Hãy dựa trên sở thích và ngân sách của người dùng để gợi ý lịch trình tối ưu nhất.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'loai' => 'Phân tích sở thích',
                'noi_dung' => 'Dựa trên lịch sử đi lại và đánh giá của người dùng, hãy xác định phong cách du lịch của họ (Khám phá, Nghỉ dưỡng, Ẩm thực...).',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'loai' => 'Tối ưu hóa ngân sách',
                'noi_dung' => 'Tính toán chi phí dự kiến và đưa ra các lựa chọn thay thế rẻ hơn nhưng vẫn đảm bảo trải nghiệm.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
