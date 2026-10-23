<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Golongan extends Model
{
    protected $fillable = ['kode', 'nama', 'masa_manfaat', 'persen', 'is_active'];

    protected $casts = [
        'masa_manfaat' => 'integer',
        'persen'       => 'decimal:2',
        'is_active'    => 'boolean',
    ];

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}