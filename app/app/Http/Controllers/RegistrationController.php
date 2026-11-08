<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(StoreRegistrationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::query()->create([
            'name' => $validated['name'],
            'email' => Str::lower($validated['email']),
            'phone_number' => $validated['phone_number'],
            'password_hash' => $validated['password'],
            'role' => 'member',
            'is_active' => true,
        ]);

        return redirect()
            ->route('login')
            ->with('status', 'Je account is aangemaakt. Je kunt nu inloggen.');
    }
}
