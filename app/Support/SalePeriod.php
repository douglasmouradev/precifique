<?php

declare(strict_types=1);

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

final class SalePeriod
{
    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    public static function bounds(int $year, ?int $month = null): array
    {
        if ($month !== null) {
            $start = Carbon::create($year, $month, 1)->startOfDay();

            return [$start, $start->copy()->endOfMonth()];
        }

        return [
            Carbon::create($year, 1, 1)->startOfDay(),
            Carbon::create($year, 12, 31)->endOfDay(),
        ];
    }

    public static function applyMonth(Builder|Relation $query, int $year, int $month, string $column = 'sold_at'): Builder|Relation
    {
        [$start, $end] = self::bounds($year, $month);

        return $query->whereBetween($column, [$start, $end]);
    }

    public static function applyYear(Builder|Relation $query, int $year, string $column = 'sold_at'): Builder|Relation
    {
        [$start, $end] = self::bounds($year);

        return $query->whereBetween($column, [$start, $end]);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public static function applyFromFilters(Builder|Relation $query, array $filters, string $column = 'sold_at'): Builder|Relation
    {
        $year = isset($filters['year']) && $filters['year'] !== ''
            ? (int) $filters['year']
            : now()->year;

        $month = isset($filters['month']) && $filters['month'] !== ''
            ? (int) $filters['month']
            : null;

        if ($month !== null) {
            return self::applyMonth($query, $year, $month, $column);
        }

        return self::applyYear($query, $year, $column);
    }
}
