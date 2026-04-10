<?php

namespace Database\Seeders;

use App\Models\DanhGiaKeHoach;
use Illuminate\Database\Seeder;

class DanhGiaKeHoachSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $danhGiaData = [
            [
                'Ma_danh_gia' => '001',
                'Ma_khach_hang' => '1',
                'ma_dia_diem' => '001',
                'so_sao' => 5,
                'noi_dung' => 'Nhà thờ Đức Bà rất đẹp, kiến trúc tuyệt vời. Rất đáng để ghé thăm!',
            ],
            [
                'Ma_danh_gia' => '002',
                'Ma_khach_hang' => '2',
                'ma_dia_diem' => '001',
                'so_sao' => 4,
                'noi_dung' => 'Tuyệt đẹp nhưng hơi đông đúc vào cuối tuần. Nên đi vào sáng sớm.',
            ],
            [
                'Ma_danh_gia' => '003',
                'Ma_khach_hang' => '3',
                'ma_dia_diem' => '002',
                'so_sao' => 5,
                'noi_dung' => 'Dinh Độc Lập là một trong những địa điểm lịch sử quan trọng nhất. Hướng dẫn viên rất chừng chừa.',
            ],
            [
                'Ma_danh_gia' => '004',
                'Ma_khach_hang' => '4',
                'ma_dia_diem' => '002',
                'so_sao' => 4,
                'noi_dung' => 'Công trình lịch sử quan trọng. Cần có hướng dẫn viên để hiểu rõ lịch sử.',
            ],
            [
                'Ma_danh_gia' => '005',
                'Ma_khach_hang' => '5',
                'ma_dia_diem' => '003',
                'so_sao' => 5,
                'noi_dung' => 'Reverie Saigon là khách sạn 5 sao tuyệt vời. Phục vụ chuyên nghiệp, tiện nghi đầy đủ.',
            ],
            [
                'Ma_danh_gia' => '006',
                'Ma_khach_hang' => '6',
                'ma_dia_diem' => '003',
                'so_sao' => 5,
                'noi_dung' => 'Phòng sạch sẽ, nhân viên thân thiện, vị trí tuyệt vời. Sẽ quay lại!',
            ],
            [
                'Ma_danh_gia' => '007',
                'Ma_khach_hang' => '7',
                'ma_dia_diem' => '004',
                'so_sao' => 4,
                'noi_dung' => 'Quán Ngon có ẩm thực Việt Nam chân chính và ngon. Giá cơ phải hợp lý.',
            ],
            [
                'Ma_danh_gia' => '008',
                'Ma_khach_hang' => '8',
                'ma_dia_diem' => '004',
                'so_sao' => 4,
                'noi_dung' => 'Đồ ăn ngon, phục vụ nhanh chóng. Không gian nhà hàng thoáng đãng.',
            ],
            [
                'Ma_danh_gia' => '009',
                'Ma_khach_hang' => '1',
                'ma_dia_diem' => '005',
                'so_sao' => 3,
                'noi_dung' => 'Bến Bạch Đằng đẹp lúc hoàng hôn. Nhưng hơi ít điểm tham quan.',
            ],
            [
                'Ma_danh_gia' => '010',
                'Ma_khach_hang' => '2',
                'ma_dia_diem' => '005',
                'so_sao' => 4,
                'noi_dung' => 'Cảnh sông rất đẹp. Lý tưởng cho những bức ảnh hoàng hôn.',
            ],
        ];

        foreach ($danhGiaData as $data) {
            DanhGiaKeHoach::firstOrCreate(
                ['Ma_danh_gia' => $data['Ma_danh_gia']],
                [
                    'Ma_khach_hang' => $data['Ma_khach_hang'],
                    'ma_dia_diem' => $data['ma_dia_diem'],
                    'so_sao' => $data['so_sao'],
                    'noi_dung' => $data['noi_dung'],
                ]
            );
        }
    }
}
