<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhSachYeuThich extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    protected $table = 'danh_sach_yeu_thich';
    protected $primaryKey = 'ma_danh_sach_ua_thich';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ma_danh_sach_ua_thich',
        'ma_khach_hang',
        'ma_dia_diem'
    ];

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'ma_khach_hang', 'Ma_khach_hang');
    }

    public function diaDiem()
    {
        return $this->belongsTo(DiaDiem::class, 'ma_dia_diem', 'ma_dia_diem');
    }
}
