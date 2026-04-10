<?php

namespace App\Services;

use App\Models\DiaDiem;
use Illuminate\Support\Str;

class DiaDiemNormalizationService
{
    public function normalize(?string $value): string
    {
        $value = trim((string) $value);
        $value = mb_strtolower($value, 'UTF-8');
        $value = Str::ascii($value);
        $value = preg_replace('/[^a-z0-9\s]/', ' ', $value) ?? '';
        $value = preg_replace('/\s+/', ' ', $value) ?? '';

        return trim($value);
    }

    public function normalizedPayload(string $tenDiaDiem, string $diaChi): array
    {
        return [
            'ten_dia_diem_normalized' => $this->normalize($tenDiaDiem),
            'dia_chi_normalized' => $this->normalize($diaChi),
        ];
    }

    public function findDuplicate(string $tenDiaDiem, string $diaChi): ?DiaDiem
    {
        $normalized = $this->normalizedPayload($tenDiaDiem, $diaChi);

        return DiaDiem::query()
            ->where('ten_dia_diem_normalized', $normalized['ten_dia_diem_normalized'])
            ->where('dia_chi_normalized', $normalized['dia_chi_normalized'])
            ->first();
    }
}
