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
        'major_signs',
        'minor_signs',
    ];

    protected $casts = [
        'major_signs' => 'array',
        'minor_signs' => 'array',
    ];
}
