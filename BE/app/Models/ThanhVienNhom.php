<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanhVienNhom extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    protected $table = 'thanh_vien_nhom';
    protected $primaryKey = 'Ma_thanh_vien';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'Ma_thanh_vien',
        'Ma_nhom',
        'Ma_khach_hang',
        'vai_tro',
    ];

    protected $casts = [
        'vai_tro' => 'integer',
    ];

    public function nhom()
    {
        return $this->belongsTo(Nhom::class, 'Ma_nhom', 'Ma_nhom');
    }

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'Ma_khach_hang', 'Ma_khach_hang');
    }
}
