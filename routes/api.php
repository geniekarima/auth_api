<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\Authenticate;



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('auth')->group(function(){
   // Login
    Route::post('login', [AuthController::class, 'login']);
    // Register
    Route::post('register', [AuthController::class, 'register']);
    // OTP Verification
    Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->middleware('auth:api');
    // resend OTP
    Route::post('resendOtp', [AuthController::class, 'resendOtp'])->middleware('auth:api');
    // Forget Password
      Route::post('forget-password', [AuthController::class, 'forgetPassword']);
       // Verify Forget Password
       Route::post('verify-forget-password', [AuthController::class, 'verifyforgetPassword']);
    // Reset Password
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

});

