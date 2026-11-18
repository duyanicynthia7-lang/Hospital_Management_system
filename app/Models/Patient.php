<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends Model
{
    public const GENDERS = [
        'male' => 'Male',
        'female' => 'Female',
        'other' => 'Other',
    ];

    public const BLOOD_TYPES = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

    protected $fillable = [
        'patient_code',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'blood_type',
        'phone',
        'email',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'allergies',
        'medical_notes',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    /**
     * Automatically generate a readable patient code (e.g. PAT-00001) after creation.
     */
    protected static function booted(): void
    {
        static::created(function (Patient $patient) {
            if (empty($patient->patient_code)) {
                $patient->patient_code = 'PAT-'.str_pad((string) $patient->id, 5, '0', STR_PAD_LEFT);
                $patient->saveQuietly();
            }
        });
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function currentAdmission(): HasOne
    {
        return $this->hasOne(Admission::class)
            ->where('status', Admission::STATUS_ADMITTED)
            ->latestOfMany('admitted_at');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    public function getGenderLabelAttribute(): string
    {
        return self::GENDERS[$this->gender] ?? ucfirst((string) $this->gender);
    }

    /**
     * Search by name, code, phone or email.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $like = '%'.$term.'%';
            $q->where('first_name', 'like', $like)
                ->orWhere('last_name', 'like', $like)
                ->orWhere('patient_code', 'like', $like)
                ->orWhere('phone', 'like', $like)
                ->orWhere('email', 'like', $like);

            // Support searching "John Doe" across both name columns
            $parts = preg_split('/\s+/', $term);
            if (count($parts) >= 2) {
                $q->orWhere(function (Builder $q2) use ($parts) {
                    $q2->where('first_name', 'like', '%'.$parts[0].'%')
                        ->where('last_name', 'like', '%'.$parts[count($parts) - 1].'%');
                });
            }
        });
    }
}
