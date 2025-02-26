<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
//        $this->reportable(function (Throwable $e) {
//        });
        //        $this->reportable(function (Throwable $e) {
        //            if ($e instanceof ModelNotFoundException) {
        //                Log::info('Model Not Found : ' . $e->getMessage() . ' at line : ' . $e->getLine());
        //            }
        //
        //            if ($e instanceof \RuntimeException) {
        //                Log::error('Run Time Exception error : ' . $e->getMessage() . ' at line : ' . $e->getLine());
        //            }
        //        });


    }
}
