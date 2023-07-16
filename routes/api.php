<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('auth')->group(function(){
   // Login
Route::post('login', [AuthController::class, 'login']);

// Register
Route::post('register', [AuthController::class, 'register']);

// Forgot Password
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);

// OTP Verification
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);

// Reset Password
Route::post('reset-password', [AuthController::class, 'resetPassword']);

});

// Route::middleware('auth:api')->group(function () {
//     Route::get('protected-route', function () {
//         return response()->json(['message' => 'This is a protected route.'], 200);
//     });
//     // Add more protected routes here...
// });
