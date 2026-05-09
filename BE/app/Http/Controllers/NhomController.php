<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNhomRequest;
use App\Http\Requests\UpdateNhomRequest;
use App\Models\KhachHang;
use App\Models\Nhom;
use App\Models\ThanhVienNhom;
use App\Services\CustomerOwnershipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NhomController extends Controller
{
    public function __construct(
        private readonly CustomerOwnershipService $ownershipService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$this->ownershipService->ensureCustomerOrAdmin($user)) {
            return $this->forbiddenResponse();
        }

        $nhoms = $this->ownershipService->visibleGroupsQuery($user)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($nhoms->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không có nhóm nào'], 404);
        }

        return response()->json(['success' => true, 'data' => $nhoms], 200);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $nhom = Nhom::query()->find($id);
        if (!$nhom) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy nhóm'], 404);
        }

        if (!$this->ownershipService->canAccessGroup($request->user(), $nhom->Ma_nhom)) {
            return $this->forbiddenResponse();
        }

        return response()->json(['success' => true, 'data' => $nhom], 200);
    }

    public function store(StoreNhomRequest $request): JsonResponse
    {
        $user = $request->user();
        if (!$this->ownershipService->ensureCustomerOrAdmin($user)) {
            return $this->forbiddenResponse();
        }

        $nhom = Nhom::query()->create($request->validated());

        if ($user instanceof KhachHang) {
            ThanhVienNhom::query()->firstOrCreate(
                [
                    'Ma_nhom' => $nhom->Ma_nhom,
                    'Ma_khach_hang' => $user->Ma_khach_hang,
                ],
                [
                    'vai_tro' => 1,
                ]
            );
        }

        return response()->json(['success' => true, 'message' => 'Thêm nhóm thành công', 'data' => $nhom], 201);
    }

    public function update(UpdateNhomRequest $request, string $id): JsonResponse
    {
        $nhom = Nhom::query()->find($id);
        if (!$nhom) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy nhóm'], 404);
        }

        if (!$this->ownershipService->canManageGroup($request->user(), $nhom->Ma_nhom)) {
            return $this->forbiddenResponse('Chỉ nhóm trưởng hoặc admin mới có thể cập nhật nhóm.');
        }

        $nhom->update($request->validated());

        return response()->json(['success' => true, 'message' => 'Cập nhật nhóm thành công', 'data' => $nhom], 200);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $nhom = Nhom::query()->find($id);
        if (!$nhom) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy nhóm'], 404);
        }

        if (!$this->ownershipService->canManageGroup($request->user(), $nhom->Ma_nhom)) {
            return $this->forbiddenResponse('Chỉ nhóm trưởng hoặc admin mới có thể xóa nhóm.');
        }

        $nhom->delete();

        return response()->json(['success' => true, 'message' => 'Xóa nhóm thành công'], 200);
    }

    public function search(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$this->ownershipService->ensureCustomerOrAdmin($user)) {
            return $this->forbiddenResponse();
        }

        $query = $this->ownershipService->visibleGroupsQuery($user);

        if ($request->filled('Ma_nhom')) {
            $query->where('Ma_nhom', 'like', '%' . $request->string('Ma_nhom')->trim() . '%');
        }
        if ($request->filled('ten_nhom')) {
            $query->where('ten_nhom', 'like', '%' . $request->string('ten_nhom')->trim() . '%');
        }

        $nhoms = $query->get();
        if ($nhoms->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy kết quả'], 404);
        }

        return response()->json(['success' => true, 'data' => $nhoms], 200);
    }

    private function forbiddenResponse(string $message = 'Bạn không có quyền truy cập nhóm này.'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 403);
    }
}
