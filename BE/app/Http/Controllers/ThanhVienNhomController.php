<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThanhVienNhomRequest;
use App\Http\Requests\UpdateThanhVienNhomRequest;
use App\Models\KhachHang;
use App\Models\ThanhVienNhom;
use App\Services\CustomerOwnershipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ThanhVienNhomController extends Controller
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

        $query = ThanhVienNhom::query()->with(['nhom', 'khachHang']);

        if ($user instanceof KhachHang) {
            $query->whereIn('Ma_nhom', $this->ownershipService->groupIdsForCustomer($user->Ma_khach_hang));
        }

        $tvn = $query->get();
        if ($tvn->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không có thành viên nhóm nào'], 404);
        }

        return response()->json(['success' => true, 'data' => $tvn], 200);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $tvn = ThanhVienNhom::query()->with(['nhom', 'khachHang'])->find($id);
        if (!$tvn) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thành viên nhóm'], 404);
        }

        if (!$this->ownershipService->canAccessGroup($request->user(), $tvn->Ma_nhom)) {
            return $this->forbiddenResponse();
        }

        return response()->json(['success' => true, 'data' => $tvn], 200);
    }

    public function getByNhom(Request $request, string $maNhom): JsonResponse
    {
        if (!$this->ownershipService->canAccessGroup($request->user(), $maNhom)) {
            return $this->forbiddenResponse();
        }

        $tvn = ThanhVienNhom::query()
            ->with(['khachHang'])
            ->where('Ma_nhom', $maNhom)
            ->get();

        if ($tvn->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Nhóm này chưa có thành viên hoặc không tồn tại'], 404);
        }

        return response()->json(['success' => true, 'data' => $tvn], 200);
    }
    
    public function store(StoreThanhVienNhomRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $maNhom = (string) $validated['Ma_nhom'];

        if (!$this->ownershipService->canManageGroup($request->user(), $maNhom)) {
            return $this->forbiddenResponse('Chỉ nhóm trưởng hoặc admin mới có thể thêm thành viên.');
        }

        $exists = ThanhVienNhom::query()
            ->where('Ma_nhom', $maNhom)
            ->where('Ma_khach_hang', $validated['Ma_khach_hang'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Khách hàng đã tồn tại trong nhóm này',
            ], 422);
        }

        if (!array_key_exists('vai_tro', $validated) || is_null($validated['vai_tro'])) {
            $validated['vai_tro'] = 0;
        }

        $tvn = ThanhVienNhom::query()->create($validated);

        return response()->json(['success' => true, 'message' => 'Thêm thành viên thành công', 'data' => $tvn], 201);
    }
    
    public function update(UpdateThanhVienNhomRequest $request, string $id): JsonResponse
    {
        $tvn = ThanhVienNhom::query()->find($id);
        if (!$tvn) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thành viên nhóm'], 404);
        }

        if (!$this->ownershipService->canManageGroup($request->user(), $tvn->Ma_nhom)) {
            return $this->forbiddenResponse('Chỉ nhóm trưởng hoặc admin mới có thể cập nhật thành viên.');
        }

        $tvn->update($request->validated());

        return response()->json(['success' => true, 'message' => 'Cập nhật thành viên thành công', 'data' => $tvn], 200);
    }
    
    public function destroy(Request $request, string $id): JsonResponse
    {
        $tvn = ThanhVienNhom::query()->find($id);
        if (!$tvn) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thành viên nhóm'], 404);
        }

        if (!$this->ownershipService->canManageGroup($request->user(), $tvn->Ma_nhom)) {
            return $this->forbiddenResponse('Chỉ nhóm trưởng hoặc admin mới có thể xóa thành viên.');
        }

        $tvn->delete();

        return response()->json(['success' => true, 'message' => 'Xóa thành viên thành công'], 200);
    }
    
    public function search(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$this->ownershipService->ensureCustomerOrAdmin($user)) {
            return $this->forbiddenResponse();
        }

        $query = ThanhVienNhom::query()->with(['nhom', 'khachHang']);

        if ($user instanceof KhachHang) {
            $query->whereIn('Ma_nhom', $this->ownershipService->groupIdsForCustomer($user->Ma_khach_hang));

            if ($request->filled('Ma_khach_hang')) {
                $query->where('Ma_khach_hang', $request->string('Ma_khach_hang')->trim());
            }
        } elseif ($request->filled('Ma_khach_hang')) {
            $query->where('Ma_khach_hang', $request->string('Ma_khach_hang')->trim());
        }

        if ($request->filled('Ma_thanh_vien')) {
            $query->where('Ma_thanh_vien', $request->string('Ma_thanh_vien')->trim());
        }
        if ($request->filled('Ma_nhom')) {
            $query->where('Ma_nhom', $request->string('Ma_nhom')->trim());
        }

        $tvn = $query->get();
        if ($tvn->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy kết quả'], 404);
        }

        return response()->json(['success' => true, 'data' => $tvn], 200);
    }

    private function forbiddenResponse(string $message = 'Bạn không có quyền truy cập nhóm này.'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 403);
    }
}
