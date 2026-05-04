<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    public const TYPE_TOUR = 0;
    public const TYPE_PLAN = 1;
    public const STATUS_UNPAID = 0;
    public const STATUS_PAID = 1;
    public const STATUS_PENDING = 2;

    protected $table = 'hoa_don';
    protected $primaryKey = 'ma_hoa_don';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ma_hoa_don',
        'ma_nhom',
        'loai_hoa_don',
        'ma_doi_tuong',
        'tong_tien',
        'trang_thai_thanh_toan',
        'ma_giao_dich',
        'ngay_tao',
    ];

    protected $casts = [
        'tong_tien' => 'decimal:2',
        'loai_hoa_don' => 'integer',
        'trang_thai_thanh_toan' => 'integer',
        'ngay_tao' => 'datetime',
    ];

    public function nhom()
    {
        return $this->belongsTo(Nhom::class, 'ma_nhom', 'Ma_nhom');
    }

    public function scopeForGroup($query, string $maNhom)
    {
        return $query->where('ma_nhom', $maNhom);
    }

    public function scopePaid($query)
    {
        return $query->where('trang_thai_thanh_toan', self::STATUS_PAID);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('trang_thai_thanh_toan', self::STATUS_UNPAID);
    }

    public function getTrangThaiThanhToanLabelAttribute(): string
    {
        return match ($this->trang_thai_thanh_toan) {
            self::STATUS_PAID => 'Da thanh toan',
            self::STATUS_PENDING => 'Cho xac nhan',
            default => 'Chua thanh toan',
        };
    }
}
