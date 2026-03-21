<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KeHoach extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    protected $table = 'ke_hoach';
    protected $primaryKey = 'ma_ke_hoach';
    public $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'ma_ke_hoach',
        'ma_nhom',
        'ten_ke_hoach',
        'so_nguoi',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'ngan_sach_du_kien',
        'trang_thai',
    ];

    protected $casts = [
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
        'ngan_sach_du_kien' => 'decimal:2',
        'so_nguoi' => 'integer',
        'trang_thai' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the group that owns this plan.
     */
    public function nhom(): BelongsTo
    {
        return $this->belongsTo(Nhom::class, 'ma_nhom', 'Ma_nhom');
    }

    public function xeKeHoach(): HasMany
    {
        return $this->hasMany(XeKeHoach::class, 'ma_ke_hoach');
    }
}
