<?php

namespace App\Http\Controllers;

use App\Models\DichVuDiaDiem;
use App\Http\Requests\StoreDichVuDiaDiemRequest;
use App\Http\Requests\UpdateDichVuDiaDiemRequest;
use Illuminate\Http\Request;

class DichVuDiaDiemController extends Controller
{


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


}