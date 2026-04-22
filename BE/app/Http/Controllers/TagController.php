<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;

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
    public function store(StoreTagRequest $request)
    {
        $tag = Tag::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Thêm tag thành công',
            'data' => $tag
        ], 201);
    }

    /**
     * Cập nhật một tag.
     */
    public function update(UpdateTagRequest $request, $maTag)
    {
        $tag = Tag::find($maTag);

        if (!$tag) {
            return response()->json([
                'success' => false,
                'message' => 'Tag không tồn tại'
            ], 404);
        }

        $tag->update($request->validated());

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
