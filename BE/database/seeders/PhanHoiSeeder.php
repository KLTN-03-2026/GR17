<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\KhachHang;
use Carbon\Carbon;

class PhanHoiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $khachHangs = KhachHang::pluck('Ma_khach_hang')->toArray();
        $kh1 = $khachHangs[0] ?? null;
        $kh2 = $khachHangs[1] ?? $kh1;
        $kh3 = $khachHangs[2] ?? $kh2;

        $phanHois = [
            [
                'ma_khach_hang' => $kh1,
                'tieu_de' => 'Không thể thanh toán qua quét mã QR',
                'loai' => 'Lỗi',
                'mo_ta' => 'Khi tôi tiến hành quét mã QR để thanh toán tour đi Đà Nẵng, hệ thống báo lỗi không nhận diện được ngân hàng.',
                'url_trang_loi' => 'http://localhost:5173/khach-hang/tour/T001/thanh-toan',
                'trang_thai' => 'Mới',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'ma_khach_hang' => $kh2,
                'tieu_de' => 'Gợi ý thêm tính năng lọc địa điểm',
                'loai' => 'Góp ý',
                'mo_ta' => 'Mình thấy ứng dụng rất tốt, nhưng nếu có thêm bộ lọc địa điểm theo giá vé vào cửa thì sẽ tuyệt vời hơn.',
                'url_trang_loi' => 'http://localhost:5173/khach-hang/dia-diem',
                'trang_thai' => 'Đang xử lý',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subHours(12),
            ],
            [
                'ma_khach_hang' => null, // Khách vãng lai
                'tieu_de' => 'Lỗi hiển thị hình ảnh trên mobile',
                'loai' => 'Lỗi',
                'mo_ta' => 'Hình ảnh ở trang chủ bị tràn màn hình khi xem bằng iPhone 13.',
                'url_trang_loi' => 'http://localhost:5173/',
                'trang_thai' => 'Đã giải quyết',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'ma_khach_hang' => $kh3,
                'tieu_de' => 'Không nhận được mã xác nhận email',
                'loai' => 'Lỗi',
                'mo_ta' => 'Tôi đã yêu cầu gửi lại mã xác nhận nhiều lần nhưng vẫn không nhận được email nào trong hộp thư.',
                'url_trang_loi' => 'http://localhost:5173/dang-ky',
                'trang_thai' => 'Mới',
                'created_at' => Carbon::now()->subHours(5),
                'updated_at' => Carbon::now()->subHours(5),
            ],
            [
                'ma_khach_hang' => $kh1,
                'tieu_de' => 'Yêu cầu hủy tour',
                'loai' => 'Khác',
                'mo_ta' => 'Tôi muốn hỏi thủ tục hủy tour T005 do bận việc đột xuất. Xin hướng dẫn giúp.',
                'url_trang_loi' => 'http://localhost:5173/khach-hang/lich-su-don-hang',
                'trang_thai' => 'Đang xử lý',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(2),
            ],
        ];

        DB::table('phan_hois')->insert($phanHois);
    }
}
