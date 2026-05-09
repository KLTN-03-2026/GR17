<?php

namespace App\Services;

use App\Models\HoaDon;
use Carbon\Carbon;

class InvoiceCodeGenerator
{
    public function generate(?Carbon $now = null): string
    {
        $current = ($now ?? now())->copy()->timezone('Asia/Ho_Chi_Minh');
        $prefix = 'HD' . $current->format('ym');

        $latestCode = HoaDon::query()
            ->where('ma_hoa_don', 'like', $prefix . '%')
            ->orderByDesc('ma_hoa_don')
            ->value('ma_hoa_don');

        $sequence = 1;
        if ($latestCode && str_starts_with($latestCode, $prefix)) {
            $sequence = ((int) substr($latestCode, -4)) + 1;
        }

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
