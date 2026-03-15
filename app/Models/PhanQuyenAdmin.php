<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhanQuyenAdmin extends Model
{
    use HasFactory;

    protected $table = 'phan_quyen_admin';
    protected $primaryKey = 'id_phan_quyen';
    public $timestamps = true;

    protected $fillable = [
        'id_chuc_nang',
        'id_chuc_vu',
    ];

    protected $casts = [
        'id_chuc_nang' => 'integer',
        'id_chuc_vu' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function chucNang()
    {
        return $this->belongsTo(ChucNang::class, 'id_chuc_nang');
    }

    public function chucVu()
    {
        return $this->belongsTo(ChucVu::class, 'id_chuc_vu');
    }
}
