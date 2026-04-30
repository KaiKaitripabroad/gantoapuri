<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Task;
use App\Services\BackwardScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GroupTaskController extends Controller
{
    public function __construct(
        protected BackwardScheduleService $backwardSchedule
    ) {}

    public function store(Request $request, Group $group): RedirectResponse
    {
        $this->authorize('create', [Task::class, $group]);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'assignee_id' => ['nullable', 'exists:users,id'],
            'estimated_hours' => ['required', 'numeric', 'min:0.25', 'max:9999'],
            'status' => ['required', Rule::in(['not_started', 'in_progress', 'done'])],
        ]);

        if (! empty($validated['assignee_id'])) {
            $memberIds = $group->members()->pluck('users.id')->push($group->leader_id)->unique();
            if (! $memberIds->contains((int) $validated['assignee_id'])) {
                return back()->withErrors(['assignee_id' => '担当者はグループメンバーから選んでください。']);
            }
        }

        $maxSort = (int) $group->tasks()->max('sort_order');

        $group->tasks()->create([
            'title' => $validated['title'],
            'assignee_id' => $validated['assignee_id'] ?? null,
            'estimated_hours' => $validated['estimated_hours'],
            'status' => $validated['status'],
            'sort_order' => $maxSort + 1,
        ]);

        $this->backwardSchedule->recalculate($group);

        return back()->with('status', 'タスクを追加しました。');
    }

    public function update(Request $request, Group $group, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        if ($task->group_id !== $group->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'assignee_id' => ['nullable', 'exists:users,id'],
            'estimated_hours' => ['required', 'numeric', 'min:0.25', 'max:9999'],
            'status' => ['required', Rule::in(['not_started', 'in_progress', 'done'])],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        if (array_key_exists('assignee_id', $validated) && $validated['assignee_id'] !== null) {
            $memberIds = $group->members()->pluck('users.id')->push($group->leader_id)->unique();
            if (! $memberIds->contains((int) $validated['assignee_id'])) {
                return back()->withErrors(['assignee_id' => '担当者はグループメンバーから選んでください。']);
            }
        }

        $task->update($validated);

        $this->backwardSchedule->recalculate($group);

        return back()->with('status', 'タスクを更新しました。');
    }

    public function destroy(Group $group, Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);

        if ($task->group_id !== $group->id) {
            abort(404);
        }

        $task->delete();
        $this->backwardSchedule->recalculate($group);

        return back()->with('status', 'タスクを削除しました。');
    }
}
