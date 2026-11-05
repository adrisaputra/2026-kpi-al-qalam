<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class EmployeeReportPeriodLock extends Model
{
    public const LOCKED_MESSAGE = 'Rapor untuk periode ini sedang dikunci.';

    protected $connection = 'mysql';

    protected $fillable = [
        'month',
        'year',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'status' => 'string',
        ];
    }

    public static function isLockedForPeriod(int $month, int $year): bool
    {
        return self::query()
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'locked')
            ->exists();
    }

    public static function isUnlockedForPeriod(int $month, int $year): bool
    {
        return self::query()
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'unlocked')
            ->exists();
    }

    public static function isLockedForDate(DateTimeInterface|string $date): bool
    {
        $date = Carbon::parse($date);

        return self::isLockedForPeriod($date->month, $date->year);
    }
}
