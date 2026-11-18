<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admission extends Model
{
    public const STATUS_ADMITTED = 'admitted';
    public const STATUS_DISCHARGED = 'discharged';

    public const STATUSES = [
        self::STATUS_ADMITTED => 'Admitted',
        self::STATUS_DISCHARGED => 'Discharged',
    ];

    protected $fillable = [
        'patient_id',
        'ward_id',
        'doctor_id',
        'admitted_at',
        'discharged_at',
        'diagnosis',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'admitted_at' => 'datetime',
            'discharged_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function isAdmitted(): bool
    {
        return $this->status === self::STATUS_ADMITTED;
    }

    /** Length of stay in days (up to now if still admitted). */
    public function getLengthOfStayAttribute(): int
    {
        $end = $this->discharged_at ?? now();

        return max(0, (int) $this->admitted_at?->startOfDay()->diffInDays($end->copy()->startOfDay()));
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status === self::STATUS_ADMITTED ? 'danger' : 'success';
    }
}
