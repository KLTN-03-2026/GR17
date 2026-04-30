<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\CauHinhAi;

class AITourGuideService
{
    /**
     * Gọi Gemini API để lập kế hoạch.
     */
    public function generatePlan(string $diemDen, int $soNgay, string $nganSach, array $soThich, $diaDiems, $tours, array $selectedLocations = [], string $moTa = '', $selectedTour = null)
    {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            throw new \Exception("Chưa cấu hình GEMINI_API_KEY trong .env");
        }

        $promptTemplate = CauHinhAi::where('loai', 'prompt_mac_dinh')->value('noi_dung');
        if (!$promptTemplate) {
            $promptTemplate = $this->getDefaultPrompt();
        }

        $strSoThich = implode(', ', $soThich);
        $strSelectedLocs = empty($selectedLocations) ? "Không có chỉ định đặc biệt." : "CÁC ĐỊA ĐIỂM BẮT BUỘC PHẢI CÓ TRONG LỊCH TRÌNH: " . implode(', ', $selectedLocations);
        $strSelectedTour = "";
        if ($selectedTour) {
            $strSelectedTour = "KHÁCH HÀNG ĐÃ CHỌN TOUR NÀY: " . ($selectedTour['ten_tour'] ?? '') . " (Mã: " . ($selectedTour['ma_tour'] ?? '') . "). ";
            if (!empty($selectedTour['ngay_bat_dau_tour']) && !empty($selectedTour['ngay_ket_thuc_tour'])) {
                $strSelectedTour .= "THỜI GIAN KHÁCH ĐI TOUR LÀ TỪ NGÀY " . $selectedTour['ngay_bat_dau_tour'] . " ĐẾN NGÀY " . $selectedTour['ngay_ket_thuc_tour'] . ". ";
                $strSelectedTour .= "YÊU CẦU CỰC KỲ QUAN TRỌNG: BẠN CẤM TUYỆT ĐỐI KHÔNG ĐƯỢC XẾP BẤT KỲ ĐỊA ĐIỂM HAY HOẠT ĐỘNG TỰ DO NÀO VÀO CÁC NGÀY MÀ KHÁCH ĐANG ĐI THEO TOUR (từ " . $selectedTour['ngay_bat_dau_tour'] . " đến " . $selectedTour['ngay_ket_thuc_tour'] . "). ĐỒNG THỜI, BẠN KHÔNG CẦN LÊN LỊCH TRÌNH CHI TIẾT CÁC HOẠT ĐỘNG TRONG TOUR VÀO CÁC NGÀY NÀY. CHỈ CẦN TẠO 1 HOẠT ĐỘNG DUY NHẤT MANG TÊN 'Tham gia tour' KÈM THEO MÃ TOUR CHO CÁC NGÀY ĐÓ ĐỂ GIỮ CHỖ LÀ ĐƯỢC!";
            } else {
                $strSelectedTour .= "Bạn KHÔNG CẦN liệt kê hay sắp xếp chi tiết các hoạt động trong tour này. Chỉ cần tạo một hoạt động chung 'Tham gia tour' kèm theo mã tour để giữ chỗ trong lịch trình.";
            }
        }
        
        $dbDiaDiemJson = json_encode($diaDiems, JSON_UNESCAPED_UNICODE);
        $dbTourJson = json_encode($tours, JSON_UNESCAPED_UNICODE);

        $prompt = str_replace(
            ['{diemDen}', '{soNgay}', '{nganSach}', '{soThich}', '{dbDiaDiemJson}', '{dbTourJson}', '{selectedLocs}', '{moTa}', '{selectedTour}'],
            [$diemDen, $soNgay, $nganSach, $strSoThich, $dbDiaDiemJson, $dbTourJson, $strSelectedLocs, $moTa, $strSelectedTour],
            $promptTemplate
        );

