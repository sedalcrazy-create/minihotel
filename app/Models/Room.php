<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'number',
        'capacity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get unit that owns this room
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get beds for this room
     */
    public function beds()
    {
        return $this->hasMany(Bed::class);
    }

    /**
     * Get reservations for this room
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get room status based on occupied beds
     */
    public function getStatusAttribute(): string
    {
        $occupiedCount = $this->beds()->where('status', 'occupied')->count();

        if ($occupiedCount === 0) {
            return 'خالی';
        } elseif ($occupiedCount < $this->capacity) {
            return 'نیمه‌پر';
        } else {
            return 'اشغال';
        }
    }

    /**
     * Get available beds count
     */
    public function getAvailableBedsCountAttribute(): int
    {
        return $this->beds()->where('status', 'available')->count();
    }

    /**
     * Scope active rooms
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
