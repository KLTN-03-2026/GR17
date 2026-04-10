<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaoCaoDoiTacThang extends Model
{
    use HasFactory;

    protected $table = 'bao_cao_doi_tac_thang';

    protected $fillable = [
        'ma_doi_tac',
        'thang_bao_cao',
        'trang_thai',
        'so_lan_thu',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function doiTac()
    {
        return $this->belongsTo(DoiTac::class, 'ma_doi_tac', 'ma_doi_tac');
    }
}
