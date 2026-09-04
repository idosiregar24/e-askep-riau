<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CareSession extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'uuid',
        'student_id',
        'course_id',
        'mentor_dosen_id',
        'patient_name',
        'medical_record_no',
        'age',
        'gender',
        'triage_category',
        'status',
        'submitted_at',
        'approved_at',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'approved_at'  => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_dosen_id');
    }

    public function assessment(): HasOne
    {
        return $this->hasOne(CareSessionAssessment::class);
    }

    public function carePlans(): HasMany
    {
        return $this->hasMany(NursingCarePlan::class)->orderBy('priority_order');
    }

    public function procedureLogs(): HasMany
    {
        return $this->hasMany(CareProcedureLog::class);
    }

    public function vitalSigns(): HasMany
    {
        return $this->hasMany(VitalSignMonitoring::class)->orderBy('recorded_at');
    }

    public function evaluationsAndHandovers(): HasMany
    {
        return $this->hasMany(EvaluationAndHandover::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(SessionReview::class);
    }
}
