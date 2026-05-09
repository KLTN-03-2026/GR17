<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhanHoi extends Model
{
    use HasFactory;

    protected $fillable = [
        'ma_khach_hang',
        'tieu_de',
        'loai',
        'mo_ta',
        'url_trang_loi',
        'trang_thai',
    ];

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'ma_khach_hang');
    }
}
