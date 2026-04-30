<?php

namespace App\Services;

use App\Models\Group;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class BackwardScheduleService
{
    public function recalculate(Group $group): void
    {
        if ($group->isDissolved()) {
            return;
        }

        $hoursPerDay = (float) config('group_task.working_hours_per_day', 8);
        if ($hoursPerDay <= 0) {
            $hoursPerDay = 8;
        }

        $tasks = $group->tasks()
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->get();

        if ($tasks->isEmpty()) {
            return;
        }

        $cursorEnd = Carbon::parse($group->deadline)->startOfDay();

        foreach ($tasks as $task) {
            $assignee = $task->assignee_id ? User::find($task->assignee_id) : null;
            [$start, $end] = $this->taskDateRangeBackward(
                $assignee,
                $cursorEnd,
                (float) $task->estimated_hours,
                $hoursPerDay
            );

            $task->forceFill([
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
            ])->saveQuietly();

            $cursorEnd = $start->copy()->subDay();
        }
    }

    /**
     * @return array{0: CarbonInterface, 1: CarbonInterface}
     */
    protected function taskDateRangeBackward(
        ?User $assignee,
        CarbonInterface $latestEnd,
        float $estimatedHours,
        float $hoursPerDay
    ): array {
        $daysNeeded = (int) max(1, ceil($estimatedHours / $hoursPerDay));

        $end = Carbon::parse($latestEnd)->startOfDay();
        while ($this->isNonWorkingDay($assignee, $end)) {
            $end = $end->copy()->subDay();
        }

        $cursor = $end->copy();
        $remaining = $daysNeeded;

        while ($remaining > 0) {
            if ($this->isNonWorkingDay($assignee, $cursor)) {
                $cursor = $cursor->copy()->subDay();

                continue;
            }

            $remaining--;

            if ($remaining > 0) {
                $cursor = $cursor->copy()->subDay();
            }
        }

        $start = $cursor->copy();

        return [$start, $end];
    }

    protected function isNonWorkingDay(?User $assignee, CarbonInterface $day): bool
    {
        if ($day->isWeekend()) {
            return true;
        }

        if (! $assignee) {
            return false;
        }

        return $assignee->schedules()
            ->whereDate('on_date', $day->toDateString())
            ->whereIn('type', ['holiday', 'personal', 'off'])
            ->exists();
    }
}
