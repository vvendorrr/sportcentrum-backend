<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class TrainerAccountController extends Controller
{
    // Traineraccount aanmaken.
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'phone_number' => $validated['phone_number'],
            'password_hash' => $validated['password'],
            'role' => 'admin',
            'is_active' => true,
        ]);

        return redirect()
            ->route('trainers.index')
            ->with('status', 'Traineraccount aangemaakt.');
    }

    // Traineraccount bewerken.
    public function update(Request $request, User $trainer): RedirectResponse
    {
        Gate::authorize('update', $trainer);

        abort_unless($trainer->role === 'admin', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($trainer->id),
            ],
            'phone_number' => ['required', 'string', 'max:30'],
            'is_active' => ['required', 'boolean'],
        ]);

        $trainer->update([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'phone_number' => $validated['phone_number'],
            'is_active' => $validated['is_active'],
        ]);

        return redirect()
            ->route('trainers.index', ['trainer' => $trainer->id])
            ->with('status', 'Traineraccount bijgewerkt.');
    }
}
