<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiaDiemSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            $this->location('001', 'Nhà thờ Đức Bà Sài Gòn', 1, '01 Công xã Paris, Quận 1, TP Hồ Chí Minh', '0123456789', 106.6990, 10.7798, '08:00', '17:00', 50000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Notre_Dame_de_Saigon_%286283733519%29.jpg/800px-Notre_Dame_de_Saigon_%286283733519%29.jpg', 'Công trình kiến trúc biểu tượng giữa trung tâm thành phố.', '2 giờ'),
            $this->location('002', 'Dinh Độc Lập', 1, '135 Nam Kỳ Khởi Nghĩa, Quận 1, TP Hồ Chí Minh', '0987654321', 106.6953, 10.7771, '07:30', '17:30', 40000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f0/Independence_Palace%2C_Ho_Chi_Minh_City_%285%29.jpg/800px-Independence_Palace%2C_Ho_Chi_Minh_City_%285%29.jpg', 'Di tích lịch sử nổi bật gắn với nhiều dấu mốc của thành phố.', '2 giờ'),
            $this->location('003', 'Bưu điện Trung tâm Sài Gòn', 1, '02 Công xã Paris, Quận 1, TP Hồ Chí Minh', '0911111111', 106.6993, 10.7801, '08:00', '18:00', 30000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Notre_Dame_de_Saigon_%286283733519%29.jpg/800px-Notre_Dame_de_Saigon_%286283733519%29.jpg', 'Điểm check-in cổ điển ngay bên cạnh Nhà thờ Đức Bà.', '1.5 giờ'),
            $this->location('004', 'Chợ Bến Thành', 3, 'Lê Lợi, Quận 1, TP Hồ Chí Minh', '0911222333', 106.6980, 10.7725, '07:00', '22:00', 100000, 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?auto=format&fit=crop&w=800&q=80', 'Khu chợ nổi tiếng để khám phá ẩm thực và quà lưu niệm địa phương.', '2 giờ'),
            $this->location('005', 'Hồ Hoàn Kiếm', 1, 'Quận Hoàn Kiếm, Hà Nội', '0911222444', 105.8522, 21.0285, '06:00', '22:00', 0, 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a7/HoanKiemLake.jpg/800px-HoanKiemLake.jpg', 'Không gian đi bộ và cảnh hồ trung tâm Hà Nội.', '2 giờ'),
            $this->location('006', 'Phố cổ Hà Nội', 1, 'Hàng Đào, Quận Hoàn Kiếm, Hà Nội', '0911222555', 105.8505, 21.0342, '07:00', '23:00', 0, 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a7/HoanKiemLake.jpg/800px-HoanKiemLake.jpg', 'Khu phố cổ nhộn nhịp, phù hợp để dạo bộ và thưởng thức món ăn đường phố.', '3 giờ'),
            $this->location('007', 'Vịnh Hạ Long', 1, 'Phường Bãi Cháy, TP Hạ Long, Quảng Ninh', '0911222666', 107.0448, 20.9101, '06:00', '18:00', 250000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/H%E1%BA%A1_Long_Bay_viewed_from_Ti_T%E1%BB%91p_Island.jpg/800px-H%E1%BA%A1_Long_Bay_viewed_from_Ti_T%E1%BB%91p_Island.jpg', 'Di sản thiên nhiên thế giới với tuyến tham quan vịnh và đảo đá vôi đặc trưng.', '4 giờ'),
            $this->location('008', 'Tràng An Ninh Bình', 1, 'Tràng An, Hoa Lư, Ninh Bình', '0911222777', 105.8860, 20.2506, '07:00', '17:00', 250000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Tam_Coc%2C_Ninh_Binh.jpg/800px-Tam_Coc%2C_Ninh_Binh.jpg', 'Quần thể danh thắng nổi tiếng với tuyến thuyền xuyên hang động.', '3 giờ'),
            $this->location('009', 'Nhà thờ đá Sa Pa', 1, 'Phố Hàm Rồng, Sa Pa, Lào Cai', '0911222888', 103.8414, 22.3364, '06:30', '20:00', 0, 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/af/Terraced_rice_fields_in_Mu_Cang_Chai%2C_Yen_Bai%2C_Vietnam.jpg/800px-Terraced_rice_fields_in_Mu_Cang_Chai%2C_Yen_Bai%2C_Vietnam.jpg', 'Biểu tượng trung tâm thị trấn Sa Pa, thuận tiện ghé thăm chợ đêm và quảng trường.', '1.5 giờ'),
            $this->location('010', 'Ruộng bậc thang Mù Cang Chải', 1, 'Mù Cang Chải, Yên Bái', '0911222999', 104.1100, 21.8050, '06:00', '18:00', 50000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/af/Terraced_rice_fields_in_Mu_Cang_Chai%2C_Yen_Bai%2C_Vietnam.jpg/800px-Terraced_rice_fields_in_Mu_Cang_Chai%2C_Yen_Bai%2C_Vietnam.jpg', 'Khung cảnh ruộng bậc thang đặc trưng của vùng cao Tây Bắc.', '3 giờ'),
            $this->location('011', 'Cầu Vàng Bà Nà Hills', 1, 'Hòa Ninh, Hòa Vang, Đà Nẵng', '0911333000', 107.9969, 15.9957, '08:00', '17:00', 900000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Golden_Bridge_in_Ba_Na_Hills_2.jpg/800px-Golden_Bridge_in_Ba_Na_Hills_2.jpg', 'Điểm check-in nổi bật của Đà Nẵng với góc nhìn toàn cảnh núi đồi.', '4 giờ'),
            $this->location('012', 'Phố cổ Hội An', 1, 'Minh An, Hội An, Quảng Nam', '0911333111', 108.3260, 15.8801, '08:00', '22:00', 120000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/75/Hoi_An_lanterns_at_night.jpg/800px-Hoi_An_lanterns_at_night.jpg', 'Khu phố đèn lồng nổi tiếng với nhiều quán ăn, quán cà phê và cửa hàng thủ công.', '3 giờ'),
            $this->location('013', 'Đại Nội Huế', 1, 'Thành Nội, TP Huế, Thừa Thiên Huế', '0911333222', 107.5797, 16.4696, '07:00', '17:30', 200000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9c/Hue_Imperial_City_6.jpg/800px-Hue_Imperial_City_6.jpg', 'Quần thể di tích hoàng cung triều Nguyễn, phù hợp cho tuyến tham quan di sản.', '3 giờ'),
            $this->location('014', 'Biển Nha Trang', 1, 'Trần Phú, TP Nha Trang, Khánh Hòa', '0911333333', 109.1967, 12.2388, '05:30', '18:30', 0, 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ea/Nha_Trang_beach.jpg/800px-Nha_Trang_beach.jpg', 'Bãi biển trung tâm dễ tiếp cận với nhiều hoạt động ngoài trời.', '3 giờ'),
            $this->location('015', 'Quảng trường Lâm Viên', 1, 'Trần Quốc Toản, TP Đà Lạt, Lâm Đồng', '0911333444', 108.4410, 11.9403, '06:00', '22:00', 0, 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/View_of_Da_Lat_from_Lang_Biang.jpg/800px-View_of_Da_Lat_from_Lang_Biang.jpg', 'Không gian công cộng rộng lớn, thuận tiện cho tuyến city tour Đà Lạt.', '1.5 giờ'),
            $this->location('016', 'Đỉnh Langbiang', 1, 'Lạc Dương, Lâm Đồng', '0911333555', 108.3644, 12.0048, '07:00', '17:00', 100000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/View_of_Da_Lat_from_Lang_Biang.jpg/800px-View_of_Da_Lat_from_Lang_Biang.jpg', 'Địa điểm ngắm toàn cảnh Đà Lạt từ trên cao.', '3 giờ'),
            $this->location('017', 'Bãi Sao Phú Quốc', 1, 'An Thới, Phú Quốc, Kiên Giang', '0911333666', 104.0354, 10.0303, '06:00', '18:00', 0, 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/77/Phu_Quoc_Island_Beach.jpg/800px-Phu_Quoc_Island_Beach.jpg', 'Bãi biển nổi tiếng với cát trắng và nước trong, phù hợp cho kỳ nghỉ ngắn ngày.', '3 giờ'),
            $this->location('018', 'Chợ đêm Phú Quốc', 3, 'Đường Võ Thị Sáu, Dương Đông, Phú Quốc', '0911333777', 103.9607, 10.2214, '17:00', '23:00', 150000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/77/Phu_Quoc_Island_Beach.jpg/800px-Phu_Quoc_Island_Beach.jpg', 'Điểm dạo tối nổi tiếng để thưởng thức hải sản và đặc sản địa phương.', '2 giờ'),
            $this->location('019', 'Resort biển Mũi Né', 2, 'Nguyễn Đình Chiểu, TP Phan Thiết, Bình Thuận', '0911333888', 108.2391, 10.9447, '00:00', '23:59', 1500000, 'https://images.unsplash.com/photo-1540541338-2c8115682650?auto=format&fit=crop&w=800&q=80', 'Không gian nghỉ dưỡng ven biển thuận tiện cho kỳ nghỉ gia đình.', 'Lưu trú'),
            $this->location('020', 'Khu nghỉ dưỡng ven biển Đà Nẵng', 2, 'Võ Nguyên Giáp, Sơn Trà, Đà Nẵng', '0911333999', 108.2504, 16.0740, '00:00', '23:59', 1800000, 'https://images.unsplash.com/photo-1540541338-2c8115682650?auto=format&fit=crop&w=800&q=80', 'Điểm lưu trú phù hợp cho lịch trình biển và thành phố Đà Nẵng.', 'Lưu trú'),
            $this->location('021', 'Nhà hàng hải sản Nha Trang', 3, 'Trần Phú, TP Nha Trang, Khánh Hòa', '0911444000', 109.1985, 12.2414, '10:00', '22:00', 250000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/30/B%C3%BAn_Th%E1%BB%8Bt_N%C6%B0%E1%BB%9Bng.jpg/800px-B%C3%BAn_Th%E1%BB%8Bt_N%C6%B0%E1%BB%9Bng.jpg', 'Nhà hàng phù hợp cho khách thích hải sản tươi và không gian gần biển.', '1.5 giờ'),
            $this->location('022', 'Quán ăn phố cổ Hội An', 3, 'Bạch Đằng, Hội An, Quảng Nam', '0911444111', 108.3279, 15.8783, '09:00', '22:00', 180000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/75/Hoi_An_lanterns_at_night.jpg/800px-Hoi_An_lanterns_at_night.jpg', 'Điểm dừng chân phù hợp để trải nghiệm ẩm thực Hội An sau khi dạo phố cổ.', '1.5 giờ'),
            $this->location('023', 'Homestay Sa Pa view núi', 2, 'Tả Van, Sa Pa, Lào Cai', '0911444222', 103.8221, 22.2997, '00:00', '23:59', 850000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/af/Terraced_rice_fields_in_Mu_Cang_Chai%2C_Yen_Bai%2C_Vietnam.jpg/800px-Terraced_rice_fields_in_Mu_Cang_Chai%2C_Yen_Bai%2C_Vietnam.jpg', 'Lưu trú phong cách bản địa với góc nhìn ruộng bậc thang và thung lũng.', 'Lưu trú'),
            $this->location('024', 'Khu du lịch Buôn Đôn', 1, 'Buôn Đôn, Đắk Lắk', '0911444333', 107.7547, 12.8967, '08:00', '17:00', 120000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/View_of_Da_Lat_from_Lang_Biang.jpg/800px-View_of_Da_Lat_from_Lang_Biang.jpg', 'Khu sinh thái phù hợp cho trải nghiệm thiên nhiên và văn hóa Tây Nguyên.', '3 giờ'),
        ];

        DB::table('dia_diem')->upsert(
            $data,
            ['ma_dia_diem'],
            [
                'ten_dia_diem',
                'loai',
                'dia_chi',
                'sdt',
                'kinh_do',
                'vi_do',
                'gio_mo_cua',
                'gio_dong_cua',
                'gia_giao_dong',
                'hinh_anh',
                'mo_ta',
                'thoi_gian_tham_quan',
                'nguon_tao',
                'trang_thai_duyet',
                'ly_do_tu_choi',
                'updated_at',
            ]
        );
    }

    private function location(
        string $maDiaDiem,
        string $ten,
        int $loai,
        string $diaChi,
        string $sdt,
        float $kinhDo,
        float $viDo,
        string $gioMoCua,
        string $gioDongCua,
        int $giaGiaoDong,
        string $hinhAnh,
        string $moTa,
        string $thoiGianThamQuan,
    ): array {
        return [
            'ma_dia_diem' => $maDiaDiem,
            'ten_dia_diem' => $ten,
            'loai' => $loai,
            'dia_chi' => $diaChi,
            'sdt' => $sdt,
            'kinh_do' => $kinhDo,
            'vi_do' => $viDo,
            'gio_mo_cua' => $gioMoCua,
            'gio_dong_cua' => $gioDongCua,
            'gia_giao_dong' => $giaGiaoDong,
            'hinh_anh' => $hinhAnh,
            'mo_ta' => $moTa,
            'thoi_gian_tham_quan' => $thoiGianThamQuan,
            'nguon_tao' => 'he_thong',
            'trang_thai_duyet' => 'approved',
            'ly_do_tu_choi' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
