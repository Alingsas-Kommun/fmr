<?php

namespace App\Services;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

class AnniversaryService
{
    /**
     * Get persons with their calculated service years within the specified range.
     *
     * @param array $filters
     * @return Collection
     */
    public function getPersonsByServiceYears(array $filters = []): Collection
    {
        $assignments = fn ($query) => $this->scopeAssignments($query, $filters);

        return Post::persons()
            ->published()
            ->with([
                'meta',
                'party',
                'personAssignments' => fn ($query) => $assignments($query)
                    ->reorder('period_start', 'asc'),
            ])
            ->whereHas('personAssignments', $assignments)
            ->get()
            ->map(function ($person) {
                $serviceYears = $this->calculateServiceYears($person->personAssignments);

                $firstname = $person->getMeta('person_firstname', '');
                $lastname = $person->getMeta('person_lastname', '');
                $fullName = trim($firstname . ' ' . $lastname) ?: $person->post_title;

                return [
                    'person' => $person,
                    'assignments' => $person->personAssignments,
                    'service_years' => $serviceYears,
                    'service_display' => $this->formatServiceTime($serviceYears),
                    'full_name' => $fullName,
                    'party_name' => $person->party->post_title ?? null,
                ];
            })
            ->filter(fn ($result) => $this->matchesFilter(
                $result['service_years'],
                $filters['min_years'] ?? null,
                $filters['max_years'] ?? null
            ))
            ->sortByDesc('service_years');
    }

    /**
     * Limit assignments to the selected board and roles.
     *
     * @param Builder|Relation $query
     * @param array $filters
     * @return Builder|Relation
     */
    private function scopeAssignments(Builder|Relation $query, array $filters): Builder|Relation
    {
        return $query
            ->when($filters['board_id'] ?? null, fn ($q, int $boardId) => $q->whereHas(
                'decisionAuthority',
                fn ($q) => $q->where('board_id', $boardId)
            ))
            ->when($filters['role_ids'] ?? [], fn ($q, array $roleIds) => $q->whereIn('role_term_id', $roleIds));
    }

    /**
     * Check if service years match the filter criteria.
     *
     * @param float $serviceYears
     * @param float|null $minYears
     * @param float|null $maxYears
     * @return bool
     */
    private function matchesFilter(float $serviceYears, ?float $minYears, ?float $maxYears): bool
    {
        return ($minYears === null || $serviceYears >= $minYears) &&
               ($maxYears === null || $serviceYears <= $maxYears);
    }

    /**
     * Calculate total service years from assignments, removing overlapping periods.
     *
     * @param Collection $assignments
     * @return float
     */
    public function calculateServiceYears(Collection $assignments): float
    {
        if ($assignments->isEmpty()) {
            return 0;
        }

        $today = Carbon::today();

        $periods = $assignments
            ->map(fn ($assignment) => $this->processAssignmentPeriod($assignment, $today))
            ->filter()
            ->toArray();

        $mergedPeriods = $this->mergeOverlappingPeriods($periods);

        $totalDays = collect($mergedPeriods)
            ->sum(fn ($period) => $period['start']->diffInDays($period['end']) + 1);

        return $totalDays / 365.25; // Convert to years (using 365.25 to account for leap years)
    }

    /**
     * Process a single assignment period, handling future dates.
     *
     * @param mixed $assignment
     * @param Carbon $today
     * @return array|null
     */
    private function processAssignmentPeriod($assignment, Carbon $today): ?array
    {
        $start = Carbon::parse($assignment->period_start);
        $end = Carbon::parse($assignment->period_end);

        // Skip future assignments
        if ($start->gt($today)) {
            return null;
        }

        // Cap end date at today if it's in the future
        $end = $end->gt($today) ? $today : $end;

        return compact('start', 'end');
    }

    /**
     * Merge overlapping date periods.
     *
     * @param array $periods
     * @return array
     */
    private function mergeOverlappingPeriods(array $periods): array
    {
        if (empty($periods)) {
            return [];
        }

        // Sort periods by start date using collection
        $sortedPeriods = collect($periods)
            ->sortBy(fn ($period) => $period['start']->timestamp)
            ->values()
            ->toArray();

        $merged = [$sortedPeriods[0]];

        foreach (array_slice($sortedPeriods, 1) as $current) {
            $lastMerged = &$merged[array_key_last($merged)];

            if ($this->periodsOverlap($current, $lastMerged)) {
                $lastMerged['end'] = $current['end']->gt($lastMerged['end'])
                    ? $current['end']
                    : $lastMerged['end'];
            } else {
                $merged[] = $current;
            }
        }

        return $merged;
    }

    /**
     * Check if two periods overlap or are adjacent.
     *
     * @param array $current
     * @param array $lastMerged
     * @return bool
     */
    private function periodsOverlap(array $current, array $lastMerged): bool
    {
        return $current['start']->lte($lastMerged['end']->addDay());
    }

    /**
     * Format service time as years, months, and days.
     *
     * @param float $years
     * @return string
     */
    public function formatServiceTime(float $years): string
    {
        $totalDays = round($years * 365.25);

        $timeParts = [
            'years' => intval($totalDays / 365.25),
            'months' => 0,
            'days' => 0
        ];

        $remainingDays = $totalDays - ($timeParts['years'] * 365.25);
        $timeParts['months'] = intval($remainingDays / 30.44);
        $timeParts['days'] = intval($remainingDays - ($timeParts['months'] * 30.44));

        return collect($timeParts)
            ->filter(fn ($value) => $value > 0)
            ->map(fn ($value, $key) => $this->formatTimePart($value, $key))
            ->implode(', ');
    }

    /**
     * Format a single time part (years, months, or days).
     *
     * @param int $value
     * @param string $type
     * @return string
     */
    private function formatTimePart(int $value, string $type): string
    {
        return match ($type) {
            'years' => sprintf(_n('%s year', '%s years', $value, 'fmr'), $value),
            'months' => sprintf(_n('%s month', '%s months', $value, 'fmr'), $value),
            'days' => sprintf(_n('%s day', '%s days', $value, 'fmr'), $value),
        };
    }
}
