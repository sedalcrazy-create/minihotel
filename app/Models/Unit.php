<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'number',
        'section',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get building that owns this unit
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Get rooms for this unit
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Scope active units
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by section
     */
    public function scopeSection($query, string $section)
    {
        return $query->where('section', $section);
    }
}
