<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'vouchers';

    protected $fillable = [
        'ma_voucher',
        'ma_doi_tac',
        'ten_voucher',
        'loai_giam_gia',
        'gia_tri_giam',
        'giam_toi_da',
        'don_toi_thieu',
        'so_luong',
        'da_su_dung',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'trang_thai',
    ];

    protected $casts = [
        'gia_tri_giam' => 'decimal:2',
        'giam_toi_da' => 'decimal:2',
        'don_toi_thieu' => 'decimal:2',
        'ngay_bat_dau' => 'datetime',
        'ngay_ket_thuc' => 'datetime',
        'trang_thai' => 'boolean',
    ];

    public function doiTac()
    {
        return $this->belongsTo(DoiTac::class, 'ma_doi_tac', 'Ma_doi_tac');
    }
}
