<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoatDongChiTiet extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    protected $table = 'hoat_dong_chi_tiet';
    protected $primaryKey = 'ma_hoat_dong_chi_tiet';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ma_hoat_dong_chi_tiet',
        'ma_ke_hoach',
        'ma_nhom',
        'ma_dia_diem',
        'gio_bat_dau',
        'gio_ket_thuc',
        'ngay_cu_the',
    ];

    public function keHoach()
    {
        return $this->belongsTo(KeHoach::class, 'ma_ke_hoach', 'ma_ke_hoach');
    }

    public function nhom()
    {
        return $this->belongsTo(Nhom::class, 'ma_nhom', 'Ma_nhom');
    }

    public function diaDiem()
    {
        return $this->belongsTo(DiaDiem::class, 'ma_dia_diem', 'ma_dia_diem');
    }
}
