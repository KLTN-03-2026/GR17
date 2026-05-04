<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CauHinhAi extends Model
{
    use HasFactory;

    protected $table = 'cau_hinh_ais';

    protected $fillable = [
        'loai',
        'noi_dung'
    ];
}
