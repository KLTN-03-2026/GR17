<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoiTacChangeStatusRequest;
use App\Http\Requests\DoiTacStoreRequest;
use App\Http\Requests\DoiTacUpdateRequest;
use App\Models\DoiTac;
use Illuminate\Support\Facades\Hash;

class DoiTacAdminController extends Controller
{
    public function index()
    {
        $doiTacs = DoiTac::whereNotIn('trang_thai_duyet', ['pending'])->get();

        if ($doiTacs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không có đối tác nào',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách đối tác thành công',
            'data' => $doiTacs,
        ], 200);
    }

    public function show(string $ma_doi_tac)
    {
        $doiTac = DoiTac::find($ma_doi_tac);

        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đối tác',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin đối tác thành công',
            'data' => $doiTac,
        ], 200);
    }

    public function store(DoiTacStoreRequest $request)
    {
        $data = $request->validated();
        $data['mat_khau'] = Hash::make($data['mat_khau']);

        $doiTac = DoiTac::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Thêm đối tác thành công',
            'data' => $doiTac,
        ], 201);
    }

    public function update(DoiTacUpdateRequest $request, string $ma_doi_tac)
    {
        $doiTac = DoiTac::find($ma_doi_tac);

        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đối tác',
            ], 404);
        }

        $data = $request->validated();
        if (isset($data['mat_khau'])) {
            $data['mat_khau'] = Hash::make($data['mat_khau']);
        }

        $doiTac->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật đối tác thành công',
            'data' => $doiTac,
        ], 200);
    }

    public function changeStatus(DoiTacChangeStatusRequest $request, string $ma_doi_tac)
    {
        $doiTac = DoiTac::find($ma_doi_tac);

        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đối tác',
            ], 404);
        }

        $doiTac->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái đối tác thành công',
            'data' => $doiTac,
        ], 200);
    }

    public function pending()
    {
        $doiTacs = DoiTac::where('trang_thai_duyet', 'pending')->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách đối tác chờ duyệt thành công',
            'data' => $doiTacs,
        ], 200);
    }

    public function approve(string $ma_doi_tac)
    {
        $doiTac = DoiTac::find($ma_doi_tac);

        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đối tác',
            ], 404);
        }

        $doiTac->update([
            'trang_thai_duyet' => 'approved',
            'ly_do_tu_choi' => null,
            'is_block' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Duyệt đối tác thành công',
            'data' => $doiTac,
        ], 200);
    }

    public function reject(\Illuminate\Http\Request $request, string $ma_doi_tac)
    {
        $request->validate([
            'ly_do_tu_choi' => 'required|string',
        ]);

        $doiTac = DoiTac::find($ma_doi_tac);

        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đối tác',
            ], 404);
        }

        $doiTac->update([
            'trang_thai_duyet' => 'rejected',
            'ly_do_tu_choi' => $request->ly_do_tu_choi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Từ chối đối tác thành công',
            'data' => $doiTac,
        ], 200);
    }
}
