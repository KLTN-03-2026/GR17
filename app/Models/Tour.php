<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    protected $table = 'tours';
    protected $primaryKey = 'ma_tour';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ma_tour',
        'ten_tour',
        'mo_ta',
        'hinh_anh',
        'so_tien',
        'so_ngay',
        'so_nguoi',
        'ma_tag'
    ];
}
