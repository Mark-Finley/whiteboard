<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientInvestigationUpdate extends Model
{
    use HasFactory;

    protected $table = 'patient_investigation_updates';

    public $timestamps = false; // we only use created_at

    protected $fillable = [
        'patient_investigation_id',
        'status',
        'updated_by',
        'comments',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function investigation(): BelongsTo
    {
        return $this->belongsTo(PatientInvestigation::class, 'patient_investigation_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
