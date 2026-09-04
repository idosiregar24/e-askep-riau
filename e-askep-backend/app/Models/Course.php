<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'program_study',
        'academic_year',
    ];

    public function studentGroups(): HasMany
    {
        return $this->hasMany(StudentGroup::class);
    }

    public function spoProcedures(): HasMany
    {
        return $this->hasMany(MasterSpoProcedure::class);
    }

    public function careSessions(): HasMany
    {
        return $this->hasMany(CareSession::class);
    }
}
