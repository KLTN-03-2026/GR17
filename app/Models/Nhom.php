<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nhom extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    protected $table = 'nhom';
    protected $primaryKey = 'Ma_nhom';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'Ma_nhom',
        'ten_nhom',
    ];

    public function thanhVienNhom()
    {
        return $this->hasMany(ThanhVienNhom::class, 'Ma_nhom', 'Ma_nhom');
    }

    public function hoaDon()
    {
        return $this->hasMany(HoaDon::class, 'Ma_nhom', 'Ma_nhom');
    }
}
