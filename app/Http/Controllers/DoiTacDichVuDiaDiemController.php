<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoiTacStoreDichVuDiaDiemRequest;
use App\Http\Requests\DoiTacUpdateDichVuDiaDiemRequest;
use App\Models\DiaDiem;
use App\Models\DichVuDiaDiem;
use App\Models\DoiTac;
use Illuminate\Http\Request;

class DoiTacDichVuDiaDiemController extends Controller
{
    public function index(Request $request, string $ma_dia_diem)
    {
        $doiTac = $this->resolveDoiTac($request);
        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401);
        }

        $diaDiem = DiaDiem::find($ma_dia_diem);
        if (!$diaDiem || $diaDiem->ma_doi_tac_tao !== $doiTac->ma_doi_tac) {
            return response()->json([
                'success' => false,
                'message' => 'Địa điểm không tồn tại hoặc bạn không có quyền',
            ], 403);
        }

        $dichVuList = DichVuDiaDiem::where('ma_dia_diem', $ma_dia_diem)->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách dịch vụ thành công',
            'data' => $dichVuList,
        ], 200);
    }

    public function store(DoiTacStoreDichVuDiaDiemRequest $request, string $ma_dia_diem)
    {
        $doiTac = $this->resolveDoiTac($request);
        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401);
        }

        if ($request->input('ma_dia_diem') !== $ma_dia_diem) {
             return response()->json([
                'success' => false,
                'message' => 'Mã địa điểm không hợp lệ',
            ], 400);           
        }

        $diaDiem = DiaDiem::find($ma_dia_diem);
        if (!$diaDiem || $diaDiem->ma_doi_tac_tao !== $doiTac->ma_doi_tac) {
            return response()->json([
                'success' => false,
                'message' => 'Địa điểm không tồn tại hoặc bạn không có quyền',
            ], 403);
        }

        $validated = $request->validated();
        $dichVu = DichVuDiaDiem::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Thêm dịch vụ thành công',
            'data' => $dichVu,
        ], 201);
    }

    public function update(DoiTacUpdateDichVuDiaDiemRequest $request, string $ma_dia_diem, string $ma_dich_vu)
    {
        $doiTac = $this->resolveDoiTac($request);
        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401);
        }

        $diaDiem = DiaDiem::find($ma_dia_diem);
        if (!$diaDiem || $diaDiem->ma_doi_tac_tao !== $doiTac->ma_doi_tac) {
            return response()->json([
                'success' => false,
                'message' => 'Địa điểm không tồn tại hoặc bạn không có quyền',
            ], 403);
        }

        $dichVu = DichVuDiaDiem::where('ma_dia_diem', $ma_dia_diem)->where('ma_dich_vu', $ma_dich_vu)->first();
        if (!$dichVu) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy dịch vụ',
            ], 404);
        }

        $validated = $request->validated();
        $dichVu->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật dịch vụ thành công',
            'data' => $dichVu->fresh(),
        ], 200);
    }

    public function destroy(Request $request, string $ma_dia_diem, string $ma_dich_vu)
    {
        $doiTac = $this->resolveDoiTac($request);
        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401);
        }

        $diaDiem = DiaDiem::find($ma_dia_diem);
        if (!$diaDiem || $diaDiem->ma_doi_tac_tao !== $doiTac->ma_doi_tac) {
            return response()->json([
                'success' => false,
                'message' => 'Địa điểm không tồn tại hoặc bạn không có quyền',
            ], 403);
        }

        $dichVu = DichVuDiaDiem::where('ma_dia_diem', $ma_dia_diem)->where('ma_dich_vu', $ma_dich_vu)->first();
        if (!$dichVu) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy dịch vụ',
            ], 404);
        }

        $dichVu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa dịch vụ thành công',
        ], 200);
    }

    private function resolveDoiTac(Request $request): ?DoiTac
    {
        $user = $request->user();

        return $user instanceof DoiTac ? $user : null;
    }
}
