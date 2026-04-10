<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoiTacTourRejectRequest;
use App\Http\Requests\DoiTacTourVisibilityRequest;
use App\Models\Tour;

class DoiTacTourModerationController extends Controller
{
    public function pending()
    {
        $tours = Tour::query()
            ->where('nguon_tao', 'doi_tac')
            ->where('trang_thai_duyet', 'pending_approval')
            ->orderByDesc('created_at')
            ->get();

        if ($tours->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không có tour đối tác đang chờ duyệt',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách tour chờ duyệt thành công',
            'data' => $tours,
        ], 200);
    }

    public function approve(string $ma_tour)
    {
        $tour = Tour::find($ma_tour);
        if (!$tour) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tour',
            ], 404);
        }

        if ($tour->nguon_tao !== 'doi_tac') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ duyệt tour được tạo bởi đối tác',
            ], 422);
        }

        if ($tour->trang_thai_duyet !== 'pending_approval') {
            return response()->json([
                'success' => false,
                'message' => 'Tour không ở trạng thái chờ duyệt',
            ], 422);
        }

        $tour->update([
            'trang_thai_duyet' => 'approved',
            'ly_do_tu_choi' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Duyệt tour thành công',
            'data' => $tour->fresh(),
        ], 200);
    }

    public function reject(DoiTacTourRejectRequest $request, string $ma_tour)
    {
        $tour = Tour::find($ma_tour);
        if (!$tour) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tour',
            ], 404);
        }

        if ($tour->nguon_tao !== 'doi_tac') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ từ chối tour được tạo bởi đối tác',
            ], 422);
        }

        if ($tour->trang_thai_duyet !== 'pending_approval') {
            return response()->json([
                'success' => false,
                'message' => 'Tour không ở trạng thái chờ duyệt',
            ], 422);
        }

        $tour->update([
            'trang_thai_duyet' => 'rejected',
            'trang_thai_hien_thi' => false,
            'ly_do_tu_choi' => $request->ly_do_tu_choi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Từ chối tour thành công',
            'data' => $tour->fresh(),
        ], 200);
    }

    public function visibility(DoiTacTourVisibilityRequest $request, string $ma_tour)
    {
        $tour = Tour::find($ma_tour);
        if (!$tour) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tour',
            ], 404);
        }

        if ($tour->nguon_tao !== 'doi_tac') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ cập nhật hiển thị cho tour đối tác',
            ], 422);
        }

        $visible = (bool) $request->trang_thai_hien_thi;
        if ($visible && $tour->trang_thai_duyet !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ tour đã duyệt mới được bật hiển thị',
            ], 422);
        }

        $tour->update([
            'trang_thai_hien_thi' => $visible,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật hiển thị tour thành công',
            'data' => $tour->fresh(),
        ], 200);
    }
}
