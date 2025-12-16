<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'number',
        'status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get room that owns this bed
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get reservations for this bed
     */
    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_beds');
    }

    /**
     * Get cleaning logs for this bed
     */
    public function cleaningLogs()
    {
        return $this->hasMany(CleaningLog::class);
    }

    /**
     * Get maintenance requests for this bed
     */
    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    /**
     * Check if bed is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->is_active;
    }

    /**
     * Check if bed needs cleaning
     */
    public function needsCleaning(): bool
    {
        return $this->status === 'needs_cleaning';
    }

    /**
     * Check if bed is under maintenance
     */
    public function isUnderMaintenance(): bool
    {
        return $this->status === 'under_maintenance';
    }

    /**
     * Scope available beds
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_active', true);
    }

    /**
     * Scope by status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get full bed identifier (Unit-Room-Bed)
     */
    public function getIdentifierAttribute(): string
    {
        $room = $this->room;
        $unit = $room->unit;
        return sprintf('واحد %d - اتاق %d - تخت %d', $unit->number, $room->number, $this->number);
    }
}
