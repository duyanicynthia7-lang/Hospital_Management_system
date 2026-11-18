<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'department_id',
        'name',
        'specialization',
        'license_number',
        'phone',
        'email',
        'schedule',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    /** Departments this doctor is head of. */
    public function headedDepartments(): HasMany
    {
        return $this->hasMany(Department::class, 'head_doctor_id');
    }

    public function getDisplayNameAttribute(): string
    {
        $name = $this->name;

        return preg_match('/^dr\.?\s/i', $name) ? $name : 'Dr. '.$name;
    }
}
