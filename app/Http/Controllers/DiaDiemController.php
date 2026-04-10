<?php

namespace App\Http\Controllers;

use App\Models\DiaDiem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreDiaDiemRequest;
use App\Http\Requests\UpdateDiaDiemRequest;

class DiaDiemController extends Controller
{
    /**
     * Lấy danh sách tất cả địa điểm với lọc.
     */
    public function index(Request $request)
    {
        $query = DiaDiem::query()->approved();

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
        $diaDiem = DiaDiem::approved()
            ->with('tagDiaDiems.tag')
            ->find($maDiaDiem);

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
    public function store(StoreDiaDiemRequest $request)
    {
        $diaDiem = DiaDiem::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Thêm địa điểm thành công',
            'data' => $diaDiem
        ], 201);
    }

    /**
     * Cập nhật một địa điểm.
     */
    public function update(UpdateDiaDiemRequest $request, $maDiaDiem)
    {
        $diaDiem = DiaDiem::find($maDiaDiem);

        if (!$diaDiem) {
            return response()->json([
                'success' => false,
                'message' => 'Địa điểm không tồn tại'
            ], 404);
        }

        $diaDiem->update($request->validated());

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

        $diaDiems = DiaDiem::approved()
            ->where('loai', $loai)
            ->with('tagDiaDiems.tag')
            ->get();

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
        $diaDiems = DiaDiem::approved()
            ->whereHas('tagDiaDiems', function ($query) use ($maTag) {
                $query->where('ma_tag', $maTag);
            })
            ->with('tagDiaDiems.tag')
            ->get();

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
