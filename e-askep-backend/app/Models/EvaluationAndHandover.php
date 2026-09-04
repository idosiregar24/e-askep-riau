<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationAndHandover extends Model
{
    use HasFactory;

    protected $fillable = [
        'care_session_id',
        'format_type',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function careSession(): BelongsTo
    {
        return $this->belongsTo(CareSession::class);
    }
}
