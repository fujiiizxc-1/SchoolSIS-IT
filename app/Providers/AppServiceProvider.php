<?php

namespace App\Providers;

use App\Models\User;
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
        Gate::define('student', fn (User $user) => $user->role === 'student');
        Gate::define('teacher', fn (User $user) => $user->role === 'teacher');
        Gate::define('admin', fn (User $user) => $user->role === 'admin');
    }
}
