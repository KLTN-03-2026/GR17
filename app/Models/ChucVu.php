<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChucVu extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    protected $table = 'chuc_vu';
    protected $primaryKey = 'ma_chuc_vu';
    public $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'ten_chuc_vu',
        'tinh_trang',
    ];

    protected $casts = [
        'tinh_trang' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
