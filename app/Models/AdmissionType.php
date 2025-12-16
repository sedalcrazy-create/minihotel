<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'has_reservation',
        'reservation_days_before',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'has_reservation' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get reservations for this admission type
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Scope active admission types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
