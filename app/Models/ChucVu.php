<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChucVu extends Model
{
    use HasFactory;

    protected $table = 'chuc_vu';
    protected $primaryKey = 'id_chuc_vu';
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
