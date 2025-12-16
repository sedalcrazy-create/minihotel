<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    use HasFactory;

    protected $table = 'personnel';

    protected $fillable = [
        'employment_code',
        'first_name',
        'last_name',
        'birth_year',
        'birth_month',
        'birth_day',
        'national_code',
        'father_name',
        'relation',
        'account_number',
        'service_location_code',
        'service_location',
        'department_code',
        'department',
        'employment_status',
        'main_or_branch',
        'funkefalat',
        'partner_employment_status',
        'gender',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get birth date in Jalali format
     */
    public function getBirthDateAttribute(): string
    {
        return sprintf('%d/%02d/%02d', $this->birth_year, $this->birth_month, $this->birth_day);
    }

    /**
     * Scope to get only active personnel
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get by employment status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('employment_status', $status);
    }

    /**
     * Get reservations for this personnel
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
