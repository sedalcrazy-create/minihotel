<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'national_code',
        'phone',
        'email',
        'reason',
        'organization',
    ];

    /**
     * Get reservations for this guest
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
