<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VitalSignMonitoring extends Model
{
    use HasFactory;

    protected $fillable = [
        'care_session_id',
        'recorded_at',
        'blood_pressure',
        'heart_rate',
        'respiratory_rate',
        'spo2',
        'temperature',
        'gcs_score',
        'evaluation_notes',
    ];

    /**
     * `recorded_at` adalah kolom TIME, sehingga Eloquent mengembalikannya sebagai
     * string ("14:30:00") dan bukan instance Carbon. Atribut turunan ini disertakan
     * pada payload agar tampilan cukup memakai jam-menit yang sudah rapi.
     */
    protected $appends = ['recorded_at_label'];

    /** Jam pemantauan dalam format HH:MM, aman untuk nilai string maupun kosong. */
    protected function recordedAtLabel(): Attribute
    {
        return Attribute::get(function (): string {
            $value = $this->recorded_at;

            if (blank($value)) {
                return '-';
            }

            return substr((string) $value, 0, 5);
        });
    }

    public function careSession(): BelongsTo
    {
        return $this->belongsTo(CareSession::class);
    }
}
