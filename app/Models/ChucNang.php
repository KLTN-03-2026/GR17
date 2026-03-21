<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChucNang extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    protected $table = 'chuc_nang';
    protected $primaryKey = 'ma_chuc_nang';
    public $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'ma_chuc_nang',
        'ten_chuc_nang',
        'mo_ta',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function phanQuyen()
    {
        return $this->hasMany(PhanQuyenAdmin::class, 'ma_chuc_nang');
    }
}
