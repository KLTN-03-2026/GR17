<?php

namespace App\Http\Controllers;

use App\Models\CauHinhAi;
use App\Models\DiaDiem;
use App\Models\KeHoach;
use App\Models\ThanhVienNhom;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AIPlannerController extends Controller
{
    private const TYPE_GEMINI_API_KEY = 'gemini_api_key';
    private const TYPE_PEXELS_API_KEY = 'pexels_api_key';
    private const TYPE_GEMINI_MODEL_FALLBACKS = 'gemini_model_fallbacks';
    private const DEFAULT_GEMINI_MODELS = 'gemini-3.0-flash,gemini-3.1-pro,gemini-3.0-pro';

    public function generateItinerary(Request $request)
    {
        $request->validate([
            'diemDen' => 'required|string',
            'soNgay' => 'required|integer|min:1|max:7',
            'nganSach' => 'required|string',
            'soThich' => 'array',
        ]);

        $diemDen = trim((string) $request->input('diemDen'));
        $soNgay = (int) $request->input('soNgay');
        $nganSach = (string) $request->input('nganSach');
        $soThich = implode(', ', $request->input('soThich', []));

        $diaDiemsDB = DiaDiem::query()
            ->where('dia_chi', 'LIKE', '%' . $diemDen . '%')
            ->orWhere('ten_dia_diem', 'LIKE', '%' . $diemDen . '%')
            ->limit(10)
            ->get(['ten_dia_diem', 'mo_ta', 'hinh_anh', 'gia_giao_dong', 'loai']);

        $promptTemplate = $this->loadPromptTemplate();
        if ($promptTemplate === '') {
            return $this->failureResponse(
                'Lỗi cấu hình AI không hợp lệ.',
                'AI_UNAVAILABLE',
                false,
                500
            );
        }

        $apiKey = $this->resolveGeminiApiKey();
        if ($apiKey === '') {
            return $this->failureResponse(
                'Chưa cấu hình GEMINI_API_KEY.',
                'AI_AUTH',
                false,
                500
            );
        }

        $prompt = str_replace(
            ['{diemDen}', '{soNgay}', '{nganSach}', '{soThich}', '{dbJson}'],
            [$diemDen, $soNgay, $nganSach, $soThich, $diaDiemsDB->toJson(JSON_UNESCAPED_UNICODE)],
            $promptTemplate
        );

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json',
            ],
        ];

        $candidateModels = $this->resolveGeminiModels();
        $lastFailure = [
            'code' => 'AI_UNAVAILABLE',
            'message' => 'Không thể nhận dữ liệu từ AI.',
            'retryable' => true,
            'status' => null,
        ];

        try {
            foreach ($candidateModels as $modelName) {
                $url = sprintf(
                    'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
                    $modelName,
                    $apiKey
                );

                $response = Http::withoutVerifying()->timeout(60)->post($url, $payload);
                if (! $response->successful()) {
                    $providerMessage = (string) ($response->json('error.message') ?? '');
                    $lastFailure = $this->mapAiFailure($response->status(), $providerMessage);

                    Log::warning('Gemini request failed', [
                        'model' => $modelName,
                        'status' => $response->status(),
                        'error_message' => $providerMessage,
                    ]);

                    if (in_array($response->status(), [401, 403], true)) {
                        break;
                    }

                    continue;
                }

                $aiText = data_get($response->json(), 'candidates.0.content.parts.0.text');
                if (! is_string($aiText) || trim($aiText) === '') {
                    $lastFailure = [
                        'code' => 'AI_UNAVAILABLE',
                        'message' => 'AI tra ve noi dung rong.',
                        'retryable' => true,
                        'status' => null,
                    ];

                    continue;
                }

                $itineraryData = $this->decodeItineraryJson($aiText);
                if ($itineraryData === null) {
                    $lastFailure = [
                        'code' => 'AI_UNAVAILABLE',
                        'message' => 'AI tra ve JSON khong hop le.',
                        'retryable' => true,
                        'status' => null,
                    ];

                    continue;
                }

                if (isset($itineraryData['error'])) {
                    return $this->failureResponse(
                        (string) $itineraryData['error'],
                        'AI_UNAVAILABLE',
                        false,
                        400
                    );
                }

                if (isset($itineraryData['lichTrinh']) && is_array($itineraryData['lichTrinh'])) {
                    $itineraryData = $itineraryData['lichTrinh'];
                }

                if (! $this->isValidItineraryArray($itineraryData)) {
                    $lastFailure = [
                        'code' => 'AI_UNAVAILABLE',
                        'message' => 'AI tra ve cau truc lich trinh khong dung.',
                        'retryable' => true,
                        'status' => null,
                    ];

                    continue;
                }

                $itineraryData = $this->ensureItineraryDayCount(
                    $itineraryData,
                    $soNgay,
                    $diemDen,
                    $diaDiemsDB
                );

                $pexelsPhotos = $this->fetchPexelsPhotos($diemDen);
                $this->hydrateActivityImages($itineraryData, $pexelsPhotos);
                $coverImage = $this->pickCoverImage($pexelsPhotos, $itineraryData);

                return $this->successResponse(
                    $diemDen,
                    $soNgay,
                    $nganSach,
                    $itineraryData,
                    $coverImage,
                    'ai'
                );
            }
        } catch (\Throwable $e) {
            Log::error('AI Planner fatal error', [
                'message' => $e->getMessage(),
            ]);

            $lastFailure = [
                'code' => 'AI_UNAVAILABLE',
                'message' => 'Không thể kết nối đến AI lúc này.',
                'retryable' => true,
                'status' => null,
            ];
        }

        $fallbackItinerary = $this->buildFallbackItinerary($diemDen, $soNgay, $diaDiemsDB);
        if ($this->isValidItineraryArray($fallbackItinerary)) {
            $fallbackNotice = sprintf(
                'He thong dang dung lich trinh du phong do AI gap loi: %s',
                $lastFailure['message']
            );

            return $this->successResponse(
                $diemDen,
                $soNgay,
                $nganSach,
                $fallbackItinerary,
                $this->pickCoverImage([], $fallbackItinerary),
                'fallback',
                $fallbackNotice
            );
        }

        return $this->failureResponse(
            (string) $lastFailure['message'],
            (string) $lastFailure['code'],
            (bool) $lastFailure['retryable'],
            $this->statusForErrorCode((string) $lastFailure['code'])
        );
    }

    public function saveItinerary(Request $request)
    {
        $request->validate([
            'ma_khach_hang' => 'required|string|exists:khach_hang,Ma_khach_hang',
            'ma_nhom' => 'nullable|string|exists:nhom,Ma_nhom',
            'ten_ke_hoach' => 'nullable|string|max:150',
            'so_nguoi' => 'nullable|integer|min:1',
            'ngay_bat_dau' => 'nullable|date_format:Y-m-d',
            'ngay_ket_thuc' => 'nullable|date_format:Y-m-d',
            'thong_tin_chuyen_di' => 'required|array',
            'thong_tin_chuyen_di.soNgay' => 'nullable|integer|min:1|max:7',
            'ket_qua_ai' => 'required|array',
            'ket_qua_ai.lichTrinh' => 'required|array|min:1',
        ]);

        $maKhachHang = trim((string) $request->input('ma_khach_hang'));
        $maNhom = trim((string) $request->input('ma_nhom', ''));
        $thongTinChuyenDi = (array) $request->input('thong_tin_chuyen_di', []);
        $ketQuaAi = (array) $request->input('ket_qua_ai', []);

        if ($maNhom !== '') {
            $isMember = ThanhVienNhom::query()
                ->where('Ma_khach_hang', $maKhachHang)
                ->where('Ma_nhom', $maNhom)
                ->exists();

            if (! $isMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'Khách hàng không thuộc nhóm hành trình đã chọn.',
                    'code' => 'INVALID_GROUP_MEMBER',
                ], 422);
            }
        } else {
            $maNhom = (string) (ThanhVienNhom::query()
                ->where('Ma_khach_hang', $maKhachHang)
                ->orderBy('created_at')
                ->orderBy('Ma_nhom')
                ->value('Ma_nhom') ?? '');

            if ($maNhom === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chưa tham gia nhóm hành trình nào để lưu kế hoạch.',
                    'code' => 'GROUP_NOT_FOUND_FOR_CUSTOMER',
                ], 422);
            }
        }

        [$ngayBatDau, $ngayKetThuc, $soNgay] = $this->resolvePlanDateRange(
            $thongTinChuyenDi,
            $ketQuaAi,
            $request->input('ngay_bat_dau'),
            $request->input('ngay_ket_thuc')
        );

        $tenKeHoach = trim((string) $request->input('ten_ke_hoach', ''));
        if ($tenKeHoach === '') {
            $tenKeHoach = trim((string) ($ketQuaAi['tieuDe'] ?? ''));
        }
        if ($tenKeHoach === '') {
            $diemDen = trim((string) ($thongTinChuyenDi['diemDen'] ?? ''));
            $tenKeHoach = $diemDen !== ''
                ? 'Hành trình AI - ' . $diemDen
                : 'Hành trình AI';
        }

        $tongChiPhi = $this->parseCurrencyToNumber($ketQuaAi['tongChiPhi'] ?? null);
        if ($tongChiPhi <= 0) {
            $tongChiPhi = $this->parseCurrencyToNumber($thongTinChuyenDi['tongChiPhi'] ?? null);
        }

        $soNguoi = (int) $request->input('so_nguoi', 1);
        if ($soNguoi < 1) {
            $soNguoi = 1;
        }

        $payload = [
            'ma_nhom' => $maNhom,
            'ten_ke_hoach' => mb_substr($tenKeHoach, 0, 150),
            'mo_ta' => 'Kế hoạch được tạo và lưu từ AI Planner.',
            'so_nguoi' => $soNguoi,
            'ngay_bat_dau' => $ngayBatDau,
            'ngay_ket_thuc' => $ngayKetThuc,
            'ngan_sach_du_kien' => max(0, $tongChiPhi),
            'trang_thai' => 0,
        ];

        if (Schema::hasColumn('ke_hoach', 'nguon_tao')) {
            $payload['nguon_tao'] = 'ai';
        }

        if (Schema::hasColumn('ke_hoach', 'du_lieu_ai')) {
            $payload['du_lieu_ai'] = [
                'thong_tin_chuyen_di' => $thongTinChuyenDi,
                'ket_qua_ai' => $ketQuaAi,
                'meta' => [
                    'saved_at' => now()->toDateTimeString(),
                    'so_ngay' => $soNgay,
                ],
            ];
        }

        try {
            $keHoach = KeHoach::create($payload);
        } catch (\Throwable $e) {
            Log::error('Save AI itinerary failed', [
                'message' => $e->getMessage(),
                'payload_keys' => array_keys($payload),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Không thể lưu hành trình AI vào hệ thống.',
                'code' => 'SAVE_AI_PLAN_FAILED',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lưu hành trình AI thành công.',
            'data' => [
                'ma_ke_hoach' => $keHoach->ma_ke_hoach,
                'ma_nhom' => $keHoach->ma_nhom,
                'ten_ke_hoach' => $keHoach->ten_ke_hoach,
                'nguon_tao' => $keHoach->nguon_tao ?? 'ai',
            ],
        ], 201);
    }

    private function loadPromptTemplate(): string
    {
        $aiConfigController = new AIConfigController();
        $configResponse = $aiConfigController->getPrompt();
        $configData = method_exists($configResponse, 'getData')
            ? $configResponse->getData(true)
            : [];

        return trim((string) ($configData['data']['prompt'] ?? ''));
    }

    private function resolveGeminiModels(): array
    {
        $rawModels = $this->resolveSetting(
            self::TYPE_GEMINI_MODEL_FALLBACKS,
            (string) env('GEMINI_MODEL_FALLBACKS', self::DEFAULT_GEMINI_MODELS)
        );

        $modelsFromEnv = array_filter(array_map(
            'trim',
            explode(',', $rawModels)
        ));

        $defaultModels = [
            'gemini-3.0-flash',
            'gemini-3.1-pro',
            'gemini-3.0-pro',
        ];

        $models = array_values(array_unique(array_merge($modelsFromEnv, $defaultModels)));

        return $models === [] ? ['gemini-3.0-flash'] : $models;
    }

    private function decodeItineraryJson(string $rawText): ?array
    {
        $cleanText = preg_replace('/```(?:json)?\s*(.*?)\s*```/s', '$1', $rawText);
        $data = json_decode(trim((string) $cleanText), true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($data)) {
            return null;
        }

        return $data;
    }

    private function isValidItineraryArray($itineraryData): bool
    {
        if (! is_array($itineraryData) || $itineraryData === []) {
            return false;
        }

        $firstDay = reset($itineraryData);

        return is_array($firstDay)
            && isset($firstDay['danhSachHoatDong'])
            && is_array($firstDay['danhSachHoatDong']);
    }

    private function mapAiFailure(int $statusCode, string $providerMessage): array
    {
        if ($statusCode === 429) {
            return [
                'code' => 'AI_QUOTA',
                'message' => 'Vượt hạn mức quota AI. Vui lòng thử lại sau.',
                'retryable' => true,
                'status' => 429,
            ];
        }

        if ($statusCode === 503) {
            return [
                'code' => 'AI_OVERLOADED',
                'message' => 'Dich vu AI dang qua tai tam thoi.',
                'retryable' => true,
                'status' => 503,
            ];
        }

        if ($statusCode === 404) {
            return [
                'code' => 'AI_INVALID_MODEL',
                'message' => 'Model AI khong kha dung.',
                'retryable' => true,
                'status' => 404,
            ];
        }

        if (in_array($statusCode, [401, 403], true)) {
            return [
                'code' => 'AI_AUTH',
                'message' => 'API key AI khong hop le hoac khong co quyen truy cap.',
                'retryable' => false,
                'status' => $statusCode,
            ];
        }

        if ($providerMessage !== '') {
            return [
                'code' => 'AI_UNAVAILABLE',
                'message' => 'AI tra ve loi: ' . $providerMessage,
                'retryable' => true,
                'status' => $statusCode,
            ];
        }

        return [
            'code' => 'AI_UNAVAILABLE',
            'message' => 'AI tam thoi khong kha dung.',
            'retryable' => true,
            'status' => $statusCode,
        ];
    }

    private function fetchPexelsPhotos(string $diemDen): array
    {
        try {
            $pexelsKey = $this->resolvePexelsApiKey();
            if ($pexelsKey === '') {
                return [];
            }

            $pexelResponse = Http::withoutVerifying()
                ->timeout(30)
                ->withHeaders(['Authorization' => $pexelsKey])
                ->get('https://api.pexels.com/v1/search', [
                    'query' => $diemDen . ' Vietnam',
                    'per_page' => 15,
                ]);

            if (! $pexelResponse->successful()) {
                return [];
            }

            $photos = $pexelResponse->json('photos');

            return is_array($photos) ? $photos : [];
        } catch (\Throwable $e) {
            Log::warning('Pexels fetch failed', ['message' => $e->getMessage()]);

            return [];
        }
    }

    private function hydrateActivityImages(array &$itineraryData, array &$pexelsPhotos): void
    {
        $seed = 1;

        foreach ($itineraryData as &$day) {
            if (! isset($day['danhSachHoatDong']) || ! is_array($day['danhSachHoatDong'])) {
                continue;
            }

            foreach ($day['danhSachHoatDong'] as &$activity) {
                $hasImage = isset($activity['hinhanh']) && is_string($activity['hinhanh']) && trim($activity['hinhanh']) !== '';
                if ($hasImage) {
                    continue;
                }

                if (! empty($pexelsPhotos) && isset($pexelsPhotos[0]['src']['landscape'])) {
                    $photo = array_shift($pexelsPhotos);
                    $activity['hinhanh'] = $photo['src']['landscape'];
                } else {
                    $activity['hinhanh'] = 'https://picsum.photos/400/300?random=' . time() . $seed;
                }

                $seed++;
            }
            unset($activity);
        }
        unset($day);
    }

    private function buildFallbackItinerary(string $diemDen, int $soNgay, Collection $diaDiemsDB): array
    {
        $timeSlots = [
            ['label' => 'BUOI SANG', 'iconClass' => 'icon--morning', 'icon' => 'fas fa-sun', 'duration' => '3 gio'],
            ['label' => 'BUOI CHIEU', 'iconClass' => 'icon--afternoon', 'icon' => 'fas fa-cloud-sun', 'duration' => '3 gio'],
            ['label' => 'BUOI TOI', 'iconClass' => 'icon--evening', 'icon' => 'fas fa-moon', 'duration' => '2 gio'],
        ];

        $weekdayLabels = ['Thu nhat', 'Thu hai', 'Thu ba', 'Thu tu', 'Thu nam', 'Thu sau', 'Thu bay'];
        $activityPool = $this->buildFallbackActivityPool($diaDiemsDB, $diemDen);

        if ($activityPool === []) {
            $activityPool = [
                [
                    'title' => 'Trung tam ' . mb_strtoupper($diemDen, 'UTF-8'),
                    'description' => 'Không có dữ liệu chi tiết trong hệ thống, đề xuất tham quan khu vực trung tâm.',
                    'image' => 'https://picsum.photos/400/300?random=' . time(),
                    'price' => 'Liên hệ',
                    'from_db' => false,
                ],
            ];
        }

        $result = [];
        $cursor = 0;
        $poolCount = count($activityPool);

        for ($day = 0; $day < $soNgay; $day++) {
            $dayActivities = [];

            foreach ($timeSlots as $slot) {
                $place = $activityPool[$cursor % $poolCount];
                $dayActivities[] = [
                    'buoi' => $slot['label'],
                    'iconClass' => $slot['iconClass'],
                    'icon' => $slot['icon'],
                    'tieuDe' => $place['title'],
                    'moTa' => $place['description'],
                    'hinhanh' => $place['image'],
                    'co_trong_db' => $place['from_db'],
                    'gia' => $place['price'],
                    'thoiLuong' => $slot['duration'],
                ];

                $cursor++;
            }

            $result[] = [
                'tieuDe' => 'Ngay ' . ($day + 1) . ': Hanh trinh du phong tai ' . mb_strtoupper($diemDen, 'UTF-8'),
                'thoiGian' => $weekdayLabels[$day % count($weekdayLabels)],
                'danhSachHoatDong' => $dayActivities,
            ];
        }

        return $result;
    }

    private function buildFallbackActivityPool(Collection $diaDiemsDB, string $diemDen): array
    {
        $pool = [];

        foreach ($diaDiemsDB as $diaDiem) {
            $title = trim((string) ($diaDiem->ten_dia_diem ?? ''));
            if ($title === '') {
                continue;
            }

            $description = trim((string) ($diaDiem->mo_ta ?? ''));
            if ($description === '') {
                $description = 'Địa điểm nổi bật tại ' . $diemDen . '.';
            }

            $image = trim((string) ($diaDiem->hinh_anh ?? ''));
            if ($image === '') {
                $image = 'https://picsum.photos/400/300?random=' . crc32($title);
            }

            $pool[] = [
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'price' => $this->formatPrice($diaDiem->gia_giao_dong ?? null),
                'from_db' => true,
            ];
        }

        return $pool;
    }

    private function formatPrice($value): string
    {
        if ($value === null || $value === '') {
            return 'Liên hệ';
        }

        if (is_numeric($value) && (float) $value <= 0) {
            return 'Miễn phí';
        }

        if (is_numeric($value)) {
            return number_format((float) $value, 0, ',', '.') . ' VND';
        }

        return 'Liên hệ';
    }

    private function pickCoverImage(array $pexelsPhotos, array $itineraryData): string
    {
        if (! empty($pexelsPhotos) && isset($pexelsPhotos[0]['src']['landscape'])) {
            return (string) $pexelsPhotos[0]['src']['landscape'];
        }

        if (
            isset($itineraryData[0]['danhSachHoatDong'][0]['hinhanh'])
            && is_string($itineraryData[0]['danhSachHoatDong'][0]['hinhanh'])
            && $itineraryData[0]['danhSachHoatDong'][0]['hinhanh'] !== ''
        ) {
            return $itineraryData[0]['danhSachHoatDong'][0]['hinhanh'];
        }

        return 'https://images.unsplash.com/photo-1596347958988-cb942eb22eb7?w=1000';
    }

    private function estimateBudget(string $nganSach, int $soNgay): int
    {
        $normalized = mb_strtolower(trim($nganSach), 'UTF-8');

        if (in_array($normalized, ['tiet kiem', 'tiết kiệm'], true)) {
            return 1500000 * $soNgay;
        }

        if (in_array($normalized, ['cao cap', 'cao cấp'], true)) {
            return 4500000 * $soNgay;
        }

        return 2500000 * $soNgay;
    }

    private function successResponse(
        string $diemDen,
        int $soNgay,
        string $nganSach,
        array $itineraryData,
        string $coverImage,
        string $planSource,
        ?string $notice = null
    ) {
        $meta = ['planSource' => $planSource];
        if ($notice !== null && $notice !== '') {
            $meta['notice'] = $notice;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tieuDe' => mb_strtoupper($diemDen, 'UTF-8'),
                'thoiGian' => $soNgay . ' Ngay ' . max(1, $soNgay - 1) . ' Dem',
                'nganSach' => $nganSach,
                'hinhAnh' => $coverImage,
                'lichTrinh' => $itineraryData,
                'tongChiPhi' => '~' . number_format($this->estimateBudget($nganSach, $soNgay), 0, ',', '.') . ' VND',
                'meta' => $meta,
            ],
        ]);
    }

    private function failureResponse(string $message, string $code, bool $retryable, int $status)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => $code,
            'retryable' => $retryable,
        ], $status);
    }

    private function statusForErrorCode(string $code): int
    {
        return match ($code) {
            'AI_AUTH' => 401,
            'AI_INVALID_MODEL' => 404,
            'AI_QUOTA' => 429,
            'AI_OVERLOADED' => 503,
            default => 503,
        };
    }

    private function resolveGeminiApiKey(): string
    {
        return $this->resolveSetting(
            self::TYPE_GEMINI_API_KEY,
            (string) env('GEMINI_API_KEY', '')
        );
    }

    private function resolvePexelsApiKey(): string
    {
        return $this->resolveSetting(
            self::TYPE_PEXELS_API_KEY,
            (string) env('PEXELS_API_KEY', '')
        );
    }

    private function resolveSetting(string $type, string $default = ''): string
    {
        $stored = CauHinhAi::where('loai', $type)->value('noi_dung');
        if (is_string($stored) && trim($stored) !== '') {
            return trim($stored);
        }

        return trim($default);
    }

    private function parseCurrencyToNumber($value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $clean = preg_replace('/[^\d]/', '', (string) $value);
        if (! is_string($clean) || $clean === '') {
            return 0;
        }

        return (float) $clean;
    }

    private function resolvePlanDateRange(
        array $thongTinChuyenDi,
        array $ketQuaAi,
        ?string $ngayBatDauInput,
        ?string $ngayKetThucInput
    ): array {
        $soNgay = (int) ($thongTinChuyenDi['soNgay'] ?? 0);
        if ($soNgay <= 0) {
            $soNgay = is_array($ketQuaAi['lichTrinh'] ?? null) ? count($ketQuaAi['lichTrinh']) : 1;
        }
        $soNgay = max(1, min(7, $soNgay));

        $ngayBatDau = $this->normalizeDateValue($ngayBatDauInput) ?? now()->toDateString();
        $ngayKetThuc = $this->normalizeDateValue($ngayKetThucInput);

        if ($ngayKetThuc === null) {
            $ngayKetThuc = Carbon::parse($ngayBatDau)->addDays($soNgay - 1)->toDateString();
        }

        if ($ngayKetThuc < $ngayBatDau) {
            $ngayKetThuc = $ngayBatDau;
        }

        return [$ngayBatDau, $ngayKetThuc, $soNgay];
    }

    private function normalizeDateValue(?string $value): ?string
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $raw)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function ensureItineraryDayCount(
        array $itineraryData,
        int $soNgay,
        string $diemDen,
        Collection $diaDiemsDB
    ): array {
        $normalized = [];

        foreach (array_values($itineraryData) as $index => $day) {
            if (! is_array($day)) {
                continue;
            }

            $activities = $day['danhSachHoatDong'] ?? $day['hoatDong'] ?? [];
            if (! is_array($activities)) {
                continue;
            }

            $activities = array_values(array_filter($activities, static fn ($item) => is_array($item)));
            if ($activities === []) {
                continue;
            }

            $day['danhSachHoatDong'] = $activities;
            unset($day['hoatDong']);

            $title = trim((string) ($day['tieuDe'] ?? ''));
            if ($title === '') {
                $day['tieuDe'] = 'Ngay ' . ($index + 1) . ': Hanh trinh tai ' . mb_strtoupper($diemDen, 'UTF-8');
            }

            $normalized[] = $day;
        }

        if ($normalized === []) {
            $normalized = $this->buildFallbackItinerary($diemDen, $soNgay, $diaDiemsDB);
        }

        $normalized = array_values($normalized);
        if (count($normalized) < $soNgay) {
            $fallback = $this->buildFallbackItinerary($diemDen, $soNgay, $diaDiemsDB);
            for ($dayIndex = count($normalized); $dayIndex < $soNgay; $dayIndex++) {
                if (isset($fallback[$dayIndex])) {
                    $normalized[] = $fallback[$dayIndex];
                }
            }
        } elseif (count($normalized) > $soNgay) {
            $normalized = array_slice($normalized, 0, $soNgay);
        }

        return array_values($normalized);
    }
}
