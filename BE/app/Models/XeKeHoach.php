<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XeKeHoach extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    protected $table = 'xe_ke_hoach';
    protected $primaryKey = 'ma_xe_ke_hoach';
    public $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'ma_xe_ke_hoach',
        'ma_ke_hoach',
        'ma_xe',
        'so_luong',
        'so_ngay',
        'tong_tien',
    ];

    protected $casts = [
        'so_luong' => 'integer',
        'so_ngay' => 'integer',
        'tong_tien' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function keHoach()
    {
        return $this->belongsTo(KeHoach::class, 'ma_ke_hoach');
    }

    public function xe()
    {
        return $this->belongsTo(Xe::class, 'ma_xe');
    }
}
