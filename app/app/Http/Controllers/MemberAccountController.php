<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class MemberAccountController extends Controller
{
    public function deactivate(User $member): RedirectResponse
    {
        Gate::authorize('deactivate', $member);

        $member->update(['is_active' => false]);

        return redirect()
            ->route('participants.index', ['participant' => $member->getKey()])
            ->with('status', 'Het ledenaccount is uitgeschakeld.');
    }
}
