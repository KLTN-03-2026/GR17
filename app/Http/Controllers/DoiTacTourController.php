<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoiTacStoreTourRequest;
use App\Http\Requests\DoiTacUpdateTourRequest;
use App\Models\ChiTietTour;
use App\Models\DoiTac;
use App\Models\Tour;
use Illuminate\Http\Request;

class DoiTacTourController extends Controller
{
    public function index(Request $request)
    {
        $doiTac = $this->resolveDoiTac($request);
        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401);
        }

        $tours = Tour::query()
            ->where('ma_doi_tac', $doiTac->ma_doi_tac)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách tour đối tác thành công',
            'data' => $tours,
        ], 200);
    }

    public function show(Request $request, string $ma_tour)
    {
        $doiTac = $this->resolveDoiTac($request);
        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401);
        }

        $tour = Tour::find($ma_tour);
        if (!$tour) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tour',
            ], 404);
        }

        if ($tour->ma_doi_tac !== $doiTac->ma_doi_tac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem tour này',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy chi tiết tour thành công',
            'data' => $tour,
        ], 200);
    }

    public function store(DoiTacStoreTourRequest $request)
    {
        $doiTac = $this->resolveDoiTac($request);
        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401);
        }

        $tour = Tour::create(array_merge($request->validated(), [
            'ma_doi_tac' => $doiTac->ma_doi_tac,
            'nguon_tao' => 'doi_tac',
            'trang_thai_duyet' => 'draft',
            'trang_thai_hien_thi' => false,
            'ly_do_tu_choi' => null,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Thêm tour đối tác thành công',
            'data' => $tour,
        ], 201);
    }

    public function update(DoiTacUpdateTourRequest $request, string $ma_tour)
    {
        $doiTac = $this->resolveDoiTac($request);
        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401);
        }

        $tour = Tour::find($ma_tour);
        if (!$tour) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tour',
            ], 404);
        }

        if ($tour->ma_doi_tac !== $doiTac->ma_doi_tac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền cập nhật tour này',
            ], 403);
        }

        $tour->update(array_merge($request->validated(), [
            'trang_thai_duyet' => 'draft',
            'trang_thai_hien_thi' => false,
            'ly_do_tu_choi' => null,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật tour đối tác thành công',
            'data' => $tour->fresh(),
        ], 200);
    }

    public function destroy(Request $request, string $ma_tour)
    {
        $doiTac = $this->resolveDoiTac($request);
        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401);
        }

        $tour = Tour::find($ma_tour);
        if (!$tour) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tour',
            ], 404);
        }

        if ($tour->ma_doi_tac !== $doiTac->ma_doi_tac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa tour này',
            ], 403);
        }

        ChiTietTour::query()
            ->where('ma_tour', $tour->ma_tour)
            ->delete();

        $tour->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa tour thành công',
        ], 200);
    }

    public function submit(Request $request, string $ma_tour)
    {
        $doiTac = $this->resolveDoiTac($request);
        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập đối tác',
            ], 401);
        }

        $tour = Tour::find($ma_tour);
        if (!$tour) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tour',
            ], 404);
        }

        if ($tour->ma_doi_tac !== $doiTac->ma_doi_tac) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền gửi duyệt tour này',
            ], 403);
        }

        if (!in_array($tour->trang_thai_duyet, ['draft', 'rejected'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Tour không hợp lệ để gửi duyệt',
            ], 422);
        }

        $tour->update([
            'trang_thai_duyet' => 'pending_approval',
            'trang_thai_hien_thi' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gửi duyệt tour thành công',
            'data' => $tour->fresh(),
        ], 200);
    }

    private function resolveDoiTac(Request $request): ?DoiTac
    {
        $user = $request->user();

        return $user instanceof DoiTac ? $user : null;
    }
}
