<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ward extends Model
{
    public const TYPES = [
        'general' => 'General',
        'private' => 'Private',
        'icu' => 'ICU',
        'maternity' => 'Maternity',
        'pediatric' => 'Pediatric',
        'surgical' => 'Surgical',
        'emergency' => 'Emergency',
    ];

    protected $fillable = [
        'department_id',
        'name',
        'type',
        'capacity',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    /** Patients currently occupying a bed in this ward. */
    public function activeAdmissions(): HasMany
    {
        return $this->hasMany(Admission::class)->where('status', Admission::STATUS_ADMITTED);
    }

    /**
     * Current occupancy. Uses the eager-loaded "active_admissions_count" when available
     * (via withCount('activeAdmissions')) to avoid extra queries.
     */
    public function getCurrentOccupancyAttribute(): int
    {
        if (array_key_exists('active_admissions_count', $this->attributes)) {
            return (int) $this->attributes['active_admissions_count'];
        }

        return $this->activeAdmissions()->count();
    }

    public function getAvailableBedsAttribute(): int
    {
        return max(0, $this->capacity - $this->current_occupancy);
    }

    public function getOccupancyPercentAttribute(): int
    {
        if ($this->capacity <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->current_occupancy / $this->capacity) * 100));
    }

    public function getOccupancyColorAttribute(): string
    {
        $percent = $this->occupancy_percent;

        return match (true) {
            $percent >= 90 => 'danger',
            $percent >= 70 => 'warning',
            default => 'success',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst((string) $this->type);
    }

    public function isFull(): bool
    {
        return $this->current_occupancy >= $this->capacity;
    }
}
