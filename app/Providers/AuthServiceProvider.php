<?php

namespace App\Providers;

use App\Application\Policies\ExamPolicy;
use App\Models\Exam;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Exam::class => ExamPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        //        Gate::before(function ($user, $ability) {
        //            return $user->hasRole('Super Admin') ? true : null;
        //        });
        Gate::before(function ($user, $ability) {
            return $user->checkPermission($ability) ?: null;
        });
    }
}
