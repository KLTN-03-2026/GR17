<?php

namespace App\Http\Controllers;

use App\Models\DiaDiem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiaDiemController extends Controller
{
    /**
     * Lấy danh sách tất cả địa điểm với lọc.
     */
    public function index(Request $request)
    {
        $query = DiaDiem::query();

        // Lọc theo loại
        if ($request->has('loai')) {
            $query->where('loai', $request->loai);
        }

        // Lọc theo tag
        if ($request->has('ma_tag')) {
            $query->whereHas('tagDiaDiems', function ($q) {
                $q->where('ma_tag', request('ma_tag'));
            });
        }

        // Tìm kiếm theo tên
        if ($request->has('search')) {
            $query->where('ten_dia_diem', 'like', '%' . $request->search . '%');
        }

        $diaDiems = $query->with('tagDiaDiems.tag')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách địa điểm thành công',
            'data' => $diaDiems
        ], 200);
    }

    /**
     * Lấy chi tiết một địa điểm.
     */
    public function show($maDiaDiem)
    {
        $diaDiem = DiaDiem::with('tagDiaDiems.tag')->find($maDiaDiem);

        if (!$diaDiem) {
            return response()->json([
                'success' => false,
                'message' => 'Địa điểm không tồn tại'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin chi tiết thành công',
            'data' => $diaDiem
        ], 200);
    }

    /**
     * Thêm một địa điểm mới.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ma_dia_diem' => 'required|string|unique:dia_diem|max:10',
            'ten_dia_diem' => 'required|string|min:5|max:100',
            'loai' => 'required|integer|in:1,2,3',
            'dia_chi' => 'required|string|max:255',
            'sdt' => 'nullable|regex:/^0[0-9]{9}$/',
            'kinh_do' => 'required|numeric|between:-180,180',
            'vi_do' => 'required|numeric|between:-90,90',
            'gio_mo_cua' => 'nullable|date_format:H:i',
            'gio_dong_cua' => 'nullable|date_format:H:i',
            'gia_giao_dong' => 'nullable|numeric|min:0',
            'hinh_anh' => 'nullable|string|url',
            'mo_ta' => 'nullable|string',
            'thoi_gian_tham_quan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Thêm địa điểm thất bại',
                'errors' => $validator->errors()
            ], 422);
        }

        $diaDiem = DiaDiem::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Thêm địa điểm thành công',
            'data' => $diaDiem
        ], 201);
    }

    /**
     * Cập nhật một địa điểm.
     */
    public function update(Request $request, $maDiaDiem)
    {
        $diaDiem = DiaDiem::find($maDiaDiem);

        if (!$diaDiem) {
            return response()->json([
                'success' => false,
                'message' => 'Địa điểm không tồn tại'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'ten_dia_diem' => 'nullable|string|min:5|max:100',
            'loai' => 'nullable|integer|in:1,2,3',
            'dia_chi' => 'nullable|string|max:255',
            'sdt' => 'nullable|regex:/^0[0-9]{9}$/',
            'kinh_do' => 'nullable|numeric|between:-180,180',
            'vi_do' => 'nullable|numeric|between:-90,90',
            'gio_mo_cua' => 'nullable|date_format:H:i',
            'gio_dong_cua' => 'nullable|date_format:H:i',
            'gia_giao_dong' => 'nullable|numeric|min:0',
            'hinh_anh' => 'nullable|string|url',
            'mo_ta' => 'nullable|string',
            'thoi_gian_tham_quan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật địa điểm thất bại',
                'errors' => $validator->errors()
            ], 422);
        }

        $diaDiem->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật địa điểm thành công',
            'data' => $diaDiem
        ], 200);
    }

    /**
     * Xóa một địa điểm.
     */
    public function destroy($maDiaDiem)
    {
        $diaDiem = DiaDiem::find($maDiaDiem);

        if (!$diaDiem) {
            return response()->json([
                'success' => false,
                'message' => 'Địa điểm không tồn tại'
            ], 404);
        }

        $diaDiem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa địa điểm thành công'
        ], 200);
    }

    /**
     * Lọc địa điểm theo loại.
     */
    public function filterByType($loai)
    {
        $validator = Validator::make(['loai' => $loai], [
            'loai' => 'required|integer|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Loại địa điểm không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $diaDiems = DiaDiem::where('loai', $loai)->with('tagDiaDiems.tag')->get();

        return response()->json([
            'success' => true,
            'message' => 'Lọc địa điểm theo loại thành công',
            'data' => $diaDiems
        ], 200);
    }

    /**
     * Lọc địa điểm theo tag.
     */
    public function filterByTag($maTag)
    {
        $diaDiems = DiaDiem::whereHas('tagDiaDiems', function ($query) use ($maTag) {
            $query->where('ma_tag', $maTag);
        })->with('tagDiaDiems.tag')->get();

        if ($diaDiems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy địa điểm với tag này'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lọc địa điểm theo tag thành công',
            'data' => $diaDiems
        ], 200);
    }
}
