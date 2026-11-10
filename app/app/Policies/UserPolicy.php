<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function deactivate(User $actor, User $member): bool
    {
        return $actor->role === 'admin' && $member->role === 'member';
    }
}
