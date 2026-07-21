<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Gate::define('view-admin-dashboard', static fn (User $user): bool => $user->isAdmin());
        Gate::define('manage-users', static fn (User $user): bool => $user->isAdmin());
        Gate::define('manage-wards', static fn (User $user): bool => $user->isAdmin());
        Gate::define('manage-teams', static fn (User $user): bool => $user->isAdmin());
        Gate::define('manage-patients', static fn (User $user): bool => $user->isAdmin() || $user->isTriage());
        Gate::define('move-patient', static fn (User $user, Patient $patient): bool => $user->isAdmin() || $user->isTriage());
    }
}
