<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicinePrescription extends Model
{
    use HasFactory;

    protected $table = "medicine_prescriptions";

    protected $fillable = [
        'prescription_id',
        'medicine_id',
        'dosage',
        'duration',
        'time',
        'dose_interval',
        'comment'
    ];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function medicine() : BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
}
