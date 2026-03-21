<?php

namespace App\Http\Controllers;

use App\Models\DichVuDiaDiem;
use App\Http\Requests\StoreDichVuDiaDiemRequest;
use App\Http\Requests\UpdateDichVuDiaDiemRequest;
use Illuminate\Http\Request;

class DichVuDiaDiemController extends Controller
{
    public function index()
    {
        $dich_vu = DichVuDiaDiem::where('trang_thai', 1)->get();

        return response()->json([
            'success' => true,
            'data' => $dich_vu,
            'total' => $dich_vu->count()
        ]);
    }

    public function indexAll()
    {
        $dich_vu = DichVuDiaDiem::orderBy('ma_dich_vu', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $dich_vu,
            'total' => $dich_vu->count()
        ]);
    }

    public function search(Request $request)
    {
        $keyword = $request->query('keyword');

        if (!$keyword) {
            return response()->json([
                'success' => false,
                'message' => 'Từ khóa không được trống'
            ], 400);
        }

        $type = $request->query('type', 'all');
        $query = DichVuDiaDiem::query();

        if ($type === 'ten_dich_vu') {
            $query->where('ten_dich_vu', 'like', "%{$keyword}%");
        }
        elseif ($type === 'ma_dia_diem') {
            $query->where('ma_dia_diem', 'like', "%{$keyword}%");
        }
        else {
            $query->where(function ($q) use ($keyword) {
                $q->where('ten_dich_vu', 'like', "%{$keyword}%")
                    ->orWhere('ma_dia_diem', 'like', "%{$keyword}%");
            });
        }

        $dich_vu = $query->orderBy('ten_dich_vu')->get();

        return response()->json([
            'success' => true,
            'data' => $dich_vu,
            'total' => $dich_vu->count()
        ]);
    }

    public function show($ma_dich_vu)
    {
        $dich_vu = DichVuDiaDiem::find($ma_dich_vu);

        if (!$dich_vu) {
            return response()->json([
                'success' => false,
                'message' => 'Dịch vụ không tồn tại'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $dich_vu
        ]);
    }

    public function store(StoreDichVuDiaDiemRequest $request)
    {
        try {
            $dich_vu = DichVuDiaDiem::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Thêm thành công',
                'data' => $dich_vu
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateDichVuDiaDiemRequest $request, $ma_dich_vu)
    {
        $dich_vu = DichVuDiaDiem::find($ma_dich_vu);

        if (!$dich_vu) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại'
            ], 404);
        }

        try {
            $dich_vu->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công',
                'data' => $dich_vu
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changeStatus($ma_dich_vu)
    {
        $dich_vu = DichVuDiaDiem::find($ma_dich_vu);

        if (!$dich_vu) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại'
            ], 404);
        }

        try {
            $dich_vu->update([
                'trang_thai' => $dich_vu->trang_thai ? 0 : 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đổi trạng thái thành công',
                'data' => $dich_vu
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($ma_dich_vu)
    {
        $dich_vu = DichVuDiaDiem::find($ma_dich_vu);

        if (!$dich_vu) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại'
            ], 404);
        }

        try {
            $dich_vu->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xoá thành công'
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getByLocation($ma_dia_diem)
    {
        $dich_vu = DichVuDiaDiem::where('ma_dia_diem', $ma_dia_diem)
            ->where('trang_thai', 1)
            ->get();

        if ($dich_vu->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $dich_vu,
            'total' => $dich_vu->count()
        ]);
    }
}