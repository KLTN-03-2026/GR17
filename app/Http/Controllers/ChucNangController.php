<?php

namespace App\Http\Controllers;

use App\Models\ChucNang;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ten_chuc_nang' => 'required|string|max:100|unique:chuc_nang,ten_chuc_nang',
            'mo_ta' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $chucNang = ChucNang::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Them chuc nang thanh cong',
            'data' => $chucNang,
        ], 201);
    }

    public function update(Request $request, $ma_chuc_nang): JsonResponse
    {
        $chucNang = ChucNang::find($ma_chuc_nang);

        if (!$chucNang) {
            return response()->json([
                'success' => false,
                'message' => 'Khong tim thay chuc nang',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'ten_chuc_nang' => 'sometimes|required|string|max:100|unique:chuc_nang,ten_chuc_nang,' . $ma_chuc_nang . ',ma_chuc_nang',
            'mo_ta' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $chucNang->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cap nhat chuc nang thanh cong',
            'data' => $chucNang,
        ], 200);
    }

    public function destroy($ma_chuc_nang): JsonResponse
    {
        $chucNang = ChucNang::find($ma_chuc_nang);

        if (!$chucNang) {
            return response()->json([
                'success' => false,
                'message' => 'Khong tim thay chuc nang',
            ], 404);
        }

        $chucNang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoa chuc nang thanh cong',
        ], 200);
    }
}
