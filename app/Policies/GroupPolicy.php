<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    public function view(User $user, Group $group): bool
    {
        if ($group->isDissolved()) {
            return false;
        }

        return $group->leader_id === $user->id
            || $group->members()->where('users.id', $user->id)->exists();
    }

    public function update(User $user, Group $group): bool
    {
        return ! $group->isDissolved() && $group->leader_id === $user->id;
    }

    public function dissolve(User $user, Group $group): bool
    {
        return ! $group->isDissolved() && $group->leader_id === $user->id;
    }

    public function manageTasks(User $user, Group $group): bool
    {
        return $this->view($user, $group);
    }

    public function invite(User $user, Group $group): bool
    {
        return ! $group->isDissolved() && $group->leader_id === $user->id;
    }
}
