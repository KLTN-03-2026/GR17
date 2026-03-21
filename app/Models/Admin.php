<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use \App\Traits\GeneratesIdFromZero;
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'admins';

    protected $primaryKey = 'Ma_admin';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'Ma_admin',
        'Ho_va_ten',
        'Mat_khau',
        'Email',
        'Ngay_sinh',
        'Gioi_tinh',
        'ma_chuc_vu',
        'is_block',
        'hash_reset',
        'so_dien_thoai',
    ];
}
