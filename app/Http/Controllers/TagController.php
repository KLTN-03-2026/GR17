<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    /**
     * Lấy danh sách tất cả tag.
     */
    public function index()
    {
        $tags = Tag::with('tagDiaDiems')->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách tag thành công',
            'data' => $tags
        ], 200);
    }

    /**
     * Lấy chi tiết một tag.
     */
    public function show($maTag)
    {
        $tag = Tag::with('tagDiaDiems.diaDiem')->find($maTag);

        if (!$tag) {
            return response()->json([
                'success' => false,
                'message' => 'Tag không tồn tại'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin tag thành công',
            'data' => $tag
        ], 200);
    }

    /**
     * Thêm một tag mới.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ma_tag' => 'required|string|unique:tag|max:10',
            'ten_tag' => 'required|string|min:2|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Thêm tag thất bại',
                'errors' => $validator->errors()
            ], 422);
        }

        $tag = Tag::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Thêm tag thành công',
            'data' => $tag
        ], 201);
    }

    /**
     * Cập nhật một tag.
     */
    public function update(Request $request, $maTag)
    {
        $tag = Tag::find($maTag);

        if (!$tag) {
            return response()->json([
                'success' => false,
                'message' => 'Tag không tồn tại'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'ten_tag' => 'required|string|min:2|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật tag thất bại',
                'errors' => $validator->errors()
            ], 422);
        }

        $tag->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật tag thành công',
            'data' => $tag
        ], 200);
    }

    /**
     * Xóa một tag.
     */
    public function destroy($maTag)
    {
        $tag = Tag::find($maTag);

        if (!$tag) {
            return response()->json([
                'success' => false,
                'message' => 'Tag không tồn tại'
            ], 404);
        }

        $tag->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa tag thành công'
        ], 200);
    }
}
