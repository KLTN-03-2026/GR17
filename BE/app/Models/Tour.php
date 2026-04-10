<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    protected $table = 'tours';
    protected $primaryKey = 'ma_tour';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ma_tour',
        'ma_doi_tac',
        'ten_tour',
        'mo_ta',
        'hinh_anh',
        'so_tien',
        'so_ngay',
        'so_nguoi',
        'ma_tag',
        'nguon_tao',
        'trang_thai_duyet',
        'trang_thai_hien_thi',
        'ly_do_tu_choi',
    ];

    protected $casts = [
        'trang_thai_hien_thi' => 'boolean',
    ];

    public function doiTac()
    {
        return $this->belongsTo(DoiTac::class, 'ma_doi_tac', 'ma_doi_tac');
    }

    public function chiTietTours()
    {
        return $this->hasMany(ChiTietTour::class, 'ma_tour', 'ma_tour')
            ->orderBy('ngay_hanh_trinh')
            ->orderBy('thu_tu_hanh_trinh');
    }

    public function scopePubliclyVisible($query)
    {
        return $query
            ->where('trang_thai_duyet', 'approved')
            ->where('trang_thai_hien_thi', true);
    }
}
