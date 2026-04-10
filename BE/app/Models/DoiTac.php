<?php

namespace App\Models;

use App\Traits\GeneratesIdFromZero;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class DoiTac extends Authenticatable
{
    use GeneratesIdFromZero;
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $table = 'doi_tac';
    protected $primaryKey = 'ma_doi_tac';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ma_doi_tac',
        'ten_doi_tac',
        'ten_nguoi_dai_dien',
        'email',
        'mat_khau',
        'so_dien_thoai',
        'dia_chi',
        'is_block',
        'trang_thai_duyet',
        'ly_do_tu_choi',
    ];

    protected $hidden = [
        'mat_khau',
        'remember_token',
    ];

    protected $casts = [
        'is_block' => 'boolean',
    ];

    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    public function diaDiems()
    {
        return $this->hasMany(DiaDiem::class, 'ma_doi_tac_tao', 'ma_doi_tac');
    }

    public function tours()
    {
        return $this->hasMany(Tour::class, 'ma_doi_tac', 'ma_doi_tac');
    }
}
