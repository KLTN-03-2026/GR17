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
        $dich_vu = DichVuDiaDiem::with('diaDiem')->get();

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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
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
        } catch (\Exception $e) {
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
                'message' => 'Dịch vụ không tồn tại'
            ], 404);
        }

        try {
            $dich_vu->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}