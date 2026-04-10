<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiaDiem extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    protected $table = 'dia_diem';
    protected $primaryKey = 'ma_dia_diem';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'ma_dia_diem',
        'ten_dia_diem',
        'loai',
        'dia_chi',
        'sdt',
        'kinh_do',
        'vi_do',
        'gio_mo_cua',
        'gio_dong_cua',
        'gia_giao_dong',
        'hinh_anh',
        'mo_ta',
        'thoi_gian_tham_quan',
    ];

    protected $casts = [
        'kinh_do' => 'decimal:7',
        'vi_do' => 'decimal:7',
        'gia_giao_dong' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all tags for this location.
     */
    public function tagDiaDiems(): HasMany
    {
        return $this->hasMany(TagDiaDiem::class, 'ma_dia_diem', 'ma_dia_diem');
    }

    /**
     * Get all tags through the junction table.
     */
    public function tags()
    {
        return $this->hasManyThrough(Tag::class, TagDiaDiem::class, 'ma_dia_diem', 'ma_tag', 'ma_dia_diem', 'ma_tag');
    }

    public function dichVuDiaDiems(): HasMany
    {
        return $this->hasMany(DichVuDiaDiem::class, 'ma_dia_diem', 'ma_dia_diem');
    }
}
