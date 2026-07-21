<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ward extends Model
{
    use HasFactory;

    public const TRIAGE_HOLDING = 'TRIAGE HOLDING';
    public const RED = 'RED';
    public const ORANGE = 'ORANGE';
    public const YELLOW = 'YELLOW';

    public const TRIAGE_COLOR = '#6b7280';
    public const RED_COLOR = '#dc2626';
    public const ORANGE_COLOR = '#f97316';
    public const YELLOW_COLOR = '#eab308';

    public const COLOR_PRESETS = [
        self::TRIAGE_HOLDING => self::TRIAGE_COLOR,
        self::RED => self::RED_COLOR,
        self::ORANGE => self::ORANGE_COLOR,
        self::YELLOW => self::YELLOW_COLOR,
    ];

    protected $fillable = [
        'name',
        'color_code',
    ];

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public static function colorPresets(): array
    {
        return self::COLOR_PRESETS;
    }

    public static function colorCodes(): array
    {
        return array_values(self::COLOR_PRESETS);
    }

    public static function colorForName(string $name): string
    {
        return self::COLOR_PRESETS[$name] ?? self::TRIAGE_COLOR;
    }
}
