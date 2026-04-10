<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhanQuyenAdmin extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    protected $table = 'phan_quyen_admin';
    protected $primaryKey = 'ma_phan_quyen';
    public $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'ma_phan_quyen',
        'ma_chuc_nang',
        'ma_chuc_vu',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function chucNang()
    {
        return $this->belongsTo(ChucNang::class, 'ma_chuc_nang');
    }

    public function chucVu()
    {
        return $this->belongsTo(ChucVu::class, 'ma_chuc_vu');
    }
}
