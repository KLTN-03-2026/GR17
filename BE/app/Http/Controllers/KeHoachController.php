<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKeHoachRequest;
use App\Http\Requests\UpdateKeHoachRequest;
use App\Models\DiaDiem;
use App\Models\HoatDongChiTiet;
use App\Models\KeHoach;
use App\Models\KhachHang;
use App\Models\Tour;
use App\Services\AITourGuideService;
use App\Services\CustomerOwnershipService;
use App\Services\MapLocationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KeHoachController extends Controller
{
    public function __construct(
        private readonly CustomerOwnershipService $ownershipService,
        private readonly AITourGuideService $aiTourGuideService,
        private readonly MapLocationService $mapLocationService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$this->ownershipService->ensureCustomerOrAdmin($user)) {
            return $this->forbiddenResponse('Bạn phải đăng nhập để xem kế hoạch.');
        }

        $query = KeHoach::query()
            ->with(['nhom', 'hoatDongChiTiets.diaDiem'])
            ->orderByDesc('ma_ke_hoach');

        if (!$this->ownershipService->isAdmin($user)) {
            /** @var \App\Models\KhachHang $user */
            $query->whereIn('ma_nhom', $this->ownershipService->groupIdsForCustomer($user->Ma_khach_hang));
        }

        $keHoach = $query->get();

        return response()->json([
            'success' => true,
            'data' => $keHoach,
        ]);
    }

    public function indexAll(Request $request): JsonResponse
    {
        if (!$this->ownershipService->isAdmin($request->user())) {
            return $this->forbiddenResponse('Chỉ admin mới có thể xem toàn bộ kế hoạch.');
        }

        $keHoach = KeHoach::query()->with('nhom')->orderByDesc('ma_ke_hoach')->get();

        return response()->json([
            'success' => true,
            'data' => $keHoach,
            'total' => $keHoach->count(),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$this->ownershipService->ensureCustomerOrAdmin($user)) {
            return $this->forbiddenResponse('Bạn phải đăng nhập để tìm kiếm kế hoạch.');
        }

        $keyword = trim((string) $request->query('keyword', ''));
        if ($keyword === '') {
            return response()->json([
                'success' => false,
                'message' => 'Từ khóa tìm kiếm không được để trống',
            ], 400);
        }

        $query = KeHoach::query();
        if (!$this->ownershipService->isAdmin($user)) {
            /** @var \App\Models\KhachHang $user */
            $query->whereIn('ma_nhom', $this->ownershipService->groupIdsForCustomer($user->Ma_khach_hang));
        }

        $searchType = $request->query('type', 'all');
        if ($searchType === 'ten_ke_hoach') {
            $query->where('ten_ke_hoach', 'like', '%' . $keyword . '%');
        } elseif ($searchType === 'ma_nhom') {
            $query->where('ma_nhom', 'like', '%' . $keyword . '%');
        } else {
            $query->where(function ($builder) use ($keyword): void {
                $builder->where('ten_ke_hoach', 'like', '%' . $keyword . '%')
                    ->orWhere('ma_nhom', 'like', '%' . $keyword . '%');
            });
        }

        $keHoach = $query->with('nhom')->orderBy('ten_ke_hoach')->get();

        return response()->json([
            'success' => true,
            'data' => $keHoach,
            'total' => $keHoach->count(),
        ]);
    }

    public function show(Request $request, string $ma_ke_hoach): JsonResponse
    {
        $keHoach = KeHoach::query()
            ->with(['nhom', 'hoatDongChiTiets.diaDiem.dichVuDiaDiems'])
            ->find($ma_ke_hoach);

        if (!$keHoach) {
            return response()->json([
                'success' => false,
                'message' => 'Kế hoạch không tồn tại',
            ], 404);
        }

        if (!$this->ownershipService->canAccessPlan($request->user(), $keHoach)) {
            return $this->forbiddenResponse();
        }

        return response()->json([
            'success' => true,
            'data' => $keHoach,
        ]);
    }

    public function generateAiTimeline(Request $request, string $ma_ke_hoach): JsonResponse
    {
        $keHoach = KeHoach::query()
            ->with(['hoatDongChiTiets'])
            ->find($ma_ke_hoach);

        if (!$keHoach) {
            return response()->json([
                'success' => false,
                'message' => 'Kế hoạch không tồn tại',
            ], 404);
        }

        if (!$this->ownershipService->canAccessPlan($request->user(), $keHoach)) {
            return $this->forbiddenResponse();
        }

        $replaceExisting = $request->boolean('replace_existing');
        if ($keHoach->hoatDongChiTiets->isNotEmpty() && !$replaceExisting) {
            return response()->json([
                'success' => false,
                'message' => 'Kế hoạch đã có lịch trình chi tiết. Truyền replace_existing=true nếu muốn tạo lại.',
            ], 409);
        }

        try {
            $result = DB::transaction(function () use ($keHoach, $replaceExisting): array {
                if ($replaceExisting) {
                    $keHoach->hoatDongChiTiets()->delete();
                }

                $aiData = $replaceExisting ? null : $keHoach->du_lieu_ai;
                $generationMode = 'ai';
                $notice = null;
                if (!is_array($aiData) || empty($this->extractAiDays($aiData))) {
                    try {
                        $aiData = $this->generateAiDataForPlan($keHoach);
                    } catch (\Throwable $exception) {
                        if (!$this->aiTourGuideService->isRetriableFailure($exception)) {
                            throw $exception;
                        }

                        Log::warning('Gemini unavailable for plan ' . $keHoach->ma_ke_hoach . ', switching to fallback draft: ' . $exception->getMessage());
                        $generationMode = 'fallback';
                        $notice = 'AI đang bận, hệ thống đã tạo bản nháp để bạn chỉnh tiếp.';
                        $aiData = $this->buildFallbackAiDataForPlan($keHoach, $notice);
                    }

                    $keHoach->forceFill(['du_lieu_ai' => $aiData])->save();
                } else {
                    $generationMode = $this->extractGenerationMode($aiData);
                    if ($generationMode === 'fallback') {
                        $notice = (string) ($aiData['notice'] ?? data_get($aiData, '_metadata.notice') ?: 'AI đang bận, hệ thống đã tạo bản nháp để bạn chỉnh tiếp.');
                    }
                }

                $created = $this->materializeAiTimeline($keHoach->fresh(), $aiData);
                if ($created === 0 && $generationMode !== 'fallback') {
                    $generationMode = 'fallback';
                    $notice = 'AI chưa gắn được địa điểm thật, hệ thống đã tạo bản nháp để bạn chỉnh tiếp.';
                    $aiData = $this->buildFallbackAiDataForPlan($keHoach->fresh(), $notice);
                    $keHoach->forceFill(['du_lieu_ai' => $aiData])->save();
                    $created = $this->materializeAiTimeline($keHoach->fresh(), $aiData);
                }

                return [
                    'created' => $created,
                    'plan' => $keHoach->fresh(['nhom', 'hoatDongChiTiets.diaDiem.dichVuDiaDiems']),
                    'generation_mode' => $generationMode,
                    'notice' => $notice,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Tạo lịch trình AI cho kế hoạch thành công',
                'data' => $result,
            ]);
        } catch (\Throwable $exception) {
            Log::error('Generate plan AI timeline failed: ' . $exception->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo lịch trình AI cho kế hoạch.',
            ], 500);
        }
    }

    public function store(StoreKeHoachRequest $request): JsonResponse
    {
        $user = $request->user();
        if (!$this->ownershipService->ensureCustomerOrAdmin($user)) {
            return $this->forbiddenResponse('Bạn phải xác thực khách hàng để thêm kế hoạch.');
        }

        $payload = $request->validated();
        $maNhom = (string) ($payload['ma_nhom'] ?? '');

        if ($user instanceof KhachHang && !$this->ownershipService->customerBelongsToGroup($user->Ma_khach_hang, $maNhom)) {
            return $this->forbiddenResponse('Bạn không thuộc nhóm hành trình đã chọn.');
        }

        unset($payload['ma_khach_hang']);

        try {
            $keHoach = KeHoach::query()->create($payload);

            return response()->json([
                'success' => true,
                'message' => 'Thêm kế hoạch thành công',
                'data' => $keHoach->load('nhom'),
            ], 201);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm kế hoạch',
            ], 500);
        }
    }

    public function update(UpdateKeHoachRequest $request, string $ma_ke_hoach): JsonResponse
    {
        $keHoach = KeHoach::query()->find($ma_ke_hoach);
        if (!$keHoach) {
            return response()->json([
                'success' => false,
                'message' => 'Kế hoạch không tồn tại',
            ], 404);
        }

        $user = $request->user();
        if (!$this->ownershipService->canAccessPlan($user, $keHoach)) {
            return $this->forbiddenResponse();
        }

        $payload = $request->validated();
        $targetGroup = (string) ($payload['ma_nhom'] ?? $keHoach->ma_nhom);

        if ($user instanceof KhachHang && !$this->ownershipService->customerBelongsToGroup($user->Ma_khach_hang, $targetGroup)) {
            return $this->forbiddenResponse('Bạn không thuộc nhóm hành trình đã chọn.');
        }

        unset($payload['ma_khach_hang']);

        try {
            $keHoach->update($payload);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật kế hoạch thành công',
                'data' => $keHoach->load('nhom'),
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật kế hoạch',
            ], 500);
        }
    }

    public function changeStatus(Request $request, string $ma_ke_hoach): JsonResponse
    {
        $keHoach = KeHoach::query()->find($ma_ke_hoach);
        if (!$keHoach) {
            return response()->json([
                'success' => false,
                'message' => 'Kế hoạch không tồn tại',
            ], 404);
        }

        if (!$this->ownershipService->canAccessPlan($request->user(), $keHoach)) {
            return $this->forbiddenResponse();
        }

        try {
            $keHoach->trang_thai = $keHoach->trang_thai == 1 ? 0 : 1;
            $keHoach->save();

            return response()->json([
                'success' => true,
                'message' => 'Đổi trạng thái kế hoạch thành công',
                'data' => $keHoach,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đổi trạng thái kế hoạch',
            ], 500);
        }
    }

    public function destroy(Request $request, string $ma_ke_hoach): JsonResponse
    {
        $keHoach = KeHoach::query()->find($ma_ke_hoach);
        if (!$keHoach) {
            return response()->json([
                'success' => false,
                'message' => 'Kế hoạch không tồn tại',
            ], 404);
        }

        if (!$this->ownershipService->canAccessPlan($request->user(), $keHoach)) {
            return $this->forbiddenResponse();
        }

        try {
            $keHoach->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa kế hoạch thành công',
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa kế hoạch',
            ], 500);
        }
    }

    public function getByGroup(Request $request, string $ma_nhom): JsonResponse
    {
        if (!$this->ownershipService->canAccessGroup($request->user(), $ma_nhom)) {
            return $this->forbiddenResponse();
        }

        $keHoach = KeHoach::query()
            ->where('ma_nhom', $ma_nhom)
            ->where('trang_thai', 1)
            ->with('nhom')
            ->get();

        if ($keHoach->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy kế hoạch cho nhóm này',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $keHoach,
            'total' => $keHoach->count(),
        ]);
    }

    public function suggestNearby(Request $request, string $ma_ke_hoach): JsonResponse
    {
        $radius = $request->input('radius', 10);

        $keHoach = KeHoach::query()->with(['hoatDongChiTiets.diaDiem'])->find($ma_ke_hoach);
        if (!$keHoach) {
            return response()->json([
                'success' => false,
                'message' => 'Kế hoạch không tồn tại',
            ], 404);
        }

        if (!$this->ownershipService->canAccessPlan($request->user(), $keHoach)) {
            return $this->forbiddenResponse();
        }

        $diaDiemsInPlan = collect();
        $provinces = collect();

        foreach ($keHoach->hoatDongChiTiets as $hoatDong) {
            if ($hoatDong->diaDiem) {
                $diaDiemsInPlan->push($hoatDong->diaDiem);

                $addressParts = explode(',', (string) $hoatDong->diaDiem->dia_chi);
                $province = trim((string) end($addressParts));
                if ($province !== '') {
                    $provinces->push($province);
                }
            }
        }

        if ($diaDiemsInPlan->isEmpty()) {
            $fallbackSuggestions = collect($this->fallbackLocationCandidates($keHoach))
                ->take(8)
                ->map(function (DiaDiem $location) {
                    $location->distance_km = null;
                    $location->reason = 'Phù hợp với tên hoặc mô tả kế hoạch';

                    return $location;
                })
                ->values();

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách địa điểm gợi ý theo kế hoạch thành công',
                'radius_applied' => $radius,
                'total' => $fallbackSuggestions->count(),
                'data' => $fallbackSuggestions,
            ]);
        }

        $diaDiemsInPlanIds = $diaDiemsInPlan->pluck('ma_dia_diem')->toArray();
        $provinces = $provinces->unique()->toArray();

        $allOtherLocations = \App\Models\DiaDiem::query()
            ->whereNotIn('ma_dia_diem', $diaDiemsInPlanIds)
            ->with(['dichVuDiaDiems', 'tags'])
            ->get();

        $suggestedLocations = collect();

        foreach ($allOtherLocations as $location) {
            $isNearby = false;
            $inSameProvince = false;
            $minDistance = null;

            foreach ($provinces as $prov) {
                if (stripos((string) $location->dia_chi, (string) $prov) !== false) {
                    $inSameProvince = true;
                    break;
                }
            }

            foreach ($diaDiemsInPlan as $planLocation) {
                $distance = $this->calculateDistance(
                    $planLocation->vi_do,
                    $planLocation->kinh_do,
                    $location->vi_do,
                    $location->kinh_do
                );

                if ($minDistance === null || $distance < $minDistance) {
                    $minDistance = $distance;
                }

                if ($distance <= $radius) {
                    $isNearby = true;
                }
            }

            if ($isNearby || $inSameProvince) {
                $location->distance_km = round((float) $minDistance, 2);
                $location->reason = $isNearby ? "Trong bán kính {$radius}km" : 'Cùng khu vực/tỉnh thành';
                $suggestedLocations->push($location);
            }
        }

        $suggestedLocations = $suggestedLocations->sortBy('distance_km')->values();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách địa điểm gợi ý lân cận thành công',
            'radius_applied' => $radius,
            'total' => $suggestedLocations->count(),
            'data' => $suggestedLocations,
        ]);
    }

    private function generateAiDataForPlan(KeHoach $keHoach): array
    {
        $startDate = Carbon::parse($keHoach->ngay_bat_dau ?: now());
        $endDate = Carbon::parse($keHoach->ngay_ket_thuc ?: $startDate);
        $dayCount = max(1, min(7, $startDate->diffInDays($endDate) + 1));
        $destination = trim((string) ($keHoach->ten_ke_hoach ?: 'Việt Nam'));

        $locations = DiaDiem::query()
            ->where('dia_chi', 'LIKE', '%' . $destination . '%')
            ->orWhere('ten_dia_diem', 'LIKE', '%' . $destination . '%')
            ->limit(40)
            ->get();

        $tours = Tour::query()
            ->with('chiTietTours.diaDiem')
            ->where('ten_tour', 'LIKE', '%' . $destination . '%')
            ->orWhere('mo_ta', 'LIKE', '%' . $destination . '%')
            ->get();

        $aiData = $this->aiTourGuideService->generatePlan(
            $destination,
            $dayCount,
            (string) ($keHoach->ngan_sach_du_kien ?? 0),
            [],
            $locations,
            $tours,
            [],
            (string) ($keHoach->mo_ta ?? ''),
        );

        if (!is_array($aiData)) {
            throw new \RuntimeException('AI did not return a valid itinerary payload.');
        }

        return $this->hydrateAiCoordinates($aiData);
    }

    private function hydrateAiCoordinates(array $aiData): array
    {
        foreach ($this->extractAiDays($aiData) as $dayIndex => $day) {
            foreach ($this->extractAiActivities($day) as $activityIndex => $activity) {
                $location = $this->resolveActivityLocation($activity, false);
                if (!$location) {
                    continue;
                }

                $aiData['lichTrinh'][$dayIndex]['danhSachHoatDong'][$activityIndex]['ma_dia_diem'] = $location->ma_dia_diem;
                $aiData['lichTrinh'][$dayIndex]['danhSachHoatDong'][$activityIndex]['kinh_do'] = $location->kinh_do;
                $aiData['lichTrinh'][$dayIndex]['danhSachHoatDong'][$activityIndex]['vi_do'] = $location->vi_do;
            }
        }

        return $aiData;
    }

    private function buildFallbackAiDataForPlan(KeHoach $keHoach, string $notice): array
    {
        $startDate = Carbon::parse($keHoach->ngay_bat_dau ?: now())->startOfDay();
        $endDate = Carbon::parse($keHoach->ngay_ket_thuc ?: $startDate)->startOfDay();
        if ($endDate->lt($startDate)) {
            $endDate = $startDate->copy();
        }

        $candidates = $this->fallbackLocationCandidates($keHoach);
        $candidateCount = count($candidates);
        $days = [];
        $cursor = 0;

        for ($date = $startDate->copy(), $dayNumber = 1; $date->lte($endDate); $date->addDay(), $dayNumber++) {
            $activities = [];

            if ($candidateCount > 0) {
                /** @var \App\Models\DiaDiem $location */
                $location = $candidates[$cursor % $candidateCount];
                $slot = $this->fallbackTimelineSlots()[0];
                $activities[] = [
                    'buoi' => $slot['session'],
                    'tieuDe' => $location->ten_dia_diem,
                    'ma_dia_diem' => $location->ma_dia_diem,
                    'moTa' => 'Gợi ý hệ thống để bạn tiếp tục hoàn thiện lịch trình.',
                    'gia' => (string) ((float) ($location->gia_giao_dong ?? 0)),
                    'thoiGian' => $slot['start'],
                    'thoiLuong' => '2 giờ',
                    'kinh_do' => $location->kinh_do,
                    'vi_do' => $location->vi_do,
                    'dia_chi' => $location->dia_chi,
                    'hinh_anh' => $location->hinh_anh,
                ];
                $cursor++;
            }

            $days[] = [
                'tieuDe' => 'Ngày ' . $dayNumber . ': Gợi ý hệ thống',
                'thoiGian' => 'Ngày ' . $dayNumber,
                'ngay_cu_the' => $date->toDateString(),
                'danhSachHoatDong' => $activities,
            ];
        }

        return [
            'tieuDe' => 'Bản nháp gợi ý hệ thống: ' . ($keHoach->ten_ke_hoach ?: 'Hành trình'),
            'tongChiPhi' => (string) ((float) ($keHoach->ngan_sach_du_kien ?? 0)),
            'generation_mode' => 'fallback',
            'notice' => $notice,
            '_metadata' => [
                'generation_mode' => 'fallback',
                'notice' => $notice,
            ],
            'lichTrinh' => $days,
        ];
    }

    private function fallbackLocationCandidates(KeHoach $keHoach): array
    {
        $keywords = $this->planSearchKeywords($keHoach);
        $pool = DiaDiem::query()
            ->approved()
            ->orderByDesc('updated_at')
            ->limit(60)
            ->get();

        if ($pool->isEmpty()) {
            $pool = DiaDiem::query()
                ->orderByDesc('updated_at')
                ->limit(60)
                ->get();
        }

        $scored = $pool->map(function (DiaDiem $location) use ($keywords) {
            $haystack = $this->normalizeSearchText(implode(' ', [
                $location->ten_dia_diem,
                $location->dia_chi,
                $location->mo_ta,
            ]));

            $score = 0;
            foreach ($keywords as $keyword) {
                if ($keyword !== '' && str_contains($haystack, $keyword)) {
                    $score += max(1, mb_strlen($keyword));
                }
            }

            return ['location' => $location, 'score' => $score];
        });

        $matched = $scored
            ->where('score', '>', 0)
            ->sortByDesc('score')
            ->pluck('location')
            ->values();

        if ($matched->isNotEmpty()) {
            return $matched->all();
        }

        return $pool->take(12)->values()->all();
    }

    private function planSearchKeywords(KeHoach $keHoach): array
    {
        $source = $this->normalizeSearchText(implode(' ', [
            (string) $keHoach->ten_ke_hoach,
            (string) $keHoach->mo_ta,
        ]));

        $parts = preg_split('/\s+/u', $source, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $stopwords = array_fill_keys([
            'cac',
            'canh',
            'chuyen',
            'dia',
            'diem',
            'hanh',
            'lich',
            'quan',
            'tham',
            'trinh',
            'tour',
        ], true);

        $parts = array_values(array_unique(array_filter($parts, static function (string $keyword) use ($stopwords): bool {
            return mb_strlen($keyword) >= 3 && !isset($stopwords[$keyword]);
        })));

        return array_slice(array_values(array_unique(array_merge(
            $this->regionalFallbackKeywords($source),
            $parts,
        ))), 0, 24);
    }

    private function regionalFallbackKeywords(string $normalizedSource): array
    {
        if (
            str_contains($normalizedSource, 'mien bac')
            || str_contains($normalizedSource, 'bac bo')
            || preg_match('/\bbac\b/u', $normalizedSource)
        ) {
            return [
                'ha noi',
                'quang ninh',
                'ha long',
                'ninh binh',
                'lao cai',
                'sa pa',
                'sapa',
                'yen bai',
            ];
        }

        if (
            str_contains($normalizedSource, 'mien trung')
            || str_contains($normalizedSource, 'trung bo')
        ) {
            return [
                'da nang',
                'quang nam',
                'hoi an',
                'hue',
                'khanh hoa',
                'nha trang',
            ];
        }

        if (
            str_contains($normalizedSource, 'mien nam')
            || str_contains($normalizedSource, 'nam bo')
        ) {
            return [
                'ho chi minh',
                'sai gon',
                'can tho',
                'vung tau',
                'dong nai',
                'an giang',
            ];
        }

        return [];
    }

    private function normalizeSearchText(string $value): string
    {
        $normalized = mb_strtolower(trim($value));
        $normalized = strtr($normalized, [
            'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
            'đ' => 'd',
        ]);
        $normalized = strtr($normalized, [
            'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
            'đ' => 'd',
        ]);

        return preg_replace('/[^a-z0-9\\s]/u', ' ', $normalized) ?: '';
    }

    private function fallbackTimelineSlots(): array
    {
        return [
            ['session' => 'BUOI SANG', 'start' => '08:00'],
            ['session' => 'BUOI TRUA', 'start' => '12:00'],
            ['session' => 'BUOI TOI', 'start' => '19:00'],
        ];
    }

    private function extractGenerationMode(array $aiData): string
    {
        $mode = (string) ($aiData['generation_mode'] ?? data_get($aiData, '_metadata.generation_mode') ?? 'ai');

        return in_array($mode, ['ai', 'fallback'], true) ? $mode : 'ai';
    }

    private function materializeAiTimeline(KeHoach $keHoach, array $aiData): int
    {
        $days = $this->extractAiDays($aiData);
        if (empty($days)) {
            throw new \RuntimeException('AI itinerary has no days to materialize.');
        }

        $startDate = Carbon::parse($keHoach->ngay_bat_dau ?: now());
        $created = 0;

        foreach ($days as $dayIndex => $day) {
            $date = data_get($day, 'ngay_cu_the')
                ? Carbon::parse(data_get($day, 'ngay_cu_the'))->toDateString()
                : $startDate->copy()->addDays($dayIndex)->toDateString();

            foreach ($this->extractAiActivities($day) as $activity) {
                $location = $this->resolveActivityLocation($activity, false);
                if (!$location) {
                    continue;
                }

                $startTime = $this->resolveActivityStartTime($activity);
                HoatDongChiTiet::query()->create([
                    'ma_ke_hoach' => $keHoach->ma_ke_hoach,
                    'ma_nhom' => $keHoach->ma_nhom,
                    'ma_dia_diem' => $location->ma_dia_diem,
                    'ma_tour' => data_get($activity, 'ma_tour'),
                    'ma_thoi_gian_tour' => data_get($activity, 'ma_thoi_gian_tour'),
                    'ghi_chu' => data_get($activity, 'moTa') ?: data_get($activity, 'mo_ta'),
                    'gio_bat_dau' => $startTime,
                    'gio_ket_thuc' => Carbon::parse($startTime)->addHours(2)->toTimeString(),
                    'ngay_cu_the' => $date,
                ]);

                $created++;
            }
        }

        return $created;
    }

    private function extractAiDays(array $aiData): array
    {
        $days = $aiData['lichTrinh'] ?? $aiData['lich_trinh'] ?? $aiData['itinerary'] ?? [];
        if (!is_array($days)) {
            return [];
        }

        return array_is_list($days) ? $days : array_values($days);
    }

    private function extractAiActivities(array $day): array
    {
        $activities = $day['danhSachHoatDong']
            ?? $day['danh_sach_hoat_dong']
            ?? $day['hoat_dong']
            ?? $day['activities']
            ?? [];

        if (!is_array($activities)) {
            return [];
        }

        return array_is_list($activities) ? $activities : array_values($activities);
    }

    private function resolveActivityLocation(array $activity, bool $createFallback): ?DiaDiem
    {
        $locationId = data_get($activity, 'ma_dia_diem');
        if ($locationId && $locationId !== 'null') {
            $location = DiaDiem::query()->find($locationId);
            if ($location) {
                return $location;
            }
        }

        $name = $this->activityTitle($activity);
        if ($name !== '') {
            $location = DiaDiem::query()
                ->where('ten_dia_diem', 'LIKE', '%' . $name . '%')
                ->first();

            if ($location) {
                return $location;
            }

            if ($createFallback) {
                try {
                    $location = $this->mapLocationService->findOrSyncLocation($name);
                    if ($location) {
                        return $location;
                    }
                } catch (\Throwable $exception) {
                    Log::warning('Could not sync AI activity location: ' . $exception->getMessage());
                }
            }
        }

        return null;

        if (!$createFallback) {
            return null;
        }

        $lat = is_numeric(data_get($activity, 'vi_do')) ? (float) data_get($activity, 'vi_do') : 16.0544;
        $lng = is_numeric(data_get($activity, 'kinh_do')) ? (float) data_get($activity, 'kinh_do') : 108.2022;
        $fallbackName = $name !== '' ? $name : 'Điểm dừng chân';

        return DiaDiem::query()->firstOrCreate(
            ['ten_dia_diem' => $fallbackName],
            [
                'loai' => 1,
                'dia_chi' => data_get($activity, 'dia_chi') ?: $fallbackName,
                'kinh_do' => $lng,
                'vi_do' => $lat,
                'gia_giao_dong' => 0,
                'hinh_anh' => data_get($activity, 'hinh_anh') ?: data_get($activity, 'image'),
                'mo_ta' => data_get($activity, 'moTa') ?: data_get($activity, 'mo_ta'),
                'gio_mo_cua' => '08:00:00',
                'gio_dong_cua' => '22:00:00',
            ],
        );
    }

    private function activityTitle(array $activity): string
    {
        return trim((string) (
            data_get($activity, 'tieuDe')
            ?: data_get($activity, 'tieu_de')
            ?: data_get($activity, 'ten_dia_diem')
            ?: data_get($activity, 'name')
            ?: ''
        ));
    }

    private function resolveActivityStartTime(array $activity): string
    {
        $rawTime = (string) (
            data_get($activity, 'thoiGian')
            ?: data_get($activity, 'thoi_gian')
            ?: data_get($activity, 'time')
            ?: ''
        );

        if (preg_match('/(\d{1,2}):(\d{2})/', $rawTime, $matches)) {
            return sprintf('%02d:%02d:00', (int) $matches[1], (int) $matches[2]);
        }

        $session = mb_strtolower((string) (data_get($activity, 'buoi') ?: data_get($activity, 'session') ?: ''));
        if (str_contains($session, 'trưa') || str_contains($session, 'trua') || str_contains($session, 'chiều') || str_contains($session, 'chieu')) {
            return '12:00:00';
        }

        if (str_contains($session, 'tối') || str_contains($session, 'toi')) {
            return '19:00:00';
        }

        return '08:00:00';
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function forbiddenResponse(string $message = 'Bạn không có quyền truy cập kế hoạch này.'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 403);
    }
}
