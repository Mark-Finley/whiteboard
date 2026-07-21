<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatientInvestigation extends Model
{
    use HasFactory;

    protected $table = 'patient_investigations';

    protected $fillable = [
        'patient_id',
        'investigation_type',
        'category',
        'priority',
        'status',
        'notes',
        'assigned_by',
        'assigned_at',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(PatientInvestigationUpdate::class);
    }

    /**
     * Get mapped timeline data for serialization.
     */
    public function getTimelineData(): array
    {
        return $this->updates->map(fn($u) => [
            'status' => $u->status,
            'updated_by_name' => $u->updatedBy?->name ?? 'System',
            'comments' => $u->comments,
            'created_at' => $u->created_at ? $u->created_at->format('d M Y, g:i a') : '',
        ])->all();
    }
}
