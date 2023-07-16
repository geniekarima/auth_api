<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class VerifyOTP
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // If OTP is not verified, deny access
            if (!$user->otp_verified) {
                throw ValidationException::withMessages(['otp' => 'OTP not verified']);
            }
        }

        return $next($request);
    }
}

