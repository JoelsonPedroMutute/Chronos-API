<?php

namespace App\Providers;

use App\Models\Companies;
use App\Models\User;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeCategory;
use App\Policies\UserPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\EmployeeCategoryPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
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
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Companies::class, CompanyPolicy::class);
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(EmployeeCategory::class, EmployeeCategoryPolicy::class);
    }
}
