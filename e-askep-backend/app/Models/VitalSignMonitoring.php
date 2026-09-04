<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VitalSignMonitoring extends Model
{
    use HasFactory;

    protected $fillable = [
        'care_session_id',
        'recorded_at',
        'blood_pressure',
        'heart_rate',
        'respiratory_rate',
        'spo2',
        'temperature',
        'gcs_score',
        'evaluation_notes',
    ];

    public function careSession(): BelongsTo
    {
        return $this->belongsTo(CareSession::class);
    }
}
