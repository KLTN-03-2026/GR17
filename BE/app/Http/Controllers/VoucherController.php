<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\Tour;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VoucherController extends Controller
{
    public function getAvailableVouchers(Request $request)
    {
        $request->validate([
            'ma_tour' => 'required|string',
            'tong_tien' => 'required|numeric'
        ]);

        $tour = Tour::find($request->ma_tour);
        if (!$tour) {
            return response()->json(['success' => false, 'message' => 'Tour không tồn tại'], 404);
        }

        $maDoiTac = $tour->ma_doi_tac;
        $now = Carbon::now();

        // Lấy tất cả voucher đang active, còn hạn, chưa hết lượt
        $vouchers = Voucher::where('trang_thai', true)
            ->where('ngay_bat_dau', '<=', $now)
            ->where('ngay_ket_thuc', '>=', $now)
            ->whereRaw('so_luong > da_su_dung')
            ->where(function($q) use ($maDoiTac) {
                $q->whereNull('ma_doi_tac') // Voucher sàn
                  ->orWhere('ma_doi_tac', $maDoiTac); // Voucher đối tác của tour này
            })
            ->get();

        $tongTien = $request->tong_tien;
        
        $validVouchers = [];
        $invalidVouchers = [];

        foreach ($vouchers as $v) {
            if ($tongTien >= $v->don_toi_thieu) {
                $validVouchers[] = $v;
            } else {
                $invalidVouchers[] = $v;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'valid' => $validVouchers,
                'invalid' => $invalidVouchers
            ]
        ]);
    }

    public function applyVoucher(Request $request)
    {
        $request->validate([
            'ma_voucher' => 'required|string',
            'ma_tour' => 'required|string',
            'tong_tien' => 'required|numeric'
        ]);

        $voucher = Voucher::where('ma_voucher', $request->ma_voucher)->first();
        
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không tồn tại'], 404);
        }

        if (!$voucher->trang_thai) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá đã bị khóa'], 400);
        }

        $now = Carbon::now();
        if ($now < $voucher->ngay_bat_dau) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá chưa đến thời gian sử dụng'], 400);
        }

        if ($now > $voucher->ngay_ket_thuc) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết hạn'], 400);
        }

        if ($voucher->da_su_dung >= $voucher->so_luong) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng'], 400);
        }

        if ($request->tong_tien < $voucher->don_toi_thieu) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng chưa đạt giá trị tối thiểu'], 400);
        }

        $tour = Tour::find($request->ma_tour);
        if ($voucher->ma_doi_tac && $voucher->ma_doi_tac !== $tour->ma_doi_tac) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không áp dụng cho tour này'], 400);
        }

        // Tính tiền giảm
        $tienGiam = 0;
        if ($voucher->loai_giam_gia == 'fixed') {
            $tienGiam = $voucher->gia_tri_giam;
        } else {
            $tienGiam = ($request->tong_tien * $voucher->gia_tri_giam) / 100;
            if ($voucher->giam_toi_da && $tienGiam > $voucher->giam_toi_da) {
                $tienGiam = $voucher->giam_toi_da;
            }
        }

        // Đảm bảo tiền giảm không lớn hơn tổng tiền
        if ($tienGiam > $request->tong_tien) {
            $tienGiam = $request->tong_tien;
        }

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã thành công',
            'data' => [
                'tien_giam_gia' => $tienGiam,
                'tong_tien_sau_giam' => $request->tong_tien - $tienGiam,
                'voucher' => $voucher
            ]
        ]);
    }
}
