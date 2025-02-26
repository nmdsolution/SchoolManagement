<?php

namespace App\Providers;

use App\Events\MarksUploadedEvent;
use App\Listeners\AutoGenerateReportListener;
use App\Rules\uniqueForCenter;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\View;
use App\Models\Teacher;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
//        $this->renderable(function (NotFoundHttpException $e, $request) {
//            if ($request->is('api/*')) {
//                return response()->json([
//                    'message' => 'Record not found.'
//                ], 404);
//            }
//        });
//        $this->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
//            if ($request->is('api/*')) {
//                return response()->json([
//                    'message' => 'Not authenticated'
//                ], 401);
//            }
//        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        //
        Schema::defaultStringLength(191);

//        $this->app['validator']->extend('unique_for_center', function ($attribute, $value, $parameters) {
//            // Extract and validate the parameters from the rule syntax.
//            [$table, $column, $ignoreID, $centerID] = $parameters;
//
//            // Create an instance of your CustomRule and call the passes method.
//            return (new uniqueForCenter($table, $column, $ignoreID, $centerID))->passes($attribute, $value);
//        });
        Validator::extend('numeric_or_slash', function ($attribute, $value, $parameters, $validator) {
            return is_numeric($value) || $value=='/';
        });

        Paginator::useBootstrapFive();
        Event::listen(
            MarksUploadedEvent::class,
            AutoGenerateReportListener::class
        );

        View::composer('*', function ($view) {
            $resetRequestsCount = 0;
            if (auth()->check()) {
                $resetRequestsCount = Teacher::owner()
                    ->whereHas('user', function($query) {
                        $query->where('reset_request', 1);
                    })
                    ->count();
            }
            $view->with('resetRequestsCount', $resetRequestsCount);
        });
    }
}
