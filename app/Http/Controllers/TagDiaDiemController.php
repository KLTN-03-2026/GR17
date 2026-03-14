<?php

namespace App\Http\Controllers;

use App\Models\TagDiaDiem;
use App\Models\DiaDiem;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagDiaDiemController extends Controller
{
    /**
     * Lấy danh sách tất cả tag_dia_diem.
     */
    public function index()
    {
        $tagDiaDiems = TagDiaDiem::with(['diaDiem', 'tag'])->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách tag địa điểm thành công',
            'data' => $tagDiaDiems
        ], 200);
    }

    /**
     * Lấy chi tiết một tag_dia_diem.
     */
    public function show($maTagDiaDiem)
    {
        $tagDiaDiem = TagDiaDiem::with(['diaDiem', 'tag'])->find($maTagDiaDiem);

        if (!$tagDiaDiem) {
            return response()->json([
                'success' => false,
                'message' => 'Tag địa điểm không tồn tại'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin tag địa điểm thành công',
            'data' => $tagDiaDiem
        ], 200);
    }

    /**
     * Thêm một tag_dia_diem mới.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ma_tag_dia_diem' => 'required|string|unique:tag_dia_diem|max:10',
            'ma_dia_diem' => 'required|string|exists:dia_diem,ma_dia_diem',
            'ma_tag' => 'required|string|exists:tag,ma_tag',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Thêm tag địa điểm thất bại',
                'errors' => $validator->errors()
            ], 422);
        }

        $tagDiaDiem = TagDiaDiem::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Thêm tag địa điểm thành công',
            'data' => $tagDiaDiem->load(['diaDiem', 'tag'])
        ], 201);
    }

    /**
     * Cập nhật một tag_dia_diem.
     */
    public function update(Request $request, $maTagDiaDiem)
    {
        $tagDiaDiem = TagDiaDiem::find($maTagDiaDiem);

        if (!$tagDiaDiem) {
            return response()->json([
                'success' => false,
                'message' => 'Tag địa điểm không tồn tại'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'ma_dia_diem' => 'nullable|string|exists:dia_diem,ma_dia_diem',
            'ma_tag' => 'nullable|string|exists:tag,ma_tag',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật tag địa điểm thất bại',
                'errors' => $validator->errors()
            ], 422);
        }

        $tagDiaDiem->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật tag địa điểm thành công',
            'data' => $tagDiaDiem->load(['diaDiem', 'tag'])
        ], 200);
    }

    /**
     * Xóa một tag_dia_diem.
     */
    public function destroy($maTagDiaDiem)
    {
        $tagDiaDiem = TagDiaDiem::find($maTagDiaDiem);

        if (!$tagDiaDiem) {
            return response()->json([
                'success' => false,
                'message' => 'Tag địa điểm không tồn tại'
            ], 404);
        }

        $tagDiaDiem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa tag địa điểm thành công'
        ], 200);
    }

    /**
     * Lấy tất cả tag của một địa điểm.
     */
    public function getTagsByLocation($maDiaDiem)
    {
        $diaDiem = DiaDiem::find($maDiaDiem);

        if (!$diaDiem) {
            return response()->json([
                'success' => false,
                'message' => 'Địa điểm không tồn tại'
            ], 404);
        }

        $tags = $diaDiem->tagDiaDiems()->with('tag')->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy tag của địa điểm thành công',
            'data' => $tags
        ], 200);
    }

    /**
     * Lấy tất cả địa điểm của một tag.
     */
    public function getLocationsByTag($maTag)
    {
        $tag = Tag::find($maTag);

        if (!$tag) {
            return response()->json([
                'success' => false,
                'message' => 'Tag không tồn tại'
            ], 404);
        }

        $diaDiems = $tag->tagDiaDiems()->with('diaDiem')->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy địa điểm của tag thành công',
            'data' => $diaDiems
        ], 200);
    }
}
