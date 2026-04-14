<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiaoDichQr extends Model
{
    use HasFactory;

    protected $table = 'giao_dich_qr';
    protected $primaryKey = 'ma_giao_dich_qr';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ma_giao_dich_qr',
        'ma_hoa_don',
        'provider',
        'so_tien',
        'noi_dung_chuyen_khoan',
        'qr_url',
        'qr_payload',
        'trang_thai',
        'webhook_transaction_id',
        'reference_code',
        'webhook_payload',
        'expires_at',
        'paid_at',
    ];

    protected $casts = [
        'so_tien' => 'decimal:2',
        'qr_payload' => 'array',
        'webhook_payload' => 'array',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function hoaDon()
    {
        return $this->belongsTo(HoaDon::class, 'ma_hoa_don', 'ma_hoa_don');
    }
}
