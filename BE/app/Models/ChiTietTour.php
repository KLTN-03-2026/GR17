<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietTour extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    protected $table = 'chi_tiet_tours';
    protected $primaryKey = 'ma_chi_tiet_tour';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ma_chi_tiet_tour',
        'ma_tour',
        'ma_dia_diem',
    ];

    public function diaDiem()
    {
        return $this->belongsTo(DiaDiem::class, 'ma_dia_diem', 'ma_dia_diem');
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'ma_tour', 'ma_tour');
    }
}
