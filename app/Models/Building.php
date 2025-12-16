<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get units for this building
     */
    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * Scope active buildings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
