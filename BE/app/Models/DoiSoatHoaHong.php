<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoiSoatHoaHong extends Model
{
    use HasFactory;

    protected $table = 'doi_soat_hoa_hong';
    protected $primaryKey = 'ma_doi_soat';

    protected $fillable = [
        'ma_hoa_don',
        'ma_doi_tac',
        'loai_giao_dich',
        'tong_tien_giao_dich',
        'phan_tram_hoa_hong',
        'tien_hoa_hong_admin',
        'tien_doi_tac_thuc_nhan',
        'trang_thai_thanh_toan',
        'mo_ta',
        'created_at',
        'updated_at',
    ];

    public function hoaDon()
    {
        return $this->belongsTo(HoaDon::class, 'ma_hoa_don', 'ma_hoa_don');
    }

    public function doiTac()
    {
        return $this->belongsTo(DoiTac::class, 'ma_doi_tac', 'ma_doi_tac');
    }
}
