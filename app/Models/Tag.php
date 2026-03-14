<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    protected $table = 'tag';
    protected $primaryKey = 'ma_tag';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'ma_tag',
        'ten_tag',
    ];

    /**
     * Get all tag locations for this tag.
     */
    public function tagDiaDiems(): HasMany
    {
        return $this->hasMany(TagDiaDiem::class, 'ma_tag', 'ma_tag');
    }
}
