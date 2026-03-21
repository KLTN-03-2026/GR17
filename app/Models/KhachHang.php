<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class KhachHang extends Authenticatable
{
    use \App\Traits\GeneratesIdFromZero;
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'khach_hang';
    protected $primaryKey = 'Ma_khach_hang';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'Ma_khach_hang',
        'Ho_va_ten',
        'Mat_khau',
        'Email',
        'Ngay_sinh',
        'Gioi_tinh',
        'so_dien_thoai',
        'is_block',
        'hash_reset',
    ];

    protected $hidden = [
        'Mat_khau',
        'hash_reset',
    ];

    protected $casts = [
        'Ngay_sinh' => 'date',
        'Gioi_tinh' => 'boolean',
        'is_block' => 'boolean',
    ];
}
