<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NursingCarePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'care_session_id',
        'sdki_id',
        'slki_id',
        'siki_id',
        'subjective_data',
        'objective_data',
        'etiology',
        'custom_outcome_targets',
        'custom_interventions',
        'priority_order',
    ];

    public function careSession(): BelongsTo
    {
        return $this->belongsTo(CareSession::class);
    }

    public function sdki(): BelongsTo
    {
        return $this->belongsTo(MasterSdki::class, 'sdki_id');
    }

    public function slki(): BelongsTo
    {
        return $this->belongsTo(MasterSlki::class, 'slki_id');
    }

    public function siki(): BelongsTo
    {
        return $this->belongsTo(MasterSiki::class, 'siki_id');
    }
}
