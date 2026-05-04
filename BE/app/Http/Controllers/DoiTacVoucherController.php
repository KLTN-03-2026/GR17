<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DoiTacVoucherController extends Controller
{
    private function getPartnerId()
    {
        return request()->user()->ma_doi_tac;
    }

    public function index(Request $request)
    {
        $maDoiTac = $this->getPartnerId();
        $query = Voucher::where('ma_doi_tac', $maDoiTac)->orderBy('created_at', 'desc');
        
        $vouchers = $query->paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $vouchers
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ma_voucher' => 'required|string|unique:vouchers,ma_voucher|max:50',
            'ten_voucher' => 'required|string|max:255',
            'loai_giam_gia' => 'required|in:percent,fixed',
            'gia_tri_giam' => 'required|numeric|min:0',
            'giam_toi_da' => 'nullable|numeric|min:0',
            'don_toi_thieu' => 'required|numeric|min:0',
            'so_luong' => 'required|integer|min:1',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after_or_equal:ngay_bat_dau',
            'trang_thai' => 'boolean'
        ]);

        $voucher = new Voucher($request->all());
        $voucher->ma_voucher = strtoupper($request->ma_voucher);
        $voucher->ma_doi_tac = $this->getPartnerId();
        $voucher->save();

        return response()->json([
            'success' => true,
            'message' => 'Tạo mã giảm giá thành công',
            'data' => $voucher
        ], 201);
    }

    public function show($id)
    {
        $voucher = Voucher::where('ma_doi_tac', $this->getPartnerId())->find($id);
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy'], 404);
        }
        return response()->json(['success' => true, 'data' => $voucher]);
    }

    public function update(Request $request, $id)
    {
        $voucher = Voucher::where('ma_doi_tac', $this->getPartnerId())->find($id);
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy'], 404);
        }

        $request->validate([
            'ten_voucher' => 'required|string|max:255',
            'loai_giam_gia' => 'required|in:percent,fixed',
            'gia_tri_giam' => 'required|numeric|min:0',
            'giam_toi_da' => 'nullable|numeric|min:0',
            'don_toi_thieu' => 'required|numeric|min:0',
            'so_luong' => 'required|integer|min:1',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after_or_equal:ngay_bat_dau',
            'trang_thai' => 'boolean'
        ]);

        $voucher->update($request->except(['ma_voucher', 'ma_doi_tac', 'da_su_dung']));

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật mã giảm giá thành công',
            'data' => $voucher
        ]);
    }

    public function changeStatus($id)
    {
        $voucher = Voucher::where('ma_doi_tac', $this->getPartnerId())->find($id);
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy'], 404);
        }

        $voucher->update(['trang_thai' => !$voucher->trang_thai]);

        return response()->json([
            'success' => true,
            'message' => 'Đổi trạng thái thành công',
            'data' => $voucher
        ]);
    }

    public function destroy($id)
    {
        $voucher = Voucher::where('ma_doi_tac', $this->getPartnerId())->find($id);
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy'], 404);
        }

        if ($voucher->da_su_dung > 0) {
            return response()->json(['success' => false, 'message' => 'Không thể xóa mã đã có lượt sử dụng'], 400);
        }

        $voucher->delete();
        return response()->json(['success' => true, 'message' => 'Xóa thành công']);
    }
}
