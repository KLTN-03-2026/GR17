<?php

namespace App\Http\Controllers;

use App\Models\KeHoach;
use App\Models\HoatDongChiTiet;
use App\Models\ThanhVienNhom;
use App\Models\Nhom;
use App\Models\Tour;
use App\Models\DiaDiem;
use App\Services\AITourGuideService;
use App\Services\MapLocationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AIPlannerController extends Controller
{
    protected $aiTourGuideService;
    protected $mapLocationService;

    public function __construct(AITourGuideService $aiService, MapLocationService $mapService)
    {
        $this->aiTourGuideService = $aiService;
        $this->mapLocationService = $mapService;
    }

    /**
     * Đề xuất các địa điểm dựa trên thông tin form nhập vào
     */
    public function suggestLocations(Request $request)
    {
        set_time_limit(180);

        try {
            $request->validate([
                'diem_den' => 'required|string',
                'so_ngay' => 'required|integer|min:1|max:7',
                'ngan_sach' => 'nullable|numeric',
            ], [
                'diem_den.required' => 'Vui lòng nhập điểm đến cho chuyến đi.',
                'diem_den.string' => 'Điểm đến không hợp lệ.',
                'so_ngay.required' => 'Vui lòng nhập số ngày.',
                'so_ngay.integer' => 'Số ngày phải là một số nguyên.',
                'so_ngay.min' => 'Thời gian đi tối thiểu là 1 ngày.',
                'so_ngay.max' => 'Hệ thống AI hiện giới hạn lên lịch trình tối đa 7 ngày.',
                'ngan_sach.numeric' => 'Ngân sách phải là một số hợp lệ.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'code' => 'VALIDATION_ERROR',
                'errors' => $e->validator->errors()
            ], 422);
        }

        $diemDen = $request->input('diem_den');
        $soNgay = $request->input('so_ngay');
        $nganSach = $request->input('ngan_sach') ?: 0;
        $soThich = $request->input('so_thich', []);
        $moTa = $request->input('mo_ta_chuyen_di', '');

        try {
            // Lấy danh sách địa điểm liên quan từ CSDL
            $diaDiemsDB = DiaDiem::where('dia_chi', 'LIKE', '%' . $diemDen . '%')
                ->orWhere('ten_dia_diem', 'LIKE', '%' . $diemDen . '%')
                ->limit(20)->get();

            // Xin đề xuất từ AI
            $aiResponse = $this->aiTourGuideService->suggestLocations($diemDen, $soNgay, $nganSach, $soThich, $diaDiemsDB, $moTa);

            return response()->json([
                'success' => true,
                'data' => $aiResponse,
                'message' => 'Lấy danh sách địa điểm gợi ý thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('AI Suggestion Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra khi lấy danh sách gợi ý: ' . $e->getMessage(),
                'code' => 'AI_ERROR'
            ], 500);
        }
    }

    /**
     * Gọi luồng AI sinh lịch trình
     */
    public function generateItinerary(Request $request)
    {
        set_time_limit(180);

        try {
            $request->validate([
                'diem_den' => 'required|string',
                'so_ngay' => 'required|integer|min:1|max:7',
                'ngan_sach' => 'nullable|numeric',
            ], [
                'diem_den.required' => 'Vui lòng nhập điểm đến cho chuyến đi.',
                'diem_den.string' => 'Điểm đến không hợp lệ.',
                'so_ngay.required' => 'Vui lòng nhập số ngày.',
                'so_ngay.integer' => 'Số ngày phải là một số nguyên.',
                'so_ngay.min' => 'Thời gian đi tối thiểu là 1 ngày.',
                'so_ngay.max' => 'Hệ thống AI hiện giới hạn lên lịch trình tối đa 7 ngày.',
                'ngan_sach.numeric' => 'Ngân sách phải là một số hợp lệ.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'code' => 'VALIDATION_ERROR',
                'errors' => $e->validator->errors()
            ], 422);
        }

        $diemDen = $request->input('diem_den');
        $soNgay = $request->input('so_ngay');
        $nganSach = $request->input('ngan_sach') ?: 0;
        $soThich = $request->input('so_thich', []);
        $moTa = $request->input('mo_ta_chuyen_di', '');
        $selectedLocations = $request->input('selectedLocations', []);

        try {
            $newSyncLocs = [];
            // 1. Đồng bộ điểm đến chính và các điểm khách đã chọn
            try {
                $loc = $this->mapLocationService->findOrSyncLocation($diemDen);
                if ($loc && $loc->wasRecentlyCreated)
                    $newSyncLocs[] = $loc;
            } catch (\Exception $e) {
                Log::warning("Could not sync main destination: " . $e->getMessage());
            }

            foreach ($selectedLocations as $locName) {
                try {
                    $loc = $this->mapLocationService->findOrSyncLocation($locName);
                    if ($loc && $loc->wasRecentlyCreated)
                        $newSyncLocs[] = $loc;
                } catch (\Exception $e) {
                    Log::warning("Could not sync selected location '$locName': " . $e->getMessage());
                }
            }

            // 1.5 Ước lượng giá hàng loạt (Batch) cho các điểm mới để tiết kiệm x5 quota
            if (!empty($newSyncLocs)) {
                $this->mapLocationService->batchEstimatePrices($newSyncLocs);
            }

            // 2. Lấy danh sách địa điểm liên quan từ CSDL
            // Lấy các điểm ở vùng lân cận điểm đến
            $diaDiemsDB = DiaDiem::where('dia_chi', 'LIKE', '%' . $diemDen . '%')
                ->orWhere('ten_dia_diem', 'LIKE', '%' . $diemDen . '%')
                ->limit(15)->get();

            // Đảm bảo các điểm khách đã chọn cũng có trong danh sách gửi cho AI
            $selectedLocsDB = DiaDiem::whereIn('ten_dia_diem', $selectedLocations)->get();
            $diaDiemsDB = $diaDiemsDB->merge($selectedLocsDB)->unique('ma_dia_diem');

            // 3. Lấy danh sách Tour phù hợp (Tìm theo từ khóa điểm đến)
            $toursDB = Tour::with('chiTietTours.diaDiem')
                ->where('ten_tour', 'LIKE', '%' . $diemDen . '%')
                ->orWhere('mo_ta', 'LIKE', '%' . $diemDen . '%')
                ->get();

            // 4. Sinh kịch bản qua AI
            $aiResponse = $this->aiTourGuideService->generatePlan($diemDen, $soNgay, $nganSach, $soThich, $diaDiemsDB, $toursDB, $selectedLocations, $moTa);

            return response()->json([
                'success' => true,
                'data' => $aiResponse,
                'thongTinChuyenDi' => [
                    'diemDen' => $diemDen,
                    'soNgay' => $soNgay,
                    'nganSach' => $nganSach,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('AI Generator Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra khi tạo kế hoạch: ' . $e->getMessage(),
                'code' => 'AI_ERROR'
            ], 500);
        }
    }

    /**
     * Lưu trữ lịch trình AI vào hệ thống Database
     */
    public function saveItinerary(Request $request)
    {
        $request->validate([
            'ma_khach_hang' => 'required|string|exists:khach_hang,Ma_khach_hang',
            'ket_qua_ai' => 'required|array',
            'thong_tin_chuyen_di' => 'required|array',
        ]);

        $maKhachHang = $request->input('ma_khach_hang');
        $aiData = $request->input('ket_qua_ai');
        $itineraryData = $request->input('thong_tin_chuyen_di');

        try {
            // 1. Khởi tạo Nhóm
            $nhom = Nhom::create([
                'ten_nhom' => 'Nhóm Hành Trình ' . ($aiData['tieuDe'] ?? 'AI'),
            ]);

            ThanhVienNhom::create([
                'Ma_nhom' => $nhom->Ma_nhom,
                'Ma_khach_hang' => $maKhachHang,
                // Mặc định quyền trưởng nhóm nếu có thiết kế
            ]);

            // 2. Tạo Kế hoạch tổng
            $soNgay = $itineraryData['soNgay'] ?? 1;
            $keHoach = KeHoach::create([
                'ma_nhom' => $nhom->Ma_nhom,
                'ten_ke_hoach' => $aiData['tieuDe'] ?? 'Kế hoạch AI',
                'mo_ta' => 'Tạo bởi AI cho điểm đến ' . ($itineraryData['diemDen'] ?? ''),
                'so_nguoi' => 1,
                'ngay_bat_dau' => now()->toDateString(),
                'ngay_ket_thuc' => now()->addDays($soNgay - 1)->toDateString(),
                'ngan_sach_du_kien' => preg_replace('/\D/', '', $aiData['tongChiPhi'] ?? 0) ?? 0,
                'tong_chi_phi' => 0,
                'trang_thai' => 0,
                'nguon_tao' => 'ai',
                'du_lieu_ai' => $aiData,
            ]);

            // 3. Khởi tạo Hoạt động chi tiết
            if (isset($aiData['lichTrinh']) && is_array($aiData['lichTrinh'])) {
                $dayCount = 0;
                foreach ($aiData['lichTrinh'] as $day) {
                    $ngayCuThe = now()->addDays($dayCount)->toDateString();
                    if (isset($day['danhSachHoatDong']) && is_array($day['danhSachHoatDong'])) {
                        foreach ($day['danhSachHoatDong'] as $act) {
                            $gioBatDau = match ($act['buoi'] ?? 'BUOI SANG') {
                                'BUOI SANG' => '08:00:00',
                                'BUOI CHIEU' => '14:00:00',
                                'BUOI TOI' => '19:00:00',
                                default => '08:00:00'
                            };

                            $maTourHanhDong = $act['ma_tour'] ?? null;
                            $ghiChu = null;
                            if ($maTourHanhDong) {
                                $tourInfo = Tour::find($maTourHanhDong);
                                $ghiChu = "Thuộc tour: " . ($tourInfo->ten_tour ?? $maTourHanhDong);
                            }

                            $maDiaDiem = $act['ma_dia_diem'] ?? null;
                            if (empty($maDiaDiem) || $maDiaDiem === "null" || $maDiaDiem == 0) {
                                try {
                                    $tieuDe = !empty($act['tieuDe']) ? $act['tieuDe'] : ($itineraryData['diemDen'] ?? 'Điểm đến');
                                    $loc = $this->mapLocationService->findOrSyncLocation($tieuDe);
                                    if ($loc) {
                                        $maDiaDiem = $loc->ma_dia_diem;
                                    }
                                } catch (\Exception $e) {
                                }

                                if (!$maDiaDiem) {
                                    $locFallback = DiaDiem::firstOrCreate(
                                        ['ten_dia_diem' => $itineraryData['diemDen'] ?? 'Điểm dừng chân'],
                                        [
                                            'dia_chi' => $itineraryData['diemDen'] ?? 'Việt Nam',
                                            'loai' => 1,
                                            'kinh_do' => '108.2022',
                                            'vi_do' => '16.0544',
                                            'gia_giao_dong' => 0,
                                            'hinh_anh' => 'https://picsum.photos/400/300?random=' . rand(1, 1000),
                                            'mo_ta' => 'Điểm dừng chân tạm thời do AI sinh ra',
                                            'gio_mo_cua' => '00:00:00',
                                            'gio_dong_cua' => '23:59:59',
                                        ]
                                    );
                                    $maDiaDiem = $locFallback->ma_dia_diem;
                                }
                            }

                            HoatDongChiTiet::create([
                                'ma_ke_hoach' => $keHoach->ma_ke_hoach,
                                'ma_nhom' => $nhom->Ma_nhom,
                                'ma_dia_diem' => $maDiaDiem,
                                'ma_tour' => $maTourHanhDong,
                                'ghi_chu' => $ghiChu,
                                'gio_bat_dau' => $gioBatDau,
                                'gio_ket_thuc' => Carbon::parse($gioBatDau)->addHours(2)->toTimeString(),
                                'ngay_cu_the' => $ngayCuThe,
                            ]);
                        }
                    }
                    $dayCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Lưu lộ trình thành công!',
                'data' => [
                    'ma_ke_hoach' => $keHoach->ma_ke_hoach,
                    'ma_nhom' => $nhom->Ma_nhom,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Save AI itinerary failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lưu kế hoạch vào hệ thống.',
            ], 500);
        }
    }

    /**
     * Phương thức dùng để Debug trực tiếp Gemini.
     */
    public function testGemini()
    {
        $keys = [env('GEMINI_API_KEY'), env('GEMINI_API_KEY_2')];
        $modelsToTest = ['gemini-2.0-flash-lite', 'gemini-1.5-flash-8b', 'gemini-2.5-flash', 'gemini-3.1-flash-lite-preview'];
        $results = [];

        foreach ($keys as $index => $key) {
            if (!$key)
                continue;

            $keyResults = [];
            foreach ($modelsToTest as $model) {
                try {
                    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}";
                    $response = Http::withoutVerifying()->post($url, [
                        'contents' => [['parts' => [['text' => 'Hi']]]]
                    ]);

                    if ($response->successful()) {
                        $keyResults[$model] = "✅ HOẠT ĐỘNG OK";
                    } else {
                        $errorMsg = data_get($response->json(), 'error.message');
                        if (str_contains($errorMsg, 'limit: 0')) {
                            $keyResults[$model] = "❌ Bị khóa (Limit 0)";
                        } else if ($response->status() == 404) {
                            $keyResults[$model] = "🚫 Không tìm thấy model";
                        } else {
                            $keyResults[$model] = "⚠️ Lỗi: " . $response->status();
                        }
                    }
                } catch (\Exception $e) {
                    $keyResults[$model] = "💥 Lỗi kết nối";
                }
            }
            $results["Key_" . ($index + 1)] = $keyResults;
        }

        return response()->json([
            'message' => 'Kết quả kiểm tra đa mẫu Gemini',
            'results' => $results
        ]);
    }

    /**
     * Phương thức dùng để Debug trực tiếp OpenAI.
     */
    public function testOpenAI()
    {
        // Key được cung cấp bởi user
        $key = 'sk-a43b4b29ac739b8c-i63bkg-451abc95';
        $modelsToTest = ['gpt-3.5-turbo', 'gpt-4o', 'gpt-4-turbo'];
        $results = [];

        foreach ($modelsToTest as $model) {
            try {
                $url = "https://api.openai.com/v1/chat/completions";
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $key,
                    'Content-Type' => 'application/json',
                ])->withoutVerifying()->post($url, [
                            'model' => $model,
                            'messages' => [
                                ['role' => 'user', 'content' => 'Hi']
                            ],
                            'max_tokens' => 10
                        ]);

                if ($response->successful()) {
                    $results[$model] = "✅ HOẠT ĐỘNG OK - Trả lời: " . data_get($response->json(), 'choices.0.message.content');
                } else {
                    $errorMsg = data_get($response->json(), 'error.message');
                    $results[$model] = "⚠️ Lỗi: " . $response->status() . " - " . $errorMsg;
                }
            } catch (\Exception $e) {
                $results[$model] = "💥 Lỗi kết nối: " . $e->getMessage();
            }
        }

        return response()->json([
            'message' => 'Kết quả kiểm tra đa mẫu OpenAI',
            'results' => ['OpenAI_Key' => $results]
        ]);
    }
}
