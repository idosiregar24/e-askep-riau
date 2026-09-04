<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSiki extends Model
{
    use HasFactory;

    protected $table = 'master_siki';

    protected $fillable = [
        'code',
        'title',
        'actions',
    ];

    protected $casts = [
        'actions' => 'array',
    ];
}
