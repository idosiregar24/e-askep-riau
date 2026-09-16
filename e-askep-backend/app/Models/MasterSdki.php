<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSdki extends Model
{
    use HasFactory;

    protected $table = 'master_sdki';

    protected $fillable = [
        'code',
        'title',
        'category',
        'sub_category',
        'definition',
        'major_signs',
        'minor_signs',
        'risk_factors',
        'causes',
        'source_url',
    ];

    protected $casts = [
        'major_signs'  => 'array',
        'minor_signs'  => 'array',
        'risk_factors' => 'array',
        'causes'       => 'array',
    ];
}
