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
use Illuminate\Support\Str;
use App\Http\Requests\AIPlannerRequest;
use App\Http\Requests\SaveItineraryRequest;
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
    public function suggestLocations(AIPlannerRequest $request)
    {
        set_time_limit(180);

        $diemDen = $request->input('diem_den');
        $soNgay = $request->input('so_ngay');
        $nganSach = $request->input('ngan_sach') ?: 0;
        $soThich = $request->input('so_thich', []);
        $moTa = (string) $request->input('mo_ta_chuyen_di', '');

        try {
            // Lấy danh sách địa điểm liên quan từ CSDL
            $diaDiemsDB = DiaDiem::where('dia_chi', 'LIKE', '%' . $diemDen . '%')
                ->orWhere('ten_dia_diem', 'LIKE', '%' . $diemDen . '%')
                ->limit(50)->get();

            // Xin đề xuất từ AI (Khách sạn, Tham quan, Nhà hàng)
            $aiResponse = $this->aiTourGuideService->suggestLocations($diemDen, $soNgay, $nganSach, $soThich, $diaDiemsDB, $moTa);

            // Lấy danh sách Tour phù hợp từ CSDL để giới thiệu kèm theo
            $toursDB = Tour::with('chiTietTours.diaDiem')
                ->where('ten_tour', 'LIKE', '%' . $diemDen . '%')
                ->orWhere('mo_ta', 'LIKE', '%' . $diemDen . '%')
                ->limit(6)->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'ai_suggestions' => $aiResponse,
                    'tours' => $toursDB
                ],
                'message' => 'Lấy danh sách địa điểm và tour gợi ý thành công'
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
     * Lấy danh sách tour được tinh chỉnh dựa trên các địa điểm khách hàng đã chọn
     */
    public function suggestRefinedTours(Request $request)
    {
        $diemDen = $request->input('diem_den');
        $selectedLocations = $request->input('selected_locations', []);
        $ngayBatDau = $request->input('ngay_bat_dau');
        $ngayKetThuc = $request->input('ngay_ket_thuc');

        try {
            $query = Tour::with(['chiTietTours.diaDiem', 'tourKhoiHanhs'])
                ->where(function ($q) use ($diemDen) {
                    $q->where('ten_tour', 'LIKE', '%' . $diemDen . '%')
                      ->orWhere('mo_ta', 'LIKE', '%' . $diemDen . '%');
                });

            if (!empty($selectedLocations)) {
                $query->orWhereHas('chiTietTours.diaDiem', function ($q) use ($selectedLocations) {
                    $q->whereIn('ten_dia_diem', $selectedLocations);
                });
            }

            // Nếu có khoảng thời gian, ưu tiên lọc tour có lịch khởi hành nằm trong khoảng thời gian này
            if ($ngayBatDau && $ngayKetThuc) {
                $query->whereHas('tourKhoiHanhs', function ($q) use ($ngayBatDau, $ngayKetThuc) {
                    $q->whereDate('ngay_bat_dau', '>=', $ngayBatDau)
                      ->whereDate('ngay_ket_thuc', '<=', $ngayKetThuc);
                });
            }

            $tours = $query->get();

            // 2. Tính toán độ phù hợp (Match Score)
            // Điểm số dựa trên số lượng địa điểm khách chọn trùng với địa điểm trong tour
            $refinedTours = $tours->map(function ($tour) use ($selectedLocations, $ngayBatDau, $ngayKetThuc) {
                $tourLocationNames = $tour->chiTietTours->pluck('diaDiem.ten_dia_diem')->filter()->toArray();
                
                $matchCount = 0;
                foreach ($selectedLocations as $selected) {
                    // Kiểm tra khớp chính xác hoặc khớp tương đối (chứa trong tên)
                    foreach ($tourLocationNames as $tLoc) {
                        if (mb_stripos($tLoc, $selected) !== false || mb_stripos($selected, $tLoc) !== false) {
                            $matchCount++;
                            break; 
                        }
                    }
                }
                
                $tour->match_score = $matchCount;

                // Chọn lịch khởi hành phù hợp nhất
                if ($ngayBatDau && $ngayKetThuc) {
                    $lichPhuHop = $tour->tourKhoiHanhs->where('ngay_bat_dau', '>=', $ngayBatDau)
                        ->where('ngay_ket_thuc', '<=', $ngayKetThuc)
                        ->first();
                    if ($lichPhuHop) {
                        $tour->ma_thoi_gian_tour = $lichPhuHop->ma_thoi_gian_tour;
                        $tour->ngay_bat_dau_tour = $lichPhuHop->ngay_bat_dau;
                        $tour->ngay_ket_thuc_tour = $lichPhuHop->ngay_ket_thuc;
                        $tour->so_tien = $lichPhuHop->so_tien;
                    }
                }

                return $tour;
            });

            // 3. Sắp xếp theo điểm số giảm dần và lấy top kết quả
            $data = $refinedTours->sortByDesc('match_score')->values()->take(6);

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Lọc tour phù hợp thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Refined Tours Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lọc tour: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gọi luồng AI sinh lịch trình
     */
    public function generateItinerary(AIPlannerRequest $request)
    {
        set_time_limit(180);

        $diemDen = $request->input('diem_den');
        $soNgay = $request->input('so_ngay');
        $nganSach = $request->input('ngan_sach') ?: 0;
        $soThich = $request->input('so_thich', []);
        $moTa = (string) $request->input('mo_ta_chuyen_di', '');
        $ngayBatDau = $request->input('ngay_bat_dau', now()->toDateString());
        $ngayKetThuc = $request->input('ngay_ket_thuc', Carbon::parse($ngayBatDau)->addDays($soNgay - 1)->toDateString());
        $selectedLocations = $request->input('selectedLocations', []);
        $selectedTourRaw = $request->input('selected_tour');

        try {
            $selectedTour = null;
            if ($selectedTourRaw && !empty($selectedTourRaw['ma_tour'])) {
                $selectedTour = $selectedTourRaw;
                // Nếu có mã thời gian tour từ Frontend
                if (!empty($selectedTour['ma_thoi_gian_tour'])) {
                    $tk = \App\Models\TourKhoiHanh::find($selectedTour['ma_thoi_gian_tour']);
                    if ($tk) {
                        $selectedTour['ngay_bat_dau_tour'] = $tk->ngay_bat_dau;
                        $selectedTour['ngay_ket_thuc_tour'] = $tk->ngay_ket_thuc;
                    }
                } else {
                    // Nếu không có, cố gắng tìm một lịch khởi hành tự động
                    $tk = \App\Models\TourKhoiHanh::where('ma_tour', $selectedTour['ma_tour'])
                        ->whereDate('ngay_bat_dau', '>=', $ngayBatDau)
                        ->whereDate('ngay_ket_thuc', '<=', $ngayKetThuc)
                        ->first();
                    if ($tk) {
                        $selectedTour['ma_thoi_gian_tour'] = $tk->ma_thoi_gian_tour;
                        $selectedTour['ngay_bat_dau_tour'] = $tk->ngay_bat_dau;
                        $selectedTour['ngay_ket_thuc_tour'] = $tk->ngay_ket_thuc;
                    }
                }
            }

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
                ->limit(40)->get();

            // Đảm bảo các điểm khách đã chọn cũng có trong danh sách gửi cho AI
            $selectedLocsDB = DiaDiem::whereIn('ten_dia_diem', $selectedLocations)->get();
            $diaDiemsDB = $diaDiemsDB->merge($selectedLocsDB)->unique('ma_dia_diem');

            $queryTours = Tour::with('chiTietTours.diaDiem')
                ->where(function ($q) use ($diemDen) {
                    $q->where('ten_tour', 'LIKE', '%' . $diemDen . '%')
                      ->orWhere('mo_ta', 'LIKE', '%' . $diemDen . '%');
                });

            if (!empty($selectedLocations)) {
                $queryTours->orWhereHas('chiTietTours.diaDiem', function ($q) use ($selectedLocations) {
                    $q->whereIn('ten_dia_diem', $selectedLocations);
                });
            }

            $toursDB = $queryTours->get();

            // 4. Sinh kịch bản qua AI
            $aiResponse = $this->aiTourGuideService->generatePlan($diemDen, $soNgay, $nganSach, $soThich, $diaDiemsDB, $toursDB, $selectedLocations, $moTa, $selectedTour);

            // Bổ sung tọa độ (kinh_do, vi_do) cho các điểm đến để Frontend vẽ bản đồ
            if (isset($aiResponse['lichTrinh']) && is_array($aiResponse['lichTrinh'])) {
                foreach ($aiResponse['lichTrinh'] as &$day) {
                    if (isset($day['danhSachHoatDong']) && is_array($day['danhSachHoatDong'])) {
                        foreach ($day['danhSachHoatDong'] as &$act) {
                            $maDiaDiem = $act['ma_dia_diem'] ?? null;
                            $tieuDe = $act['tieuDe'] ?? '';
                            $act['kinh_do'] = null;
                            $act['vi_do'] = null;

                            if (!empty($maDiaDiem) && $maDiaDiem !== 'null') {
                                $loc = DiaDiem::find($maDiaDiem);
                                if ($loc) {
                                    $act['kinh_do'] = $loc->kinh_do;
                                    $act['vi_do'] = $loc->vi_do;
                                }
                            } else if (!empty($tieuDe)) {
                                $loc = DiaDiem::where('ten_dia_diem', 'LIKE', '%' . $tieuDe . '%')->first();
                                if ($loc) {
                                    $act['ma_dia_diem'] = $loc->ma_dia_diem;
                                    $act['kinh_do'] = $loc->kinh_do;
                                    $act['vi_do'] = $loc->vi_do;
                                }
                            }
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $aiResponse,
                'thongTinChuyenDi' => [
                    'diemDen' => $diemDen,
                    'soNgay' => $soNgay,
                    'nganSach' => $nganSach,
                    'ngayBatDau' => $ngayBatDau,
                    'ngayKetThuc' => $ngayKetThuc,
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
    public function saveItinerary(SaveItineraryRequest $request)
    {

        $maKhachHang = $request->input('ma_khach_hang');
        $aiData = $request->input('ket_qua_ai');
        $itineraryData = $request->input('thong_tin_chuyen_di');

        try {
            // 1. Khởi tạo Nhóm
            $tenNhom = 'Nhóm Hành Trình ' . ($aiData['tieuDe'] ?? 'AI');
            $nhom = Nhom::create([
                'ten_nhom' => Str::limit($tenNhom, 95),
            ]);

            ThanhVienNhom::create([
                'Ma_nhom' => $nhom->Ma_nhom,
                'Ma_khach_hang' => $maKhachHang,
                // Mặc định quyền trưởng nhóm nếu có thiết kế
            ]);

            // 2. Tạo Kế hoạch tổng
            $soNgay = $itineraryData['soNgay'] ?? 1;
            $ngayBatDau = $itineraryData['ngayBatDau'] ?? now()->toDateString();
            $ngayKetThuc = $itineraryData['ngayKetThuc'] ?? Carbon::parse($ngayBatDau)->addDays($soNgay - 1)->toDateString();
            $keHoach = KeHoach::create([
                'ma_nhom' => $nhom->Ma_nhom,
                'ten_ke_hoach' => Str::limit($aiData['tieuDe'] ?? 'Kế hoạch AI', 250),
                'mo_ta' => Str::limit('Tạo bởi AI cho điểm đến ' . ($itineraryData['diemDen'] ?? ''), 500),
                'so_nguoi' => 1,
                'ngay_bat_dau' => $ngayBatDau,
                'ngay_ket_thuc' => $ngayKetThuc,
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
                    $ngayCuThe = Carbon::parse($ngayBatDau)->addDays($dayCount)->toDateString();
                    if (isset($day['danhSachHoatDong']) && is_array($day['danhSachHoatDong'])) {
                        foreach ($day['danhSachHoatDong'] as $act) {
                            $gioBatDau = match ($act['buoi'] ?? 'BUOI SANG') {
                                'BUOI SANG' => '08:00:00',
                                'BUOI CHIEU' => '14:00:00',
                                'BUOI TOI' => '19:00:00',
                                default => '08:00:00'
                            };

                            $maTourHanhDong = $act['ma_tour'] ?? null;
                            $maThoiGianTour = $act['ma_thoi_gian_tour'] ?? null;
                            $ghiChu = null;
                            if ($maTourHanhDong) {
                                $tourInfo = \App\Models\Tour::find($maTourHanhDong);
                                $ghiChu = "Thuộc tour: " . ($tourInfo->ten_tour ?? $maTourHanhDong);
                                
                                // Nếu frontend có truyền ma_thoi_gian_tour trong selectedTour
                                $thongTinChuyenDiInput = $request->input('thong_tin_chuyen_di', []);
                                $selectedTourInput = $thongTinChuyenDiInput['selected_tour'] ?? null;
                                if (empty($maThoiGianTour) && !empty($selectedTourInput) && isset($selectedTourInput['ma_thoi_gian_tour'])) {
                                    if ($selectedTourInput['ma_tour'] == $maTourHanhDong) {
                                        $maThoiGianTour = $selectedTourInput['ma_thoi_gian_tour'];
                                    }
                                }

                                // Nếu vẫn chưa có chuyến đi cụ thể, tự động tìm một TourKhoiHanh phù hợp với ngày này
                                if (empty($maThoiGianTour)) {
                                    $chuyenDi = \App\Models\TourKhoiHanh::where('ma_tour', $maTourHanhDong)
                                        ->whereDate('ngay_bat_dau', '<=', $ngayCuThe)
                                        ->whereDate('ngay_ket_thuc', '>=', $ngayCuThe)
                                        ->first();
                                    if ($chuyenDi) {
                                        $maThoiGianTour = $chuyenDi->ma_thoi_gian_tour;
                                    }
                                }
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
                                'ma_thoi_gian_tour' => $maThoiGianTour,
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
        $modelsToTest = ['gemini-2.5-flash', 'gemini-2.5-pro', 'gemini-2.0-flash-exp', 'gemini-2.0-pro-exp'];
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
