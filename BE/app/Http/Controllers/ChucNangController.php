<?php

namespace App\Http\Controllers;

use App\Models\ChucNang;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreChucNangRequest;
use App\Http\Requests\UpdateChucNangRequest;

class ChucNangController extends Controller
{
    public function index(): JsonResponse
    {
        $chucNangs = ChucNang::orderBy('ma_chuc_nang', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $chucNangs,
            'total' => $chucNangs->count(),
        ], 200);
    }

    public function store(StoreChucNangRequest $request): JsonResponse
    {
        $chucNang = ChucNang::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Thêm chức năng thành công',
            'data' => $chucNang,
        ], 201);
    }

    public function update(UpdateChucNangRequest $request, $ma_chuc_nang): JsonResponse
    {
        $chucNang = ChucNang::find($ma_chuc_nang);

        if (!$chucNang) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chức năng',
            ], 404);
        }

        $chucNang->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật chức năng thành công',
            'data' => $chucNang,
        ], 200);
    }

    public function destroy($ma_chuc_nang): JsonResponse
    {
        $chucNang = ChucNang::find($ma_chuc_nang);

        if (!$chucNang) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chức năng',
            ], 404);
        }

        $chucNang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa chức năng thành công',
        ], 200);
    }
}
