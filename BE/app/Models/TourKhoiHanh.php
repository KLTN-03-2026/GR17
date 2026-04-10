<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourKhoiHanh extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    protected $table = 'tour_khoi_hanhs';
    protected $primaryKey = 'ma_thoi_gian_tour';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ma_thoi_gian_tour',
        'ma_tour',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'so_cho',
        'tinh_trang'
    ];
}
