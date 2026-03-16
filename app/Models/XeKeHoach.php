<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XeKeHoach extends Model
{
    use HasFactory;

    protected $table = 'xe_ke_hoach';
    protected $primaryKey = 'id_xe_ke_hoach';
    public $timestamps = true;

    protected $fillable = [
        'id_ke_hoach',
        'id_xe',
        'so_luong',
        'so_ngay',
        'tong_tien',
    ];

    protected $casts = [
        'id_ke_hoach' => 'integer',
        'id_xe' => 'integer',
        'so_luong' => 'integer',
        'so_ngay' => 'integer',
        'tong_tien' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function keHoach()
    {
        return $this->belongsTo(KeHoach::class, 'id_ke_hoach');
    }

    public function xe()
    {
        return $this->belongsTo(Xe::class, 'id_xe');
    }
}
