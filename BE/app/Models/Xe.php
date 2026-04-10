<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Xe extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    protected $table = 'xe';
    protected $primaryKey = 'ma_xe';
    public $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'ma_xe',
        'ten_xe',
        'loai_xe',
        'so_cho',
        'gia_theo_ngay',
        'tong_so_xe',
        'mo_ta',
        'trang_thai',
    ];

    protected $casts = [
        'so_cho' => 'integer',
        'gia_theo_ngay' => 'decimal:2',
        'tong_so_xe' => 'integer',
        'trang_thai' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
