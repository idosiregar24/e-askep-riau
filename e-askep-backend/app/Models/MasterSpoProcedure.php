<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterSpoProcedure extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'sub_cpmk_reference',
        'domain_category',
        'procedure_name',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
