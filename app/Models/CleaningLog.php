<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CleaningLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'bed_id',
        'cleaned_at',
        'type',
        'cleaned_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'cleaned_at' => 'datetime',
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
     * Get cleaner user
     */
    public function cleaner()
    {
        return $this->belongsTo(User::class, 'cleaned_by');
    }
}
