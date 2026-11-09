<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password:web'],
        ], [
            'password.current_password' => 'Het opgegeven wachtwoord is onjuist.',
            'password.required' => 'Vul je huidige wachtwoord in.',
        ]);

        $user = $request->user();

        DB::transaction(function () use ($user): void {
            $lessons = Lesson::query()
                ->whereHas('registrations', fn ($query) => $query
                    ->where('user_id', $user->getKey())
                    ->where('status', 'enrolled'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $user->lessonRegistrations()->delete();

            foreach ($lessons as $lesson) {
                if ($lesson->registrations()->where('status', 'enrolled')->count() >= $lesson->max_participants) {
                    continue;
                }

                $lesson->registrations()
                    ->where('status', 'waitlisted')
                    ->orderBy('registered_at')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->first()
                    ?->update(['status' => 'enrolled']);
            }

            // Avoid Eloquent's deleted model being saved again when logout rotates its remember token.
            DB::table('users')->where('id', $user->getKey())->delete();
        });

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('status', 'Je account is verwijderd.');
    }
}
