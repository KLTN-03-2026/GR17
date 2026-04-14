<?php

namespace App\Services;

use App\Models\DiaDiem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MapLocationService
{
    /**
     * Tìm hoặc lưu một địa điểm từ OpenStreetMap (Nominatim).
     * @param string $keyword Tên địa điểm khách nhập
     * @return DiaDiem|null
     */
    public function findOrSyncLocation(string $keyword): ?DiaDiem
    {
        // 1. Tìm trong DB trước (tìm gần đúng theo tên)
        $existing = DiaDiem::where('ten_dia_diem', 'LIKE', '%' . $keyword . '%')
            ->first();

        if ($existing) {
            return $existing;
        }

        // 2. Không có -> Gọi API OpenStreetMap (Nominatim API)
        // Nominatim miễn phí thì không cần truyền key, nhưng sử dụng header User-Agent đúng chuẩn
        try {
            $url = 'https://nominatim.openstreetmap.org/search';
            $response = Http::withHeaders([
                'User-Agent' => 'ExplorerVN_App/1.0 (doantotnghiep)',
            ])->get($url, [
                'q' => $keyword . ', Vietnam',
                'format' => 'json',
                'limit' => 1,
            ]);

            if ($response->successful() && !empty($response->json())) {
                $data = $response->json()[0];
                
                $lat = $data['lat'] ?? null;
                $lon = $data['lon'] ?? null;
                $name = $data['name'] ?? $keyword;
                $address = $data['display_name'] ?? $keyword;

                $lat = $data['lat'] ?? null;
                $lon = $data['lon'] ?? null;
                $name = $data['name'] ?? $keyword;
                $address = $data['display_name'] ?? $keyword;

                return DiaDiem::create([
                    'ten_dia_diem' => $name,
                    'dia_chi' => $address,
                    'loai' => 1, // Mặc định là địa điểm du lịch (kiểu integer)
                    'kinh_do' => $lon,
                    'vi_do' => $lat,
                    'gia_giao_dong' => 0, // Sẽ được cập nhật hàng loạt (batch) sau
                    'hinh_anh' => 'https://picsum.photos/400/300?random=' . rand(1, 1000), 
                    'mo_ta' => 'Địa điểm được lấy từ bản đồ: ' . $name,
                    'gio_mo_cua' => '08:00:00',
                    'gio_dong_cua' => '22:00:00',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('OpenStreetMap API error: ' . $e->getMessage());
        }

        return null; // Trả về null nếu không tìm thấy trên map
    }



    /**
     * Ước lượng giá cho nhiều địa điểm cùng lúc để tiết kiệm quota.
     */
    public function batchEstimatePrices(array $diaDiems): void
    {
        if (empty($diaDiems)) return;

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) return;

        $locationList = "";
        foreach ($diaDiems as $dd) {
            $locationList .= "- {$dd->ten_dia_diem} ({$dd->dia_chi})\n";
        }

        try {
            $prompt = "Bạn là chuyên gia du lịch. Hãy ước lượng giá vé tham quan trung bình (VNĐ) cho danh sách các địa điểm sau:\n$locationList\n
            Hãy trả về một mảng JSON duy nhất, mỗi phần tử có 'ten_dia_diem' và 'gia_uoc_luong' (là con số nguyên).
            Ví dụ: [{\"ten_dia_diem\": \"...\", \"gia_uoc_luong\": 50000}]";

        $keys = [env('GEMINI_API_KEY'), env('GEMINI_API_KEY_2')];
        $lastResponse = null;

        foreach ($keys as $key) {
            if (!$key) continue;

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$key}";
            $response = Http::withoutVerifying()->post($url, [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['response_mime_type' => 'application/json']
            ]);

            if ($response->successful()) {
                $aiText = data_get($response->json(), 'candidates.0.content.parts.0.text');
                $prices = json_decode($aiText, true);

                if (is_array($prices)) {
                    foreach ($prices as $item) {
                        DiaDiem::where('ten_dia_diem', $item['ten_dia_diem'])
                            ->update(['gia_giao_dong' => $item['gia_uoc_luong'] ?? 0]);
                    }
                }
                return; // Thành công
            }
            $lastResponse = $response;
        }

        if ($lastResponse) {
            Log::error('Batch estimate price failed for all keys: ' . $lastResponse->body());
        }
        } catch (\Exception $e) {
            Log::error('Batch estimate price error: ' . $e->getMessage());
        }
    }
}
