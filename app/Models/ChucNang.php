<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChucNang extends Model
{
    use HasFactory;

    protected $table = 'chuc_nang';
    protected $primaryKey = 'id_chuc_nang';
    public $timestamps = true;

    protected $fillable = [
        'ten_chuc_nang',
        'mo_ta',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function phanQuyen()
    {
        return $this->hasMany(PhanQuyenAdmin::class, 'id_chuc_nang');
    }
}
