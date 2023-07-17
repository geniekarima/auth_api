<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Traits\Base;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    use Base;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    // public function register(): void
    // {
    //     $this->reportable(function (Throwable $e) {
    //         //
    //     });
    // }



    public function register(): void
    {
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return Base::error('URL Not Found');
            }
        });
    }
    public function unauthenticated($request, AuthenticationException $exception)
    {
        if (!$request->expectsJson()) {
            if (collect($request->route()->middleware())->contains('api')) {
                 return Base::error("You are not logged in.", [], 'not_auth');
            }
         }
    }
}
