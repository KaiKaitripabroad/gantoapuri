<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Services\BackwardScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __construct(
        protected BackwardScheduleService $backwardSchedule
    ) {}

    public function index(Request $request): View
    {
        $month = $request->query('month', now()->format('Y-m'));
        try {
            $start = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable) {
            $start = now()->startOfMonth();
        }

        $end = $start->copy()->endOfMonth();

        $schedules = $request->user()
            ->schedules()
            ->whereBetween('on_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('on_date')
            ->get()
            ->groupBy(fn (Schedule $s) => $s->on_date->toDateString());

        return view('calendar.index', [
            'monthStart' => $start,
            'schedulesByDate' => $schedules,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'on_date' => ['required', 'date'],
            'type' => ['required', Rule::in(['holiday', 'personal', 'off', 'task_related'])],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        Schedule::create([
            'user_id' => $request->user()->id,
            'on_date' => $validated['on_date'],
            'type' => $validated['type'],
            'title' => $validated['title'] ?? null,
        ]);

        $request->user()->groups()->whereNull('groups.dissolved_at')->get()
            ->each(fn ($group) => $this->backwardSchedule->recalculate($group));

        return back()->with('status', '予定を追加しました。');
    }

    public function destroy(Request $request, Schedule $schedule): RedirectResponse
    {
        if ($schedule->user_id !== $request->user()->id) {
            abort(403);
        }

        $schedule->delete();

        $request->user()->groups()->whereNull('groups.dissolved_at')->get()
            ->each(fn ($group) => $this->backwardSchedule->recalculate($group));

        return back()->with('status', '予定を削除しました。');
    }
}
