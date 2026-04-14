<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    protected $table = 'hoa_don';
    protected $primaryKey = 'ma_hoa_don';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ma_hoa_don',
        'ma_nhom',
        'ma_khach_hang_dat',
        'loai_hoa_don',
        'ma_doi_tuong',
        'ma_thoi_gian_tour',
        'tong_tien',
        'trang_thai_thanh_toan',
        'payment_method',
        'payment_status',
        'payment_reference',
        'ma_giao_dich',
        'ten_nguoi_dat',
        'email_nguoi_dat',
        'so_dien_thoai_nguoi_dat',
        'dia_chi_nguoi_dat',
        'ngay_tao',
        'paid_at',
    ];

    protected $casts = [
        'tong_tien' => 'decimal:2',
        'loai_hoa_don' => 'integer',
        'trang_thai_thanh_toan' => 'integer',
        'ngay_tao' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function nhom()
    {
        return $this->belongsTo(Nhom::class, 'ma_nhom', 'Ma_nhom');
    }

    public function khachHangDat()
    {
        return $this->belongsTo(KhachHang::class, 'ma_khach_hang_dat', 'Ma_khach_hang');
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'ma_doi_tuong', 'ma_tour');
    }

    public function tourKhoiHanh()
    {
        return $this->belongsTo(TourKhoiHanh::class, 'ma_thoi_gian_tour', 'ma_thoi_gian_tour');
    }

    public function giaoDichQrs()
    {
        return $this->hasMany(GiaoDichQr::class, 'ma_hoa_don', 'ma_hoa_don');
    }

    public function latestQrPayment()
    {
        return $this->hasOne(GiaoDichQr::class, 'ma_hoa_don', 'ma_hoa_don')->latestOfMany('created_at');
    }

    public function latestPendingQrPayment()
    {
        return $this->hasOne(GiaoDichQr::class, 'ma_hoa_don', 'ma_hoa_don')
            ->where('trang_thai', 'pending')
            ->latestOfMany('created_at');
    }
}
