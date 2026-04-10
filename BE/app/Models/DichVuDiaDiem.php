<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DichVuDiaDiem extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    protected $table = 'dich_vu_dia_diem';
    protected $primaryKey = 'ma_dich_vu';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'ma_dich_vu',
        'ma_dia_diem',
        'ten_dich_vu',
        'mo_ta',
        'so_nguoi_toi_da',
        'gia',
        'trang_thai',
    ];

    protected $casts = [
        'gia' => 'decimal:2',
        'so_nguoi_toi_da' => 'integer',
        'trang_thai' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the location that owns this service.
     */
    public function diaDiem(): BelongsTo
    {
        return $this->belongsTo(DiaDiem::class, 'ma_dia_diem', 'ma_dia_diem');
    }
}
