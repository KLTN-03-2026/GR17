<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    protected $table = 'hoa_don';
    protected $primaryKey = 'ma_hoa_don';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ma_hoa_don',
        'ma_nhom',
        'loai_hoa_don',
        'ma_doi_tuong',
        'tong_tien',
        'trang_thai_thanh_toan',
        'ma_giao_dich',
        'ngay_tao',
    ];

    protected $casts = [
        'tong_tien' => 'decimal:2',
        'loai_hoa_don' => 'integer',
        'trang_thai_thanh_toan' => 'integer',
        'ngay_tao' => 'datetime',
    ];

    public function nhom()
    {
        return $this->belongsTo(Nhom::class, 'ma_nhom', 'Ma_nhom');
    }
}
