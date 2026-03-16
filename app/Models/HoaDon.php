<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    use HasFactory;

    protected $table = 'hoa_don';
    protected $primaryKey = 'Ma_hoa_don';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'Ma_hoa_don',
        'Ma_khach_hang',
        'Ma_nhom',
        'ma_dia_diem',
        'tong_tien',
        'trang_thai',
        'ngay_dat',
    ];

    protected $casts = [
        'tong_tien'   => 'decimal:2',
        'trang_thai'  => 'integer',
        'ngay_dat'    => 'datetime',
    ];

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'Ma_khach_hang', 'Ma_khach_hang');
    }

    public function nhom()
    {
        return $this->belongsTo(Nhom::class, 'Ma_nhom', 'Ma_nhom');
    }

    public function diaDiem()
    {
        return $this->belongsTo(DiaDiem::class, 'ma_dia_diem', 'ma_dia_diem');
    }
}
