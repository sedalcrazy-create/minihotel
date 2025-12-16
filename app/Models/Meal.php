<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'date',
        'breakfast',
        'lunch',
        'dinner',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'breakfast' => 'boolean',
            'lunch' => 'boolean',
            'dinner' => 'boolean',
        ];
    }

    /**
     * Get reservation
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get total meals count
     */
    public function getTotalMealsAttribute(): int
    {
        return ($this->breakfast ? 1 : 0) + ($this->lunch ? 1 : 0) + ($this->dinner ? 1 : 0);
    }
}
