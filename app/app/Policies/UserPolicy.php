<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function create(User $actor): bool
    {
        return $actor->role === 'admin';
    }

    public function update(User $actor, User $trainer): bool
    {
        return $actor->role === 'admin' && $trainer->role === 'admin';
    }

    public function assignLessons(User $actor, User $trainer): bool
    {
        return $this->update($actor, $trainer);
    }

    public function deactivate(User $actor, User $member): bool
    {
        return $actor->role === 'admin' && $member->role === 'member';
    }
}
