<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHoatDongChiTietRequest;
use App\Http\Requests\UpdateHoatDongChiTietRequest;
use App\Models\HoatDongChiTiet;
use App\Models\KeHoach;
use App\Services\CustomerOwnershipService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HoatDongChiTietController extends Controller
{
    public function __construct(
        private readonly CustomerOwnershipService $ownershipService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$this->ownershipService->ensureCustomerOrAdmin($user)) {
            return $this->unauthenticatedResponse();
        }

        $query = HoatDongChiTiet::query()->with(['keHoach', 'nhom', 'diaDiem']);

        if (!$this->ownershipService->isAdmin($user)) {
            /** @var \App\Models\KhachHang $user */
            $query->whereIn('ma_nhom', $this->ownershipService->groupIdsForCustomer($user->Ma_khach_hang));
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách hoạt động chi tiết thành công',
            'data' => $query->orderBy('ngay_cu_the')->orderBy('gio_bat_dau')->get(),
        ]);
    }

    public function indexByPlan(Request $request, string $maKeHoach): JsonResponse
    {
        $plan = $this->findAccessiblePlan($request, $maKeHoach);
        if ($plan instanceof JsonResponse) {
            return $plan;
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy lịch trình theo kế hoạch thành công',
            'data' => $plan->hoatDongChiTiets()
                ->with('diaDiem')
                ->orderBy('ngay_cu_the')
                ->orderBy('gio_bat_dau')
                ->get(),
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $activity = HoatDongChiTiet::query()->with(['keHoach', 'nhom', 'diaDiem'])->find($id);
        if (!$activity) {
            return $this->notFoundResponse();
        }

        if (!$this->ownershipService->canAccessPlan($request->user(), $activity->keHoach)) {
            return $this->forbiddenResponse();
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin hoạt động chi tiết thành công',
            'data' => $activity,
        ]);
    }

    public function store(StoreHoatDongChiTietRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $plan = $this->findAccessiblePlan($request, (string) $validated['ma_ke_hoach']);
        if ($plan instanceof JsonResponse) {
            return $plan;
        }

        return $this->createActivityForPlan($request, $plan);
    }

    public function storeForPlan(Request $request, string $maKeHoach): JsonResponse
    {
        $plan = $this->findAccessiblePlan($request, $maKeHoach);
        if ($plan instanceof JsonResponse) {
            return $plan;
        }

        return $this->createActivityForPlan($request, $plan);
    }

    public function update(UpdateHoatDongChiTietRequest $request, string $id): JsonResponse
    {
        $activity = HoatDongChiTiet::query()->with('keHoach')->find($id);
        if (!$activity) {
            return $this->notFoundResponse();
        }

        if (!$this->ownershipService->canAccessPlan($request->user(), $activity->keHoach)) {
            return $this->forbiddenResponse();
        }

        return $this->updateActivity($request, $activity->keHoach, $activity);
    }

    public function updateForPlan(Request $request, string $maKeHoach, string $id): JsonResponse
    {
        $plan = $this->findAccessiblePlan($request, $maKeHoach);
        if ($plan instanceof JsonResponse) {
            return $plan;
        }

        $activity = $plan->hoatDongChiTiets()->find($id);
        if (!$activity) {
            return $this->notFoundResponse();
        }

        return $this->updateActivity($request, $plan, $activity);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $activity = HoatDongChiTiet::query()->with('keHoach')->find($id);
        if (!$activity) {
            return $this->notFoundResponse();
        }

        if (!$this->ownershipService->canAccessPlan($request->user(), $activity->keHoach)) {
            return $this->forbiddenResponse();
        }

        $activity->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa hoạt động chi tiết thành công',
        ]);
    }

    public function destroyForPlan(Request $request, string $maKeHoach, string $id): JsonResponse
    {
        $plan = $this->findAccessiblePlan($request, $maKeHoach);
        if ($plan instanceof JsonResponse) {
            return $plan;
        }

        $activity = $plan->hoatDongChiTiets()->find($id);
        if (!$activity) {
            return $this->notFoundResponse();
        }

        $activity->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa hoạt động chi tiết thành công',
        ]);
    }

    private function createActivityForPlan(Request $request, KeHoach $plan): JsonResponse
    {
        $validation = $this->validateActivityPayload($request, $plan);
        if ($validation instanceof JsonResponse) {
            return $validation;
        }

        $activity = HoatDongChiTiet::query()->create(array_merge($validation, [
            'ma_ke_hoach' => $plan->ma_ke_hoach,
            'ma_nhom' => $plan->ma_nhom,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Thêm hoạt động vào kế hoạch thành công',
            'data' => $activity->load('diaDiem'),
        ], 201);
    }

    private function updateActivity(Request $request, KeHoach $plan, HoatDongChiTiet $activity): JsonResponse
    {
        $validation = $this->validateActivityPayload($request, $plan, $activity);
        if ($validation instanceof JsonResponse) {
            return $validation;
        }

        $activity->update($validation);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật hoạt động chi tiết thành công',
            'data' => $activity->fresh('diaDiem'),
        ]);
    }

    private function validateActivityPayload(Request $request, KeHoach $plan, ?HoatDongChiTiet $activity = null): array|JsonResponse
    {
        $isUpdate = $activity !== null;
        $prefix = $isUpdate ? 'sometimes|required' : 'required';
        $validator = Validator::make($request->all(), [
            'ma_dia_diem' => "{$prefix}|exists:dia_diem,ma_dia_diem",
            'gio_bat_dau' => "{$prefix}|date_format:H:i",
            'gio_ket_thuc' => "{$prefix}|date_format:H:i",
            'ngay_cu_the' => "{$prefix}|date_format:Y-m-d",
            'ghi_chu' => 'nullable|string|max:2000',
            'ma_tour' => 'nullable|string|max:10',
            'ma_thoi_gian_tour' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator->errors()->toArray());
        }

        $payload = $validator->validated();
        $effectiveDate = (string) ($payload['ngay_cu_the'] ?? $activity?->ngay_cu_the ?? '');
        $effectiveStart = substr((string) ($payload['gio_bat_dau'] ?? $activity?->gio_bat_dau ?? ''), 0, 5);
        $effectiveEnd = substr((string) ($payload['gio_ket_thuc'] ?? $activity?->gio_ket_thuc ?? ''), 0, 5);

        if ($effectiveStart !== '' && $effectiveEnd !== '' && $effectiveEnd <= $effectiveStart) {
            return $this->validationResponse([
                'gio_ket_thuc' => ['Giờ kết thúc phải sau giờ bắt đầu.'],
            ]);
        }

        if ($effectiveDate !== '' && !$this->dateIsInsidePlan($effectiveDate, $plan)) {
            return $this->validationResponse([
                'ngay_cu_the' => ['Ngày hoạt động phải nằm trong khoảng ngày của kế hoạch.'],
            ]);
        }

        if (array_key_exists('gio_bat_dau', $payload)) {
            $payload['gio_bat_dau'] = $effectiveStart;
        }
        if (array_key_exists('gio_ket_thuc', $payload)) {
            $payload['gio_ket_thuc'] = $effectiveEnd;
        }

        unset($payload['ma_ke_hoach'], $payload['ma_nhom']);

        return $payload;
    }

    private function dateIsInsidePlan(string $date, KeHoach $plan): bool
    {
        $activityDate = Carbon::parse($date)->startOfDay();
        $startDate = Carbon::parse($plan->ngay_bat_dau ?: $date)->startOfDay();
        $endDate = Carbon::parse($plan->ngay_ket_thuc ?: $plan->ngay_bat_dau ?: $date)->startOfDay();

        return $activityDate->betweenIncluded($startDate, $endDate);
    }

    private function findAccessiblePlan(Request $request, string $maKeHoach): KeHoach|JsonResponse
    {
        $plan = KeHoach::query()->with('hoatDongChiTiets')->find($maKeHoach);
        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Kế hoạch không tồn tại',
            ], 404);
        }

        if (!$this->ownershipService->canAccessPlan($request->user(), $plan)) {
            return $this->forbiddenResponse();
        }

        return $plan;
    }

    private function validationResponse(array $errors): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Dữ liệu hoạt động chưa hợp lệ.',
            'errors' => $errors,
        ], 422);
    }

    private function unauthenticatedResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Vui lòng đăng nhập',
        ], 401);
    }

    private function forbiddenResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Bạn không có quyền thao tác lịch trình của kế hoạch này.',
        ], 403);
    }

    private function notFoundResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy hoạt động chi tiết',
        ], 404);
    }
}
