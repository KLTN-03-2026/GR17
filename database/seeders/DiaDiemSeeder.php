<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiaDiemSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // ================= ĐÀ NẴNG =================
            // Tham quan (loại 1)
            $this->location('DN_TQ_001', 'Bán đảo Sơn Trà & Chùa Linh Ứng', 1, 'Thọ Quang, Sơn Trà, Đà Nẵng', '02363111222', 108.2772, 16.1214, '06:00', '18:00', 0, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTKbHPV59roMWgl40SSgm-okotD9k6upQq2nA&s', 'Lá phổi xanh của Đà Nẵng, nơi có tượng Phật Bà Quan Âm cao nhất Việt Nam.', '2 giờ'),
            $this->location('DN_TQ_002', 'Ngũ Hành Sơn', 1, 'Hòa Hải, Ngũ Hành Sơn, Đà Nẵng', '02363111333', 108.2636, 16.0048, '07:00', '17:30', 40000, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT0w3nq-Fpy-D4_WQoX0qG9ky_PCOuvGxYn8Q&s', 'Quần thể 5 ngọn núi đá vôi tuyệt đẹp với hệ thống hang động và chùa chiền kỳ vĩ.', '2.5 giờ'),
            $this->location('DN_TQ_003', 'Bà Nà Hills - Cầu Vàng', 1, 'Hòa Ninh, Hòa Vang, Đà Nẵng', '0911333000', 107.9969, 15.9957, '08:00', '17:00', 900000, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSqmGkmYeMkCg0gXyQCSwA7BSVLxgKilL5K_w&s', 'Khu du lịch trên núi với Cầu Vàng nổi tiếng toàn thế giới.', '4 giờ'),
            $this->location('DN_TQ_004', 'Biển Mỹ Khê', 1, 'Sơn Trà, Đà Nẵng', '02363111444', 108.2464, 16.0600, '00:00', '23:59', 0, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR1mX-B8hTas0RTiuhxGDDmO2Xsl1NoQl-X_w&s', 'Một trong những bãi biển đẹp nhất hành tinh, cát trắng mịn, nước trong xanh.', '2 giờ'),
            // Lưu trú (loại 2)
            $this->location('DN_KS_001', 'Khách sạn Mường Thanh Luxury Đà Nẵng', 2, '270 Võ Nguyên Giáp, Ngũ Hành Sơn, Đà Nẵng', '02363929929', 108.2476, 16.0552, '00:00', '23:59', 1500000, 'https://pix10.agoda.net/hotelImages/219/2190907/2190907_17080417080054905485.jpg?ca=6&ce=1&s=414x232', 'Khách sạn 5 sao cao cấp với góc nhìn toàn cảnh biển Mỹ Khê.', 'Lưu trú'),
            $this->location('DN_KS_002', 'InterContinental Danang Sun Peninsula Resort', 2, 'Bán đảo Sơn Trà, Đà Nẵng', '02363938888', 108.3159, 16.1245, '00:00', '23:59', 5000000, 'https://vietluxtour.com/tourcaocap/Upload/images/2021/Intercontinental%20DN/1.jpg', 'Resort sang trọng bậc nhất nằm ẩn mình trên bán đảo Sơn Trà.', 'Lưu trú'),
            // Ẩm thực (loại 3)
            $this->location('DN_NA_001', 'Hải sản Bé Biển', 3, 'Lô 11 Võ Nguyên Giáp, Sơn Trà, Đà Nẵng', '0905234567', 108.2461, 16.0723, '10:00', '23:00', 300000, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRtGMYW5o2AW_spdGFdvloXbuNpqn6nCKhmqQ&s', 'Nhà hàng hải sản tươi sống nổi tiếng bậc nhất dọc bờ biển Đà Nẵng.', '1.5 giờ'),
            $this->location('DN_NA_002', 'Mì Quảng Bà Mua', 3, '19-21 Trần Bình Trọng, Hải Châu, Đà Nẵng', '02363821017', 108.2195, 16.0664, '06:00', '22:00', 50000, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQJ2lRpL0Ohv7_K2G3R71fJAY96f_OSsu8nPA&s', 'Quán mì Quảng truyền thống với nước dùng đậm đà hương vị miền Trung.', '1 giờ'),

            // ================= HUẾ =================
            // Tham quan (loại 1)
            $this->location('HUE_TQ_001', 'Đại Nội Huế', 1, 'Đường 23/8, Thuận Hòa, TP Huế', '02343111222', 107.5797, 16.4696, '07:00', '17:30', 200000, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTtwEXm_kcQgI8DLrmcBryAybIuM31jLCjM4A&s', 'Hoàng cung của 13 vị vua triều Nguyễn, di sản văn hóa thế giới.', '3 giờ'),
            $this->location('HUE_TQ_002', 'Lăng Khải Định', 1, 'Thủy Bằng, Hương Thủy, Thừa Thiên Huế', '02343111333', 107.5931, 16.3989, '07:30', '17:00', 150000, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRGYUWakLrLMYf31kuO7bQE_Qj-EFpGtjQHIw&s', 'Lăng tẩm có kiến trúc điêu khắc tinh xảo kết hợp giữa phương Đông và phương Tây.', '1.5 giờ'),
            $this->location('HUE_TQ_003', 'Chùa Thiên Mụ', 1, 'Hương Hòa, TP Huế', '02343111444', 107.5532, 16.4533, '06:00', '18:00', 0, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ0ndAJMQ_pNyqfehEUuZKw1PMpXvvnnkol_A&s', 'Ngôi chùa cổ linh thiêng bậc nhất xứ Huế, nằm yên bình bên bờ sông Hương.', '1 giờ'),
            // Lưu trú (loại 2)
            $this->location('HUE_KS_001', 'Saigon Morin Hotel Huế', 2, '30 Lê Lợi, Phú Hội, TP Huế', '02343823526', 107.5878, 16.4682, '00:00', '23:59', 1200000, 'https://images.unsplash.com/photo-1542314831-c6a4203251a5?auto=format&fit=crop&w=800&q=80', 'Khách sạn cổ kính view sông Hương, mang đậm dấu ấn kiến trúc Pháp.', 'Lưu trú'),
            $this->location('HUE_KS_002', 'Century Riverside Hotel', 2, '49 Lê Lợi, Phú Hội, TP Huế', '02343823390', 107.5912, 16.4695, '00:00', '23:59', 900000, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80', 'Khách sạn lý tưởng để ngắm trọn vẹn cầu Tràng Tiền lộng lẫy về đêm.', 'Lưu trú'),
            // Ẩm thực (loại 3)
            $this->location('HUE_NA_001', 'Bún Bò Huế Bà Gái', 3, '11A Hà Nội, Vĩnh Ninh, TP Huế', '0905111222', 107.5835, 16.4665, '06:00', '21:00', 50000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/B%C3%BAn_b%C3%B2_Hu%E1%BA%BF_in_Hue.jpg/800px-B%C3%BAn_b%C3%B2_Hu%E1%BA%BF_in_Hue.jpg', 'Quán bún bò gia truyền với hương vị ruốc sả đậm đà.', '1 giờ'),
            $this->location('HUE_NA_002', 'Cơm Niêu Khải Hoàn', 3, '90 Lê Lợi, Phú Hội, TP Huế', '02343826968', 107.5955, 16.4710, '09:00', '22:00', 150000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/30/B%C3%BAn_Th%E1%BB%8Bt_N%C6%B0%E1%BB%9Bng.jpg/800px-B%C3%BAn_Th%E1%BB%8Bt_N%C6%B0%E1%BB%9Bng.jpg', 'Nhà hàng chuyên phục vụ cơm niêu và các món ăn truyền thống cung đình Huế.', '1.5 giờ'),

            // ================= HỘI AN =================
            // Tham quan (loại 1)
            $this->location('HA_TQ_001', 'Phố cổ Hội An', 1, 'Minh An, Hội An, Quảng Nam', '02353111222', 108.3260, 15.8801, '08:00', '22:00', 120000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/75/Hoi_An_lanterns_at_night.jpg/800px-Hoi_An_lanterns_at_night.jpg', 'Di sản văn hóa thế giới, rực rỡ dưới ánh đèn lồng vào ban đêm.', '3 giờ'),
            $this->location('HA_TQ_002', 'Rừng dừa Bảy Mẫu', 1, 'Cẩm Thanh, Hội An, Quảng Nam', '02353111333', 108.3683, 15.8753, '07:00', '17:30', 150000, 'https://statics.vinpearl.com/rung-dua-bay-mau-hoi-an-1_1629452044.jpg', 'Miền Tây thu nhỏ giữa lòng Hội An, trải nghiệm đi thuyền thúng mộc mạc.', '2 giờ'),
            $this->location('HA_TQ_003', 'Đảo Cù Lao Chàm', 1, 'Tân Hiệp, Hội An, Quảng Nam', '02353111444', 108.5167, 15.9167, '08:00', '16:00', 450000, 'https://vcdn1-dulich.vnecdn.net/2022/04/27/cu-lao-cham-1.jpg', 'Khu dự trữ sinh quyển lặn ngắm san hô cực kỳ hấp dẫn.', '6 giờ'),
            // Lưu trú (loại 2)
            $this->location('HA_KS_001', 'Hoi An Historic Hotel', 2, '10 Trần Hưng Đạo, Hội An, Quảng Nam', '02353861445', 108.3275, 15.8812, '00:00', '23:59', 1100000, 'https://images.unsplash.com/photo-1542314831-c6a4203251a5?auto=format&fit=crop&w=800&q=80', 'Khách sạn lịch sử với không gian sân vườn rộng lớn ngay sát phố cổ.', 'Lưu trú'),
            $this->location('HA_KS_002', 'Mường Thanh Holiday Hội An', 2, 'Khu Ô 9, KĐT Phước Trạch, Hội An', '02353666999', 108.3615, 15.8900, '00:00', '23:59', 1300000, 'https://images.unsplash.com/photo-1540541338-2c8115682650?auto=format&fit=crop&w=800&q=80', 'Khách sạn hiện đại nằm gần bãi biển Cửa Đại thanh bình.', 'Lưu trú'),
            // Ẩm thực (loại 3)
            $this->location('HA_NA_001', 'Nhà hàng Bông Hồng Trắng', 3, '533 Hai Bà Trưng, Hội An, Quảng Nam', '02353862784', 108.3268, 15.8835, '07:30', '20:30', 80000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/75/Hoi_An_lanterns_at_night.jpg/800px-Hoi_An_lanterns_at_night.jpg', 'Nổi tiếng với món bánh bao bánh vạc (White Rose) độc quyền của dòng họ.', '1 giờ'),
            $this->location('HA_NA_002', 'Cơm Gà Bà Buội', 3, '22 Phan Chu Trinh, Hội An, Quảng Nam', '0905123456', 108.3281, 15.8794, '10:30', '20:00', 45000, 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/75/Hoi_An_lanterns_at_night.jpg/800px-Hoi_An_lanterns_at_night.jpg', 'Quán cơm gà lâu đời nhất nhì Hội An với thịt gà tơ vàng ươm.', '1 giờ'),
        ];

        // Xóa hết data cũ để insert data mới cho sạch (chỉ khi seeder)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('dia_diem')->truncate();
        DB::table('dia_diem')->insert($data);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
            'nguon_tao' => 'doi_tac',
            'ma_doi_tac_tao' => (string) rand(1, 2),
            'trang_thai_duyet' => 'approved',
            'ly_do_tu_choi' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
