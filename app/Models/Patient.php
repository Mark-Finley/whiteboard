<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'ghims_number',
        'patient_name',
        'date_of_birth',
        'age',
        'chief_complaint',
        'condition',
        'ward_id',
        'team_id',
        'status',
        'time_in',
        'time_out',
        'ward_time_cache',
        'entered_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'ward_time_cache' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (Patient $patient): void {
            if ($patient->date_of_birth) {
                $patient->age = Carbon::parse($patient->date_of_birth)->age;
            }
        });
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(PatientMovement::class);
    }

    public function investigations(): HasMany
    {
        return $this->hasMany(PatientInvestigation::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'patient_team');
    }


    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getWardTimeSpentAttribute(): string
    {
        if (! $this->time_in) {
            return '—';
        }

        $end = $this->time_out ?? now();

        $diff = $end->diff($this->time_in);
        $hours = $diff->d * 24 + $diff->h;
        $minutes = $diff->i;

        if ($hours <= 0 && $minutes <= 0) {
            return '0m';
        }

        if ($hours <= 0) {
            return sprintf('%dm', $minutes);
        }

        return sprintf('%dh %dm', $hours, $minutes);
    }

    /**
     * Return cumulative time spent per ward as formatted strings.
     *
     * @return array<string,string>  e.g. ['RED' => '1h 12m']
     */
    public function getCumulativeWardTimesAttribute(): array
    {
        if (is_array($this->ward_time_cache) && $this->ward_time_cache !== []) {
            return $this->ward_time_cache;
        }

        $movements = $this->movements()->orderBy('created_at')->get();

        $segments = [];

        if (! $this->time_in) {
            return [];
        }

        $start = $this->time_in;

        foreach ($movements as $movement) {
            $end = $movement->created_at;
            $wardId = $movement->from_ward_id;

            if ($wardId) {
                $wardName = optional($movement->fromWard)->name ?? 'Unknown';
                $seconds = $end->diffInSeconds($start);
                $segments[$wardName] = ($segments[$wardName] ?? 0) + $seconds;
            }

            $start = $end;
        }

        // final segment: from last movement (or initial time_in) to time_out or now
        $finalEnd = $this->time_out ?? now();
        if ($movements->count()) {
            $last = $movements->last();
            $finalWardId = $last->to_ward_id;
            $finalWardName = optional($last->toWard)->name ?? ($this->ward?->name ?? 'Unknown');
        } else {
            $finalWardId = $this->ward_id;
            $finalWardName = $this->ward?->name ?? 'Unassigned';
        }

        $finalSeconds = $finalEnd->diffInSeconds($start);
        $segments[$finalWardName] = ($segments[$finalWardName] ?? 0) + $finalSeconds;

        // format seconds to human readable
        $formatted = [];
        foreach ($segments as $wardName => $seconds) {
            $hours = intdiv((int) $seconds, 3600);
            $minutes = intdiv((int) ($seconds % 3600), 60);
            if ($hours > 0) {
                $formatted[$wardName] = sprintf('%dh %dm', $hours, $minutes);
            } elseif ($minutes > 0) {
                $formatted[$wardName] = sprintf('%dm', $minutes);
            } else {
                $formatted[$wardName] = '0m';
            }
        }

        return $formatted;
    }

    /**
     * Return timeline segments including start/end/duration for each ward.
     *
     * @return array<int,array{ward:string, start:string, end:string, duration:string}>
     */
    public function getWardTimelineAttribute(): array
    {
        $movements = $this->movements()->orderBy('created_at')->get();

        if (! $this->time_in) {
            return [];
        }

        $segments = [];
        $start = $this->time_in;

        foreach ($movements as $movement) {
            $end = $movement->created_at;
            $wardName = optional($movement->fromWard)->name ?? 'Unknown';
            $seconds = $end->diffInSeconds($start);
            $hours = intdiv((int) $seconds, 3600);
            $minutes = intdiv((int) ($seconds % 3600), 60);
            $duration = $hours > 0 ? sprintf('%dh %dm', $hours, $minutes) : sprintf('%dm', $minutes);

            $segments[] = [
                'ward' => $wardName,
                'start' => $start->toDateTimeString(),
                'end' => $end->toDateTimeString(),
                'duration' => $duration,
            ];

            $start = $end;
        }

        $finalEnd = $this->time_out ?? now();
        if ($movements->count()) {
            $last = $movements->last();
            $finalWardName = optional($last->toWard)->name ?? ($this->ward?->name ?? 'Unknown');
        } else {
            $finalWardName = $this->ward?->name ?? 'Unassigned';
        }

        $seconds = $finalEnd->diffInSeconds($start);
        $hours = intdiv((int) $seconds, 3600);
        $minutes = intdiv((int) ($seconds % 3600), 60);
        $duration = $hours > 0 ? sprintf('%dh %dm', $hours, $minutes) : sprintf('%dm', $minutes);

        $segments[] = [
            'ward' => $finalWardName,
            'start' => $start->toDateTimeString(),
            'end' => $finalEnd->toDateTimeString(),
            'duration' => $duration,
        ];

        return $segments;
    }

    public function recomputeWardTimeCache(): void
    {
        $this->ward_time_cache = $this->cumulative_ward_times;
        $this->saveQuietly();
    }
}
