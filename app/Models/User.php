<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role_id',
        'team_id',
        'ward_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function enteredPatients(): HasMany
    {
        return $this->hasMany(Patient::class, 'entered_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === 'Admin';
    }

    public function isTriage(): bool
    {
        return $this->role?->name === 'Triage Nurse';
    }

    public function isTriageDoctor(): bool
    {
        return $this->role?->name === 'Triage Doctor';
    }

    public function isWardDoctor(): bool
    {
        return $this->role?->name === 'Ward Doctor';
    }

    public function isWard(): bool
    {
        return $this->role?->name === 'Ward Staff';
    }

    public function isSpecialtyDoctor(): bool
    {
        return $this->role?->name === 'Specialty Doctor';
    }

    public function canAssignProcedures(): bool
    {
        if (!$this->role) {
            return false;
        }

        $roleName = strtolower($this->role->name);
        return in_array($roleName, [
            'admin',
            'in-charge',
            'consultant',
            'medical officer',
            'doctor',
            'specialty doctor',
            'triage doctor',
            'ward doctor'
        ], true);
    }

    public function canViewProcedures(): bool
    {
        return true;
    }
}