        $payload = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json',
            ],
        ];

        $model = 'gemini-2.5-flash'; 
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
        
        // Thử Key 1, nếu lỗi thử sang Key 2
        $keys = [env('GEMINI_API_KEY'), env('GEMINI_API_KEY_2')];
        $lastResponse = null;

        foreach ($keys as $key) {
            if (!$key) continue;

            $fullUrl = "{$url}?key={$key}";
            $response = Http::withoutVerifying()->timeout(90)->post($fullUrl, $payload);
            
            if ($response->successful()) {
                $text = data_get($response->json(), 'candidates.0.content.parts.0.text');
                return json_decode($text, true);
            }

            $lastResponse = $response;
            Log::warning("Gemini Key failed, trying next key... Error: " . $response->body());
        }

        if ($lastResponse) {
            Log::error('All Gemini keys failed: ' . $lastResponse->body());
            throw new \Exception("Không thể kết nối với Gemini AI.");
        }
    }
    
    public function getDefaultPrompt(): string
    {
        return <<<'PROMPT'
Bạn là chuyên gia thiết kế lịch trình du lịch thông minh tại Việt Nam.

Khách hàng muốn đi:
- Điểm đến: {diemDen}
- Số ngày: {soNgay}
- Ngân sách: {nganSach}
- Sở thích: {soThich}
- Mô tả mong muốn của chuyến đi: {moTa}
- LƯU Ý ĐẶC BIỆT: {selectedLocs}
- TOUR ĐÃ CHỌN: {selectedTour}

Dưới đây là một số Tour gợi ý từ hệ thống (nếu phù hợp):
{dbTourJson}

Và các Địa điểm tại điểm đến (để lấy mã và thông tin):
{dbDiaDiemJson}

YÊU CẦU ĐẶC BIỆT CHÚ Ý: 
1. BẠN BẮT BUỘC PHẢI THIẾT KẾ CÓ ĐỊA ĐIỂM HOẶC LỊCH TRÌNH KHÁCH SẠN/CHỖ NGHỈ VÀ NHÀ HÀNG ĂN UỐNG ĐẦY ĐỦ CHO KHÁCH HÀNG MỖI NGÀY.
2. ƯU TIÊN TUYỆT ĐỐI các địa điểm có sẵn trong danh sách "Địa điểm tại điểm đến" được cung cấp phía trên. Chỉ khi nào không tìm thấy địa điểm phù hợp trong danh sách đó mới đề xuất địa điểm bên ngoài.

YÊU CẦU:
1. Đánh giá xem có Tour nào trong danh sách "Tour gợi ý từ hệ thống" (dựa trên giá tiền, và các điểm đến) phù hợp với ngân sách và sở thích của khách hàng không. NẾU KHÔNG CÓ TOUR NÀO, BẮT BUỘC để mảng `propossedTours` rỗng.
2. Trả về một danh sách các Tour gợi ý (propossedTours) mà khách hàng có thể chọn. BẮT BUỘC CHỈ SỬ DỤNG CÁC TOUR ĐƯỢC CUNG CẤP TRONG DANH SÁCH, TUYỆT ĐỐI KHÔNG TỰ BỊA RA HOẶC TẠO RA TOUR MỚI VÀ MÃ TOUR MỚI (như CENT001...).
3. Tạo một lịch trình mẫu tối ưu:
   - NẾU CÓ TOUR ĐÃ CHỌN (tham khảo phần TOUR ĐÃ CHỌN): Bạn KHÔNG CẦN sắp xếp chi tiết các hoạt động trong tour. Chỉ cần tạo một hoạt động duy nhất mang tên "Tham gia tour" kèm theo `ma_tour` vào các ngày khách đi tour. NẾU CÓ THỜI GIAN ĐI TOUR CỤ THỂ, BẠN CẤM ĐƯỢC XẾP ĐỊA ĐIỂM TỰ ĐI VÀO THỜI GIAN ĐÓ.
   - NẾU CÓ ĐỊA ĐIỂM BẮT BUỘC (tham khảo phần LƯU Ý ĐẶC BIỆT): BẠN CHẮC CHẮN PHẢI THÊM CÁC ĐỊA ĐIỂM NÀY VÀO TRONG LỊCH TRÌNH CÁC NGÀY (nhưng phải TUYỆT ĐỐI TRÁNH đè lên các ngày khách đã có lịch đi tour).
   - BẮT BUỘC BỔ SUNG LỊCH NGHỈ NGƠI, KHÁCH SẠN, ĂN UỐNG MỖI NGÀY (ngoại trừ các ngày đã đi tour toàn thời gian). 
   - Ưu tiên đưa Tour đã chọn vào lịch trình trước, sau đó sắp xếp các địa điểm tự do xung quanh (vào các ngày/khung giờ khách rảnh rỗi) để tạo thành một hành trình hoàn chỉnh.
4. BẮT BUỘC chỉ trả về 1 chuỗi JSON duy nhất, dạng Object.

MẪU KẾT QUẢ ĐẦU RA (JSON):
{
  "tieuDe": "Hành trình vi vu ...",
  "tongChiPhi": "Ghi cụ thể giá tiền VNĐ",
  "propossedTours": [
    {
      "ma_tour": "ID từ DB",
      "ten_tour": "Tên tour",
      "gia": "Giá tiền",
      "ly_do_goi_y": "Tại sao tour này phù hợp?"
    }
  ],
  "lichTrinh": [
    {
      "tieuDe": "Ngày 1: ...",
      "thoiGian": "Thứ hai",
      "danhSachHoatDong": [
        {
          "buoi": "BUOI SANG",
          "tieuDe": "Tên địa điểm",
          "ma_dia_diem": "Mã địa điểm từ DB nếu có, nếu không có để null",
          "ma_tour": "Điền mã tour nếu hoạt động này thuộc tour, ngược lại null",
          "moTa": "Mô tả hoạt động",
          "gia": "Giá vé",
          "thoiLuong": "2 giờ"
        }
      ]
    }
  ]
}
PROMPT;
    }

    /**
     * Gọi Gemini API để đề xuất địa điểm.
     */
    public function suggestLocations(string $diemDen, int $soNgay, string $nganSach, array $soThich, $diaDiems, string $moTa = '')
    {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            throw new \Exception("Chưa cấu hình GEMINI_API_KEY trong .env");
        }

        $strSoThich = implode(', ', $soThich);
        $dbDiaDiemJson = json_encode($diaDiems, JSON_UNESCAPED_UNICODE);

        $prompt = <<<'PROMPT'
