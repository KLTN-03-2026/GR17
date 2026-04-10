<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CauHinhNgay extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    protected $table = 'cau_hinh_ngay';
    protected $primaryKey = 'ma_cau_hinh_ngay';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ma_cau_hinh_ngay',
        'loai_ngay_le',
        'ten_ngay_le',
        'ngay',
    ];

    protected $casts = [
        'loai_ngay_le' => 'integer',
        'ngay' => 'date',
    ];
}
