<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhGiaKeHoach extends Model
{
    use HasFactory;

    protected $table = 'danh_gia_ke_hoach';
    protected $primaryKey = 'Ma_danh_gia';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'Ma_danh_gia',
        'Ma_khach_hang',
        'ma_dia_diem',
        'so_sao',
        'noi_dung',
    ];

    protected $casts = [
        'so_sao' => 'integer',
    ];

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'Ma_khach_hang', 'Ma_khach_hang');
    }

    public function diaDiem()
    {
        return $this->belongsTo(DiaDiem::class, 'ma_dia_diem', 'ma_dia_diem');
    }
}
