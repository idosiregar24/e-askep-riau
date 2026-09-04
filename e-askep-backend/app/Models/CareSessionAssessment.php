<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareSessionAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'care_session_id',
        'stage_type',
        'assessment_payload',
    ];

    protected $casts = [
        'assessment_payload' => 'array',
    ];

    public function careSession(): BelongsTo
    {
        return $this->belongsTo(CareSession::class);
    }
}
