<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'bed_id',
        'description',
        'priority',
        'status',
        'reported_by',
        'assigned_to',
        'started_at',
        'completed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get room
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get bed
     */
    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }

    /**
     * Get reporter user
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Get assigned user
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope by priority
     */
    public function scopePriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }
}
