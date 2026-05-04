<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThanhVienNhomRequest;
use App\Http\Requests\UpdateThanhVienNhomRequest;
use App\Models\ThanhVienNhom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ThanhVienNhomController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ThanhVienNhom::with(['nhom', 'khachHang'])
            ->when($request->filled('Ma_nhom'), fn ($query) => $query->where('Ma_nhom', $request->query('Ma_nhom')))
            ->when($request->filled('Ma_khach_hang'), fn ($query) => $query->where('Ma_khach_hang', $request->query('Ma_khach_hang')))
            ->when($request->filled('vai_tro'), fn ($query) => $query->where('vai_tro', $request->query('vai_tro')))
            ->orderBy('created_at', 'desc');

        $perPage = (int) $request->query('per_page', 0);
        $members = $perPage > 0
            ? $query->paginate(min($perPage, 50))
            : $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Lay danh sach thanh vien nhom thanh cong',
            'data' => $members,
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $member = ThanhVienNhom::with(['nhom', 'khachHang'])->find($id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Khong tim thay thanh vien nhom',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lay thong tin thanh vien nhom thanh cong',
            'data' => $member,
        ]);
    }

    public function getByNhom(string $maNhom): JsonResponse
    {
        $members = ThanhVienNhom::with(['nhom', 'khachHang'])
            ->where('Ma_nhom', $maNhom)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lay danh sach thanh vien theo nhom thanh cong',
            'data' => $members,
        ]);
    }

    public function store(StoreThanhVienNhomRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $exists = ThanhVienNhom::query()
            ->where('Ma_nhom', $validated['Ma_nhom'])
            ->where('Ma_khach_hang', $validated['Ma_khach_hang'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Khach hang da ton tai trong nhom nay',
            ], 422);
        }

        $validated['vai_tro'] = $validated['vai_tro'] ?? 0;

        $member = ThanhVienNhom::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Them thanh vien nhom thanh cong',
            'data' => $member->load(['nhom', 'khachHang']),
        ], 201);
    }

    public function search(Request $request): JsonResponse
    {
        $members = ThanhVienNhom::with(['nhom', 'khachHang'])
            ->when($request->filled('Ma_nhom'), fn ($query) => $query->where('Ma_nhom', $request->query('Ma_nhom')))
            ->when($request->filled('Ma_khach_hang'), fn ($query) => $query->where('Ma_khach_hang', $request->query('Ma_khach_hang')))
            ->when($request->filled('vai_tro'), fn ($query) => $query->where('vai_tro', $request->query('vai_tro')))
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Tim kiem thanh vien nhom thanh cong',
            'data' => $members,
        ]);
    }

    public function update(UpdateThanhVienNhomRequest $request, string $id): JsonResponse
    {
        $member = ThanhVienNhom::find($id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Khong tim thay thanh vien nhom',
            ], 404);
        }

        $validated = $request->validated();

        if (
            array_key_exists('vai_tro', $validated)
            && (int) $member->vai_tro === 1
            && (int) $validated['vai_tro'] === 0
            && $this->isLastLeader($member)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Nhom phai co it nhat mot nhom truong',
            ], 422);
        }

        $member->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cap nhat thanh vien nhom thanh cong',
            'data' => $member->load(['nhom', 'khachHang']),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $member = ThanhVienNhom::find($id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Khong tim thay thanh vien nhom',
            ], 404);
        }

        if ((int) $member->vai_tro === 1 && $this->isLastLeader($member)) {
            return response()->json([
                'success' => false,
                'message' => 'Khong the xoa nhom truong cuoi cung cua nhom',
            ], 422);
        }

        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoa thanh vien nhom thanh cong',
        ]);
    }

    private function isLastLeader(ThanhVienNhom $member): bool
    {
        return ThanhVienNhom::query()
            ->where('Ma_nhom', $member->Ma_nhom)
            ->where('vai_tro', 1)
            ->where('Ma_thanh_vien', '!=', $member->Ma_thanh_vien)
            ->doesntExist();
    }
}
