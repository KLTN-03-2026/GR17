<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeHoach extends Model
{
    use HasFactory;

    protected $table = 'ke_hoach';
    protected $primaryKey = 'id_ke_hoach';
    public $timestamps = true;

    protected $fillable = [
        'ten_ke_hoach',
        'mo_ta',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'tong_chi_phi',
        'trang_thai',
    ];

    protected $casts = [
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
        'tong_chi_phi' => 'decimal:2',
        'trang_thai' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function xeKeHoach()
    {
        return $this->hasMany(XeKeHoach::class, 'id_ke_hoach');
    }
}