Bạn là chuyên gia du lịch am hiểu về các địa danh tại Việt Nam.

Khách hàng muốn đi du lịch và đang tìm kiếm địa điểm với các tiêu chí:
- Điểm đến chính: {diemDen}
- Số ngày dự kiến: {soNgay}
- Ngân sách: {nganSach}
- Sở thích/Yêu cầu: {soThich}
- Mô tả mong muốn của chuyến đi: {moTa}

Dưới đây là một số Địa điểm có sẵn trong cơ sở dữ liệu hệ thống (LÀM GỐC ĐỂ ĐỀ XUẤT):
{dbDiaDiemJson}

YÊU CẦU QUAN TRỌNG:
1. Bạn phải ƯU TIÊN TUYỆT ĐỐI việc lựa chọn các địa điểm từ danh sách "Địa điểm có sẵn" được cung cấp phía trên để đề xuất cho khách hàng.
2. Chỉ khi nào các địa điểm trong database không đủ số lượng hoặc không phù hợp với sở thích của khách hàng, bạn mới được phép tự đề xuất các địa điểm nổi tiếng khác bên ngoài.
3. Nếu địa điểm lấy từ database, bạn BẮT BUỘC phải giữ nguyên "ma_dia_diem" của địa điểm đó trong kết quả trả về.

BẮT BUỘC chia thành 3 phân loại sau:
1. Khách sạn / Chỗ nghỉ cho khách hàng (Tối đa 5 khách sạn)
2. Địa điểm tham quan (Tối đa 10 địa điểm)
3. Nhà hàng - Quán ăn (Tối đa 8 nhà hàng/quán ăn)

BẮT BUỘC trả về 1 chuỗi JSON duy nhất dạng Object chứa 3 mảng (khach_san, dia_diem_tham_quan, nha_hang_quan_an).
Mỗi Object trong mảng gồm các thuộc tính:
- "ma_dia_diem": Mã địa điểm từ database (nếu có), nếu không có (địa điểm ngoài) thì để null.
- "ten_dia_diem": Tên địa điểm
- "mo_ta_ngan": Mô tả rất ngắn gọn lý do tại sao nên đi hoặc điểm đặc sắc (tối đa 15 chữ)
- "dia_chi": Địa chỉ thực tế từ database hoặc địa chỉ ước lượng
- "la_dia_diem_he_thong": true nếu lấy từ database, false nếu đề xuất ngoài.
- "hinhanh": URL hình ảnh thực tế (ưu tiên lấy từ database nếu có, nếu không lấy từ Wikimedia Commons).

MẪU KẾT QUẢ ĐẦU RA (JSON):
{
  "khach_san": [
    {
      "ma_dia_diem": "ID001",
      "ten_dia_diem": "Khách sạn Mường Thanh",
      "mo_ta_ngan": "Tiện nghi 4 sao",
      "dia_chi": "Trung tâm",
      "la_dia_diem_he_thong": true,
      "hinhanh": "..."
    }
  ],
  "dia_diem_tham_quan": [
    {
      "ma_dia_diem": null,
      "ten_dia_diem": "Chợ Đà Lạt",
      "mo_ta_ngan": "Trung tâm mua sắm sầm uất",
      "dia_chi": "Phường 1, Đà Lạt",
      "la_dia_diem_he_thong": false,
      "hinhanh": "..."
    }
  ],
  "nha_hang_quan_an": [
    {
      "ma_dia_diem": "ID002",
      "ten_dia_diem": "Lẩu bò Ba Toa",
      "mo_ta_ngan": "Nổi tiếng thơm ngon",
      "dia_chi": "Hoàng Diệu, Đà Lạt",
      "la_dia_diem_he_thong": true,
      "hinhanh": "..."
    }
  ]
}
PROMPT;

        $prompt = str_replace(
            ['{diemDen}', '{soNgay}', '{nganSach}', '{soThich}', '{dbDiaDiemJson}', '{moTa}'],
            [$diemDen, $soNgay, $nganSach, $strSoThich, $dbDiaDiemJson, $moTa],
            $prompt
        );

        $payload = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json',
            ],
        ];

        $model = 'gemini-2.5-flash'; 
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
        
        $keys = [env('GEMINI_API_KEY'), env('GEMINI_API_KEY_2')];
        $lastResponse = null;

        foreach ($keys as $key) {
            if (!$key) continue;

            $fullUrl = "{$url}?key={$key}";
            $response = Http::withoutVerifying()->timeout(90)->post($fullUrl, $payload);
            
            if ($response->successful()) {
                $text = data_get($response->json(), 'candidates.0.content.parts.0.text');
                return json_decode($text, true);
            }

            $lastResponse = $response;
            Log::warning("Gemini Key failed, trying next key... Error: " . $response->body());
        }

        if ($lastResponse) {
            Log::error('All Gemini keys failed: ' . $lastResponse->body());
            throw new \Exception("Không thể kết nối với Gemini AI.");
        }
    }
}
