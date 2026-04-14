<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TourSeeder extends Seeder
{
    public function run(): void
    {
        $destinations = [
            ['ma_tour' => '001', 'ten' => 'Tour du lịch Phú Quốc 3N2Đ', 'mo_ta' => 'Khám phá Bãi Sao, chợ đêm Dương Đông và các trải nghiệm biển đảo nổi bật tại Phú Quốc.', 'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/77/Phu_Quoc_Island_Beach.jpg/800px-Phu_Quoc_Island_Beach.jpg', 'so_ngay' => 3, 'so_nguoi' => 20, 'ma_tag' => 'TAG01'],
            ['ma_tour' => '002', 'ten' => 'Tour biển Đà Nẵng 4N3Đ', 'mo_ta' => 'Kết hợp Cầu Vàng, bán đảo Sơn Trà và các bãi biển nổi bật của Đà Nẵng.', 'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Golden_Bridge_in_Ba_Na_Hills_2.jpg/800px-Golden_Bridge_in_Ba_Na_Hills_2.jpg', 'so_ngay' => 4, 'so_nguoi' => 15, 'ma_tag' => 'TAG02'],
            ['ma_tour' => '003', 'ten' => 'Tour khám phá Hà Nội 2N1Đ', 'mo_ta' => 'Lịch trình city break qua Hồ Hoàn Kiếm, phố cổ và những góc văn hóa đặc sắc của Hà Nội.', 'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a7/HoanKiemLake.jpg/800px-HoanKiemLake.jpg', 'so_ngay' => 2, 'so_nguoi' => 18, 'ma_tag' => 'TAG03'],
            ['ma_tour' => '004', 'ten' => 'Tour vịnh Hạ Long 3N2Đ', 'mo_ta' => 'Trải nghiệm du thuyền, đảo đá vôi và điểm ngắm cảnh đẹp nhất của Hạ Long.', 'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/H%E1%BA%A1_Long_Bay_viewed_from_Ti_T%E1%BB%91p_Island.jpg/800px-H%E1%BA%A1_Long_Bay_viewed_from_Ti_T%E1%BB%91p_Island.jpg', 'so_ngay' => 3, 'so_nguoi' => 22, 'ma_tag' => 'TAG04'],
            ['ma_tour' => '005', 'ten' => 'Tour phố cổ Hội An 3N2Đ', 'mo_ta' => 'Dạo phố cổ, trải nghiệm ẩm thực và không gian đèn lồng đặc trưng của Hội An.', 'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/75/Hoi_An_lanterns_at_night.jpg/800px-Hoi_An_lanterns_at_night.jpg', 'so_ngay' => 3, 'so_nguoi' => 16, 'ma_tag' => 'TAG05'],
            ['ma_tour' => '006', 'ten' => 'Tour biển Nha Trang 4N3Đ', 'mo_ta' => 'Kỳ nghỉ biển với lịch trình thư giãn, tham quan đảo và thưởng thức hải sản.', 'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ea/Nha_Trang_beach.jpg/800px-Nha_Trang_beach.jpg', 'so_ngay' => 4, 'so_nguoi' => 24, 'ma_tag' => 'TAG01'],
            ['ma_tour' => '007', 'ten' => 'Tour Đà Lạt săn mây 3N2Đ', 'mo_ta' => 'Lộ trình khám phá Langbiang, quảng trường Lâm Viên và những góc ngắm cảnh đẹp của Đà Lạt.', 'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/View_of_Da_Lat_from_Lang_Biang.jpg/800px-View_of_Da_Lat_from_Lang_Biang.jpg', 'so_ngay' => 3, 'so_nguoi' => 18, 'ma_tag' => 'TAG02'],
            ['ma_tour' => '008', 'ten' => 'Tour Huế di sản 3N2Đ', 'mo_ta' => 'Tham quan Đại Nội, lăng tẩm và các công trình di sản nổi bật của cố đô Huế.', 'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9c/Hue_Imperial_City_6.jpg/800px-Hue_Imperial_City_6.jpg', 'so_ngay' => 3, 'so_nguoi' => 20, 'ma_tag' => 'TAG03'],
            ['ma_tour' => '009', 'ten' => 'Tour Sa Pa vùng cao 3N2Đ', 'mo_ta' => 'Lịch trình ngắm núi, săn mây và khám phá văn hóa bản địa tại Sa Pa.', 'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/af/Terraced_rice_fields_in_Mu_Cang_Chai%2C_Yen_Bai%2C_Vietnam.jpg/800px-Terraced_rice_fields_in_Mu_Cang_Chai%2C_Yen_Bai%2C_Vietnam.jpg', 'so_ngay' => 3, 'so_nguoi' => 14, 'ma_tag' => 'TAG04'],
            ['ma_tour' => '010', 'ten' => 'Tour Ninh Bình 2N1Đ', 'mo_ta' => 'Khám phá Tràng An, Tam Cốc và hành trình thuyền xuyên núi đá vôi đặc trưng.', 'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Tam_Coc%2C_Ninh_Binh.jpg/800px-Tam_Coc%2C_Ninh_Binh.jpg', 'so_ngay' => 2, 'so_nguoi' => 26, 'ma_tag' => 'TAG05'],
            ['ma_tour' => '011', 'ten' => 'Tour Quy Nhơn biển xanh 3N2Đ', 'mo_ta' => 'Lịch trình nghỉ biển nhẹ nhàng, phù hợp cho nhóm bạn và gia đình.', 'hinh_anh' => 'https://images.unsplash.com/photo-1540541338-2c8115682650?auto=format&fit=crop&w=800&q=80', 'so_ngay' => 3, 'so_nguoi' => 22, 'ma_tag' => 'TAG01'],
            ['ma_tour' => '012', 'ten' => 'Tour Mũi Né 3N2Đ', 'mo_ta' => 'Kết hợp biển, đồi cát và nghỉ dưỡng ngắn ngày tại Phan Thiết.', 'hinh_anh' => 'https://images.unsplash.com/photo-1540541338-2c8115682650?auto=format&fit=crop&w=800&q=80', 'so_ngay' => 3, 'so_nguoi' => 25, 'ma_tag' => 'TAG02'],
            ['ma_tour' => '013', 'ten' => 'Tour Cần Thơ miền Tây 2N1Đ', 'mo_ta' => 'Tham quan chợ nổi, thưởng thức đặc sản và cảm nhận nhịp sống miền sông nước.', 'hinh_anh' => 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?auto=format&fit=crop&w=800&q=80', 'so_ngay' => 2, 'so_nguoi' => 20, 'ma_tag' => 'TAG03'],
            ['ma_tour' => '014', 'ten' => 'Tour Buôn Ma Thuột 3N2Đ', 'mo_ta' => 'Trải nghiệm không gian Tây Nguyên với thiên nhiên và văn hóa bản địa.', 'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/View_of_Da_Lat_from_Lang_Biang.jpg/800px-View_of_Da_Lat_from_Lang_Biang.jpg', 'so_ngay' => 3, 'so_nguoi' => 18, 'ma_tag' => 'TAG04'],
            ['ma_tour' => '015', 'ten' => 'Tour Phú Yên 3N2Đ', 'mo_ta' => 'Khám phá biển xanh, ghềnh đá và cung đường ven biển ấn tượng của Phú Yên.', 'hinh_anh' => 'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=800&q=80', 'so_ngay' => 3, 'so_nguoi' => 20, 'ma_tag' => 'TAG05'],
            ['ma_tour' => '016', 'ten' => 'Tour Mộc Châu 2N1Đ', 'mo_ta' => 'Hành trình ngắn ngày qua đồi chè, khí hậu mát mẻ và điểm ngắm cảnh vùng cao.', 'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/af/Terraced_rice_fields_in_Mu_Cang_Chai%2C_Yen_Bai%2C_Vietnam.jpg/800px-Terraced_rice_fields_in_Mu_Cang_Chai%2C_Yen_Bai%2C_Vietnam.jpg', 'so_ngay' => 2, 'so_nguoi' => 16, 'ma_tag' => 'TAG01'],
            ['ma_tour' => '017', 'ten' => 'Tour TP Hồ Chí Minh 2N1Đ', 'mo_ta' => 'City tour ngắn ngày qua các điểm biểu tượng và khu ẩm thực nổi bật của thành phố.', 'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f0/Independence_Palace%2C_Ho_Chi_Minh_City_%285%29.jpg/800px-Independence_Palace%2C_Ho_Chi_Minh_City_%285%29.jpg', 'so_ngay' => 2, 'so_nguoi' => 20, 'ma_tag' => 'TAG02'],
            ['ma_tour' => '018', 'ten' => 'Tour nghỉ dưỡng Phú Quốc 4N3Đ', 'mo_ta' => 'Gói nghỉ dưỡng phù hợp cho gia đình với lịch trình biển đảo và ẩm thực địa phương.', 'hinh_anh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/77/Phu_Quoc_Island_Beach.jpg/800px-Phu_Quoc_Island_Beach.jpg', 'so_ngay' => 4, 'so_nguoi' => 12, 'ma_tag' => 'TAG03'],
        ];

        $data = array_map(function (array $tour): array {
            return [
                'ma_tour' => $tour['ma_tour'],
                'ten_tour' => $tour['ten'],
                'mo_ta' => $tour['mo_ta'],
                'hinh_anh' => $tour['hinh_anh'],
                'so_tien' => 10000,
                'so_ngay' => $tour['so_ngay'],
                'so_nguoi' => $tour['so_nguoi'],
                'ma_tag' => $tour['ma_tag'],
                'nguon_tao' => 'he_thong',
                'trang_thai_duyet' => 'approved',
                'trang_thai_hien_thi' => true,
                'ly_do_tu_choi' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $destinations);

        DB::table('tours')->upsert(
            $data,
            ['ma_tour'],
            [
                'ten_tour',
                'mo_ta',
                'hinh_anh',
                'so_tien',
                'so_ngay',
                'so_nguoi',
                'ma_tag',
                'nguon_tao',
                'trang_thai_duyet',
                'trang_thai_hien_thi',
                'ly_do_tu_choi',
                'updated_at',
            ]
        );
    }
}
