<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_type_id',
        'personnel_id',
        'guest_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'actual_check_in',
        'actual_check_out',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'actual_check_in' => 'datetime',
            'actual_check_out' => 'datetime',
        ];
    }

    /**
     * Get admission type
     */
    public function admissionType()
    {
        return $this->belongsTo(AdmissionType::class);
    }

    /**
     * Get personnel
     */
    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    /**
     * Get guest
     */
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    /**
     * Get room
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get beds for this reservation
     */
    public function beds()
    {
        return $this->belongsToMany(Bed::class, 'reservation_beds');
    }

    /**
     * Get meals for this reservation
     */
    public function meals()
    {
        return $this->hasMany(Meal::class);
    }

    /**
     * Get creator user
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get guest name (personnel or guest)
     */
    public function getGuestNameAttribute(): string
    {
        if ($this->personnel) {
            return $this->personnel->full_name;
        } elseif ($this->guest) {
            return $this->guest->full_name;
        }
        return 'نامشخص';
    }

    /**
     * Check if checked in
     */
    public function isCheckedIn(): bool
    {
        return $this->status === 'checked_in';
    }

    /**
     * Check if checked out
     */
    public function isCheckedOut(): bool
    {
        return $this->status === 'checked_out';
    }

    /**
     * Scope by status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope active reservations (checked in)
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'checked_in');
    }
}
