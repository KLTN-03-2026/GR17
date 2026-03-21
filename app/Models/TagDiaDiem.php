<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagDiaDiem extends Model
{
    use \App\Traits\GeneratesIdFromZero;
    protected $table = 'tag_dia_diem';
    protected $primaryKey = 'ma_tag_dia_diem';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'ma_tag_dia_diem',
        'ma_dia_diem',
        'ma_tag',
    ];

    /**
     * Get the location for this tag.
     */
    public function diaDiem(): BelongsTo
    {
        return $this->belongsTo(DiaDiem::class, 'ma_dia_diem', 'ma_dia_diem');
    }

    /**
     * Get the tag for this location.
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'ma_tag', 'ma_tag');
    }
}
