<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareProcedureLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'care_session_id',
        'procedure_id',
        'is_performed',
        'performed_at',
        'is_verified',
        'verified_by_id',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'is_performed' => 'boolean',
        'is_verified'  => 'boolean',
        'performed_at' => 'datetime',
        'verified_at'  => 'datetime',
    ];

    public function careSession(): BelongsTo
    {
        return $this->belongsTo(CareSession::class);
    }

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(MasterSpoProcedure::class, 'procedure_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_id');
    }
}
