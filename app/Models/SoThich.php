<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoThich extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    use HasFactory;

    protected $table = 'so_thich';
    protected $primaryKey = 'ma_so_thich';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ma_so_thich',
        'ma_khach_hang',
        'ma_tag',
        'muc_do',
    ];
}
