<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user, Group $group): bool
    {
        return app(GroupPolicy::class)->view($user, $group);
    }

    public function create(User $user, Group $group): bool
    {
        return app(GroupPolicy::class)->manageTasks($user, $group);
    }

    public function update(User $user, Task $task): bool
    {
        $group = $task->group;

        return app(GroupPolicy::class)->manageTasks($user, $group);
    }

    public function delete(User $user, Task $task): bool
    {
        return $this->update($user, $task);
    }
}
