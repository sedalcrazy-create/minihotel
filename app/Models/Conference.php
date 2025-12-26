<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conference extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'start_date',
        'end_date',
        'organizer',
        'capacity',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * کاربر ایجادکننده
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * رزروهای این همایش
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * همایش‌های فعال
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * همایش‌های 45 روز آینده (قابل انتخاب برای پذیرش)
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now())
                     ->where('start_date', '<=', now()->addDays(45));
    }

    /**
     * همایش‌های در حال برگزاری
     */
    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }

    /**
     * تعداد روزهای همایش
     */
    public function getDurationAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * وضعیت همایش
     */
    public function getStatusAttribute(): string
    {
        if ($this->end_date->lt(now())) {
            return 'finished';
        }
        if ($this->start_date->lte(now()) && $this->end_date->gte(now())) {
            return 'ongoing';
        }
        return 'upcoming';
    }

    /**
     * وضعیت فارسی
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'finished' => 'پایان یافته',
            'ongoing' => 'در حال برگزاری',
            'upcoming' => 'آینده',
        };
    }
}
