<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoiTacStoreDiaDiemRequest;
use App\Http\Requests\DoiTacUpdateDiaDiemRequest;
use App\Models\ChiTietTour;
use App\Models\DiaDiem;
use App\Models\DoiTac;
use App\Services\DiaDiemNormalizationService;
use Illuminate\Http\Request;

class DoiTacDiaDiemController extends Controller
{
    public function __construct(private readonly DiaDiemNormalizationService $normalizationService)
    {
    }

    public function index(Request $request)
    {
        $doiTac = $this->resolveDoiTac($request);
        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401);
        }

        $diaDiems = DiaDiem::query()
            ->where('ma_doi_tac_tao', $doiTac->ma_doi_tac)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách địa điểm đối tác thành công',
            'data' => $diaDiems,
        ], 200);
    }

    public function show(Request $request, string $ma_dia_diem)
    {
        $doiTac = $this->resolveDoiTac($request);
        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401);
        }

        $diaDiem = DiaDiem::find($ma_dia_diem);
        if (!$diaDiem) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy địa điểm',
            ], 404);
        }

        if ($diaDiem->ma_doi_tac_tao !== $doiTac->ma_doi_tac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem địa điểm này',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy chi tiết địa điểm thành công',
            'data' => $diaDiem,
        ], 200);
    }

    public function store(DoiTacStoreDiaDiemRequest $request)
    {
        $doiTac = $this->resolveDoiTac($request);
        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401);
        }

        $validated = $request->validated();
        $normalized = $this->normalizationService->normalizedPayload(
            $validated['ten_dia_diem'],
            $validated['dia_chi']
        );

        $duplicate = DiaDiem::query()
            ->where('ten_dia_diem_normalized', $normalized['ten_dia_diem_normalized'])
            ->where('dia_chi_normalized', $normalized['dia_chi_normalized'])
            ->first();

        if ($duplicate) {
            return response()->json([
                'success' => true,
                'message' => 'Địa điểm đã tồn tại, sử dụng địa điểm có sẵn',
                'data' => $duplicate,
                'is_duplicate' => true,
            ], 200);
        }

        $diaDiem = DiaDiem::create(array_merge($validated, $normalized, [
            'ma_doi_tac_tao' => $doiTac->ma_doi_tac,
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'pending_approval',
            'ly_do_tu_choi' => null,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Thêm địa điểm đối tác thành công',
            'data' => $diaDiem,
            'is_duplicate' => false,
        ], 201);
    }

    public function update(DoiTacUpdateDiaDiemRequest $request, string $ma_dia_diem)
    {
        $doiTac = $this->resolveDoiTac($request);
        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401);
        }

        $diaDiem = DiaDiem::find($ma_dia_diem);
        if (!$diaDiem) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy địa điểm',
            ], 404);
        }

        if ($diaDiem->ma_doi_tac_tao !== $doiTac->ma_doi_tac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền cập nhật địa điểm này',
            ], 403);
        }

        $data = $request->validated();
        $nextTenDiaDiem = $data['ten_dia_diem'] ?? $diaDiem->ten_dia_diem;
        $nextDiaChi = $data['dia_chi'] ?? $diaDiem->dia_chi;

        $normalized = $this->normalizationService->normalizedPayload($nextTenDiaDiem, $nextDiaChi);

        $duplicate = DiaDiem::query()
            ->where('ten_dia_diem_normalized', $normalized['ten_dia_diem_normalized'])
            ->where('dia_chi_normalized', $normalized['dia_chi_normalized'])
            ->where('ma_dia_diem', '!=', $diaDiem->ma_dia_diem)
            ->first();

        if ($duplicate) {
            return response()->json([
                'success' => true,
                'message' => 'Địa điểm đã tồn tại, sử dụng địa điểm có sẵn',
                'data' => $duplicate,
                'is_duplicate' => true,
            ], 200);
        }

        $diaDiem->update(array_merge($data, $normalized, [
            'trang_thai_duyet' => 'pending_approval',
            'ly_do_tu_choi' => null,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật địa điểm đối tác thành công',
            'data' => $diaDiem->fresh(),
            'is_duplicate' => false,
        ], 200);
    }

    public function destroy(Request $request, string $ma_dia_diem)
    {
        $doiTac = $this->resolveDoiTac($request);
        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401);
        }

        $diaDiem = DiaDiem::find($ma_dia_diem);
        if (!$diaDiem) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy địa điểm',
            ], 404);
        }

        if ($diaDiem->ma_doi_tac_tao !== $doiTac->ma_doi_tac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa địa điểm này',
            ], 403);
        }

        ChiTietTour::query()
            ->where('ma_dia_diem', $diaDiem->ma_dia_diem)
            ->delete();

        $diaDiem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa địa điểm thành công',
        ], 200);
    }

    private function resolveDoiTac(Request $request): ?DoiTac
    {
        $user = $request->user();

        return $user instanceof DoiTac ? $user : null;
    }
}
