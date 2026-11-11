<?php

namespace App\Providers;

use App\Models\Lesson;
use App\Models\LessonRegistration;
use App\Models\User;
use App\Policies\LessonPolicy;
use App\Policies\LessonRegistrationPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Lesson::class, LessonPolicy::class);
        Gate::policy(LessonRegistration::class, LessonRegistrationPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
