<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'care_session_id',
        'dosen_id',
        'revision_notes',
        'rubric_scores',
        'final_score',
        'signature_snapshot_url',
        'reviewed_at',
    ];

    protected $casts = [
        'rubric_scores' => 'array',
        'final_score'   => 'decimal:2',
        'reviewed_at'   => 'datetime',
    ];

    public function careSession(): BelongsTo
    {
        return $this->belongsTo(CareSession::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }
}
