<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHoaDonRequest;
use App\Http\Requests\UpdateHoaDonRequest;
use App\Http\Requests\UpdateHoaDonStatusRequest;
use App\Models\HoaDon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HoaDonController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = HoaDon::query()
            ->with('nhom')
            ->when($request->filled('ma_nhom'), function ($query) use ($request) {
                $query->where('ma_nhom', $request->input('ma_nhom'));
            })
            ->when($request->filled('loai_hoa_don'), function ($query) use ($request) {
                $query->where('loai_hoa_don', $request->integer('loai_hoa_don'));
            })
            ->when($request->filled('trang_thai_thanh_toan'), function ($query) use ($request) {
                $query->where('trang_thai_thanh_toan', $request->integer('trang_thai_thanh_toan'));
            })
            ->when($request->filled('ma_doi_tuong'), function ($query) use ($request) {
                $query->where('ma_doi_tuong', $request->input('ma_doi_tuong'));
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('ngay_tao', '>=', $request->input('date_from'));
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('ngay_tao', '<=', $request->input('date_to'));
            })
            ->orderBy('ngay_tao', 'desc')
            ->orderBy('created_at', 'desc');

        $hoaDons = $request->filled('per_page')
            ? $query->paginate(max(1, min($request->integer('per_page'), 100)))
            : $query->get();

        return $this->successResponse('Lay danh sach hoa don thanh cong', $hoaDons);
    }

    public function show($ma_hoa_don): JsonResponse
    {
        $hoaDon = HoaDon::with('nhom')->find($ma_hoa_don);

        if (!$hoaDon) {
            return $this->errorResponse('Khong tim thay hoa don', 404);
        }

        return $this->successResponse('Lay thong tin hoa don thanh cong', $hoaDon);
    }

    public function store(StoreHoaDonRequest $request): JsonResponse
    {
        try {
            $hoaDon = HoaDon::create($request->validated());

            return $this->successResponse('Them hoa don thanh cong', $hoaDon->load('nhom'), 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Co loi xay ra khi them hoa don', 500);
        }
    }

    public function update(UpdateHoaDonRequest $request, $ma_hoa_don): JsonResponse
    {
        $hoaDon = HoaDon::find($ma_hoa_don);

        if (!$hoaDon) {
            return $this->errorResponse('Khong tim thay hoa don', 404);
        }

        try {
            $hoaDon->update($request->validated());

            return $this->successResponse('Cap nhat hoa don thanh cong', $hoaDon->load('nhom'));
        } catch (\Exception $e) {
            return $this->errorResponse('Cap nhat hoa don that bai', 500);
        }
    }

    public function updateStatus(UpdateHoaDonStatusRequest $request, $ma_hoa_don): JsonResponse
    {
        $hoaDon = HoaDon::find($ma_hoa_don);

        if (!$hoaDon) {
            return $this->errorResponse('Khong tim thay hoa don', 404);
        }

        $hoaDon->update($request->validated());

        return $this->successResponse('Cap nhat trang thai hoa don thanh cong', $hoaDon->load('nhom'));
    }

    public function getByNhom($maNhom): JsonResponse
    {
        $hoaDons = HoaDon::query()
            ->with('nhom')
            ->forGroup($maNhom)
            ->orderBy('ngay_tao', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->successResponse('Lay hoa don theo nhom thanh cong', $hoaDons);
    }

    public function summary(Request $request): JsonResponse
    {
        $query = HoaDon::query()
            ->when($request->filled('ma_nhom'), function ($query) use ($request) {
                $query->where('ma_nhom', $request->input('ma_nhom'));
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('ngay_tao', '>=', $request->input('date_from'));
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('ngay_tao', '<=', $request->input('date_to'));
            });

        $statusCounts = (clone $query)
            ->selectRaw('trang_thai_thanh_toan, COUNT(*) as total')
            ->groupBy('trang_thai_thanh_toan')
            ->pluck('total', 'trang_thai_thanh_toan');

        return $this->successResponse('Thong ke hoa don thanh cong', [
            'total_invoices' => (clone $query)->count(),
            'total_amount' => (float) (clone $query)->sum('tong_tien'),
            'paid_count' => (int) ($statusCounts[HoaDon::STATUS_PAID] ?? 0),
            'unpaid_count' => (int) ($statusCounts[HoaDon::STATUS_UNPAID] ?? 0),
            'pending_count' => (int) ($statusCounts[HoaDon::STATUS_PENDING] ?? 0),
            'paid_amount' => (float) (clone $query)->where('trang_thai_thanh_toan', HoaDon::STATUS_PAID)->sum('tong_tien'),
            'unpaid_amount' => (float) (clone $query)->where('trang_thai_thanh_toan', HoaDon::STATUS_UNPAID)->sum('tong_tien'),
        ]);
    }

    public function destroy($ma_hoa_don): JsonResponse
    {
        $hoaDon = HoaDon::find($ma_hoa_don);

        if (!$hoaDon) {
            return $this->errorResponse('Khong tim thay hoa don', 404);
        }

        try {
            $hoaDon->delete();

            return $this->successResponse('Xoa hoa don thanh cong');
        } catch (\Exception $e) {
            return $this->errorResponse('Xoa hoa don that bai. Co the hoa don dang duoc su dung.', 500);
        }
    }

    private function successResponse(string $message, mixed $data = null, int $status = 200): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $status);
    }

    private function errorResponse(string $message, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}