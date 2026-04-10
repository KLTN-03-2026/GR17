<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoiTacAttachExistingDiaDiemToTourRequest;
use App\Http\Requests\DoiTacCreateAndAttachDiaDiemToTourRequest;
use App\Http\Requests\DoiTacUpdateTourDiaDiemRequest;
use App\Models\ChiTietTour;
use App\Models\DiaDiem;
use App\Models\DoiTac;
use App\Models\Tour;
use App\Services\DiaDiemNormalizationService;
use Illuminate\Http\Request;

class DoiTacTourDiaDiemController extends Controller
{
    public function __construct(private readonly DiaDiemNormalizationService $normalizationService)
    {
    }

    public function index(Request $request, string $ma_tour)
    {
        [$tour, $error] = $this->resolveOwnedTour($request, $ma_tour);
        if ($error) {
            return $error;
        }

        $items = ChiTietTour::query()
            ->with('diaDiem')
            ->where('ma_tour', $tour->ma_tour)
            ->orderBy('ngay_hanh_trinh')
            ->orderBy('thu_tu_hanh_trinh')
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy hành trình tour thành công',
            'data' => $items,
        ], 200);
    }

    public function attachExisting(
        DoiTacAttachExistingDiaDiemToTourRequest $request,
        string $ma_tour
    ) {
        [$tour, $error] = $this->resolveOwnedTour($request, $ma_tour);
        if ($error) {
            return $error;
        }

        $diaDiem = DiaDiem::find($request->ma_dia_diem);
        if (!$diaDiem) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy địa điểm',
            ], 404);
        }

        if ($this->isDiaDiemAttachedToTour($tour->ma_tour, $diaDiem->ma_dia_diem)) {
            return response()->json([
                'success' => false,
                'message' => 'Địa điểm đã tồn tại trong hành trình tour',
            ], 422);
        }

        $ngayHanhTrinh = (int) ($request->ngay_hanh_trinh ?? 1);

        $chiTietTour = ChiTietTour::create([
            'ma_tour' => $tour->ma_tour,
            'ma_dia_diem' => $diaDiem->ma_dia_diem,
            'ngay_hanh_trinh' => $ngayHanhTrinh,
            'thu_tu_hanh_trinh' => $request->thu_tu_hanh_trinh ?? $this->nextOrder($tour->ma_tour, $ngayHanhTrinh),
            'ghi_chu_hanh_trinh' => $request->ghi_chu_hanh_trinh,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gắn địa điểm có sẵn vào tour thành công',
            'data' => $chiTietTour->fresh()->load('diaDiem'),
        ], 201);
    }

    public function createAndAttach(
        DoiTacCreateAndAttachDiaDiemToTourRequest $request,
        string $ma_tour
    ) {
        [$tour, $error] = $this->resolveOwnedTour($request, $ma_tour);
        if ($error) {
            return $error;
        }

        $validated = $request->validated();
        $normalized = $this->normalizationService->normalizedPayload(
            $validated['ten_dia_diem'],
            $validated['dia_chi']
        );

        $diaDiem = DiaDiem::query()
            ->where('ten_dia_diem_normalized', $normalized['ten_dia_diem_normalized'])
            ->where('dia_chi_normalized', $normalized['dia_chi_normalized'])
            ->first();

        $isDuplicateLocation = $diaDiem !== null;
        if (!$diaDiem) {
            $diaDiem = DiaDiem::create([
                'ten_dia_diem' => $validated['ten_dia_diem'],
                'ten_dia_diem_normalized' => $normalized['ten_dia_diem_normalized'],
                'loai' => $validated['loai'],
                'dia_chi' => $validated['dia_chi'],
                'dia_chi_normalized' => $normalized['dia_chi_normalized'],
                'sdt' => $validated['sdt'] ?? null,
                'kinh_do' => $validated['kinh_do'],
                'vi_do' => $validated['vi_do'],
                'gio_mo_cua' => $validated['gio_mo_cua'] ?? null,
                'gio_dong_cua' => $validated['gio_dong_cua'] ?? null,
                'gia_giao_dong' => $validated['gia_giao_dong'] ?? null,
                'hinh_anh' => $validated['hinh_anh'] ?? null,
                'mo_ta' => $validated['mo_ta'] ?? null,
                'thoi_gian_tham_quan' => $validated['thoi_gian_tham_quan'] ?? null,
                'ma_doi_tac_tao' => $tour->ma_doi_tac,
                'nguon_tao' => 'doi_tac',
                'trang_thai_duyet' => 'pending_approval',
                'ly_do_tu_choi' => null,
            ]);
        }

        if ($this->isDiaDiemAttachedToTour($tour->ma_tour, $diaDiem->ma_dia_diem)) {
            return response()->json([
                'success' => false,
                'message' => 'Địa điểm đã tồn tại trong hành trình tour',
                'is_duplicate_location' => $isDuplicateLocation,
                'dia_diem' => $diaDiem,
            ], 422);
        }

        $ngayHanhTrinh = (int) ($validated['ngay_hanh_trinh'] ?? 1);

        $chiTietTour = ChiTietTour::create([
            'ma_tour' => $tour->ma_tour,
            'ma_dia_diem' => $diaDiem->ma_dia_diem,
            'ngay_hanh_trinh' => $ngayHanhTrinh,
            'thu_tu_hanh_trinh' => $validated['thu_tu_hanh_trinh'] ?? $this->nextOrder($tour->ma_tour, $ngayHanhTrinh),
            'ghi_chu_hanh_trinh' => $validated['ghi_chu_hanh_trinh'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo địa điểm và gắn vào tour thành công',
            'is_duplicate_location' => $isDuplicateLocation,
            'data' => [
                'dia_diem' => $diaDiem,
                'chi_tiet_tour' => $chiTietTour->fresh()->load('diaDiem'),
            ],
        ], 201);
    }

    public function update(
        DoiTacUpdateTourDiaDiemRequest $request,
        string $ma_tour,
        string $ma_chi_tiet_tour
    ) {
        [$tour, $error] = $this->resolveOwnedTour($request, $ma_tour);
        if ($error) {
            return $error;
        }

        $chiTietTour = ChiTietTour::query()
            ->where('ma_tour', $tour->ma_tour)
            ->where('ma_chi_tiet_tour', $ma_chi_tiet_tour)
            ->first();

        if (!$chiTietTour) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chi tiết hành trình',
            ], 404);
        }

        $chiTietTour->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật hành trình tour thành công',
            'data' => $chiTietTour->fresh()->load('diaDiem'),
        ], 200);
    }

    public function destroy(Request $request, string $ma_tour, string $ma_chi_tiet_tour)
    {
        [$tour, $error] = $this->resolveOwnedTour($request, $ma_tour);
        if ($error) {
            return $error;
        }

        $chiTietTour = ChiTietTour::query()
            ->where('ma_tour', $tour->ma_tour)
            ->where('ma_chi_tiet_tour', $ma_chi_tiet_tour)
            ->first();

        if (!$chiTietTour) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chi tiết hành trình',
            ], 404);
        }

        $chiTietTour->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa địa điểm khỏi hành trình tour thành công',
        ], 200);
    }

    private function resolveOwnedTour(Request $request, string $ma_tour): array
    {
        $doiTac = $this->resolveDoiTac($request);
        if (!$doiTac) {
            return [null, response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401)];
        }

        $tour = Tour::find($ma_tour);
        if (!$tour) {
            return [null, response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tour',
            ], 404)];
        }

        if ($tour->ma_doi_tac !== $doiTac->ma_doi_tac) {
            return [null, response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thao tác tour này',
            ], 403)];
        }

        return [$tour, null];
    }

    private function resolveDoiTac(Request $request): ?DoiTac
    {
        $user = $request->user();

        return $user instanceof DoiTac ? $user : null;
    }

    private function isDiaDiemAttachedToTour(string $ma_tour, string $ma_dia_diem): bool
    {
        return ChiTietTour::query()
            ->where('ma_tour', $ma_tour)
            ->where('ma_dia_diem', $ma_dia_diem)
            ->exists();
    }

    private function nextOrder(string $ma_tour, int $ngayHanhTrinh = 1): int
    {
        $maxOrder = ChiTietTour::query()
            ->where('ma_tour', $ma_tour)
            ->where('ngay_hanh_trinh', $ngayHanhTrinh)
            ->max('thu_tu_hanh_trinh');

        return ((int) $maxOrder) + 1;
    }
}
