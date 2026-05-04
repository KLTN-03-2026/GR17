<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use App\Models\Tour;
use App\Services\CustomerInvoicePresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoiTacOrderController extends Controller
{
    public function __construct(
        private readonly CustomerInvoicePresenter $invoicePresenter,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $doiTac = $request->user();
        if (!$doiTac || !isset($doiTac->ma_doi_tac)) {
            return response()->json([
                'success' => false,
                'message' => 'Ban khong co quyen truy cap',
            ], 403);
        }

        $tourIds = Tour::query()
            ->where('ma_doi_tac', $doiTac->ma_doi_tac)
            ->pluck('ma_tour');

        $hoaDons = HoaDon::query()
            ->with(['nhom', 'tour', 'tourKhoiHanh', 'khachHangDat', 'latestQrPayment'])
            ->where('loai_hoa_don', 0)
            ->whereIn('ma_doi_tuong', $tourIds)
            ->orderByDesc('ngay_tao')
            ->get()
            ->map(fn (HoaDon $hoaDon) => $this->invoicePresenter->presentPartner($hoaDon))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Lay danh sach don hang thanh cong',
            'data' => $hoaDons,
        ]);
    }
}
