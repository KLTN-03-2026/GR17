<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoiTacDiaDiemRejectRequest;
use App\Models\DiaDiem;
use Illuminate\Http\Request;

class DoiTacDiaDiemModerationController extends Controller
{
    public function index(Request $request)
    {
        $allowedStatuses = ['pending_approval', 'approved', 'rejected'];
        $allowedLoai = ['1', '2', '3'];

        $status = (string) $request->query('trang_thai_duyet', '');
        if ($status !== '' && !in_array($status, $allowedStatuses, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Trạng thái duyệt không hợp lệ',
            ], 422);
        }

        $loai = (string) $request->query('loai', '');
        if ($loai !== '' && !in_array($loai, $allowedLoai, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Loại địa điểm không hợp lệ',
            ], 422);
        }

        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(1, min(100, $perPage));

        $query = DiaDiem::query()
            ->where('nguon_tao', 'doi_tac')
            ->with('doiTac:ma_doi_tac,ten_doi_tac,ten_nguoi_dai_dien,email');

        if ($status !== '') {
            $query->where('trang_thai_duyet', $status);
        }

        if ($loai !== '') {
            $query->where('loai', (int) $loai);
        }

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('ten_dia_diem', 'like', "%{$search}%")
                    ->orWhere('ma_dia_diem', 'like', "%{$search}%");
            });
        }

        $diaDiems = $query
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách địa điểm đối tác thành công',
            'data' => $diaDiems,
        ], 200);
    }

    public function pending()
    {
        $diaDiems = DiaDiem::query()
            ->where('nguon_tao', 'doi_tac')
            ->where('trang_thai_duyet', 'pending_approval')
            ->orderByDesc('created_at')
            ->get();

        if ($diaDiems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không có địa điểm đối tác đang chờ duyệt',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách địa điểm chờ duyệt thành công',
            'data' => $diaDiems,
        ], 200);
    }

    public function approve(string $ma_dia_diem)
    {
        $diaDiem = DiaDiem::find($ma_dia_diem);
        if (!$diaDiem) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy địa điểm',
            ], 404);
        }

        if ($diaDiem->nguon_tao !== 'doi_tac') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ duyệt địa điểm được tạo bởi đối tác',
            ], 422);
        }

        if ($diaDiem->trang_thai_duyet !== 'pending_approval') {
            return response()->json([
                'success' => false,
                'message' => 'Địa điểm không ở trạng thái chờ duyệt',
            ], 422);
        }

        $diaDiem->update([
            'trang_thai_duyet' => 'approved',
            'ly_do_tu_choi' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Duyệt địa điểm thành công',
            'data' => $diaDiem->fresh(),
        ], 200);
    }

    public function reject(DoiTacDiaDiemRejectRequest $request, string $ma_dia_diem)
    {
        $diaDiem = DiaDiem::find($ma_dia_diem);
        if (!$diaDiem) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy địa điểm',
            ], 404);
        }

        if ($diaDiem->nguon_tao !== 'doi_tac') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ từ chối địa điểm được tạo bởi đối tác',
            ], 422);
        }

        if ($diaDiem->trang_thai_duyet !== 'pending_approval') {
            return response()->json([
                'success' => false,
                'message' => 'Địa điểm không ở trạng thái chờ duyệt',
            ], 422);
        }

        $diaDiem->update([
            'trang_thai_duyet' => 'rejected',
            'ly_do_tu_choi' => $request->ly_do_tu_choi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Từ chối địa điểm thành công',
            'data' => $diaDiem->fresh(),
        ], 200);
    }
}
