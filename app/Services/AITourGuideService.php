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
    public function generatePlan(string $diemDen, int $soNgay, string $nganSach, array $soThich, $diaDiems, $tours, array $selectedLocations = [], string $moTa = '')
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
        
        $dbDiaDiemJson = json_encode($diaDiems, JSON_UNESCAPED_UNICODE);
        $dbTourJson = json_encode($tours, JSON_UNESCAPED_UNICODE);

        $prompt = str_replace(
            ['{diemDen}', '{soNgay}', '{nganSach}', '{soThich}', '{dbDiaDiemJson}', '{dbTourJson}', '{selectedLocs}', '{moTa}'],
            [$diemDen, $soNgay, $nganSach, $strSoThich, $dbDiaDiemJson, $dbTourJson, $strSelectedLocs, $moTa],
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

Dưới đây là một số Tour gợi ý từ hệ thống (nếu phù hợp):
{dbTourJson}

Và các Địa điểm tại điểm đến (để lấy mã và thông tin):
{dbDiaDiemJson}

YÊU CẦU ĐẶC BIỆT CHÚ Ý: BẠN BẮT BUỘC PHẢI THIẾT KẾ CÓ ĐỊA ĐIỂM HOẶC LỊCH TRÌNH KHÁCH SẠN/CHỖ NGHỈ VÀ NHÀ HÀNG ĂN UỐNG ĐẦY ĐỦ CHO KHÁCH HÀNG MỖI NGÀY.
Cụ thể, phải có ăn sáng/trưa/tối và ngủ tại khách sạn nào trong chuỗi "danhSachHoatDong".

YÊU CẦU:
1. Đánh giá xem có Tour nào trong danh sách Tour (dựa trên giá tiền, và các điểm đến) phù hợp với ngân sách và sở thích của khách hàng không.
2. Trả về một danh sách các Tour gợi ý (propossedTours) mà khách hàng có thể chọn.
3. Tạo một lịch trình mẫu tối ưu:
   - NẾU CÓ ĐỊA ĐIỂM BẮT BUỘC (tham khảo phần LƯU Ý ĐẶC BIỆT): BẠN CHẮC CHẮN PHẢI THÊM CÁC ĐỊA ĐIỂM NÀY VÀO TRONG LỊCH TRÌNH CÁC NGÀY.
   - BẮT BUỘC BỔ SUNG LỊCH NGHỈ NGƠI, KHÁCH SẠN, ĂN UỐNG MỖI NGÀY. 
   - Nếu có Tour phù hợp nhất: Ưu tiên đưa Tour đó vào lịch trình. Sắp xếp các hoạt động trong Tour theo đúng thứ tự của Tour đó.
   - Các hoạt động tự túc (không thuộc Tour): Sắp xếp thứ tự BUOI SANG, BUOI CHIEU, BUOI TOI sao cho tối ưu đường đi nhất.
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
          "ma_dia_diem": "Mã địa điểm từ DB nếu có",
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

Dưới đây là một số Địa điểm có sẵn trong cơ sở dữ liệu:
{dbDiaDiemJson}

YÊU CẦU:
Dựa vào các tiêu chí trên, hãy đề xuất một danh sách các địa điểm (tối đa 10 địa điểm) nên đi nhất tại điểm đến này.
BẮT BUỘC trả về 1 chuỗi JSON duy nhất dạng Array chứa các Object.
Mỗi Object gồm các thuộc tính:
- "ten_dia_diem": Tên địa điểm
- "mo_ta_ngan": Mô tả rất ngắn gọn lý do tại sao nên đi hoặc điểm đặc sắc (tối đa 15 chữ)
- "dia_chi": Địa chỉ ước lượng hoặc khu vực
- "hinhanh": BẮT BUỘC cung cấp URL hình ảnh THỰC TẾ của địa điểm này lấy từ Nguồn Wikipedia/Wikimedia Commons (Ví dụ: https://upload.wikimedia.org/wikipedia/commons/...). Nếu không tìm được link thực thế, hãy để chuỗi rỗng "". TUYỆT ĐỐI KHÔNG tự chế/bịa link ảnh.

MẪU KẾT QUẢ ĐẦU RA (JSON):
[
  {
    "ten_dia_diem": "Chợ Đà Lạt",
    "mo_ta_ngan": "Trung tâm mua sắm sầm uất",
    "dia_chi": "Phường 1, Đà Lạt",
    "hinhanh": "https://upload.wikimedia.org/wikipedia/commons/thumb/e/e7/Ch%E1%BB%A3_%C4%90%C3%A0_L%E1%BA%A1t.jpg/800px-Ch%E1%BB%A3_%C4%90%C3%A0_L%E1%BA%A1t.jpg"
  }
]
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
