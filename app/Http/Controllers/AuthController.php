<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;
use App\Traits\Base;


class AuthController extends Controller
{

    use Base;
    // Login
    public function login(Request $request)
    {
        try {
            $credentials = $request->only(['email', 'password']);

            $user = User::where('email', $credentials['email'])->first();

            if(Auth::attempt($credentials)){
                $user = Auth::user();
            $accessToken = $user->createToken('authToken')->accessToken;
            $data = [
                'token' => $accessToken,
                'user' => $user
            ];
            return Base::success('User login successfully',$data);
            }
            else{
                return Base::error('Invalid Credentials');
            }

        } catch (\Exception $e) {
            // Handle other exceptions here (if needed)
            return Base::exception($e);
        }
    }

    // Register
    public function register(Request $request)
    {
        try {
            $validateData = Validator::make($request->all(),[
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);
            if ($validateData->fails()) return Base::validation($validateData);
            // Generate an OTP and set the expiration time (e.g., 5 minutes from now)
                $otp = rand(100000, 999999); // Generate a random 6-digit OTP
                $otpExpiresAt = now()->addMinutes(5);

          $user =  User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'otp' => $otp,
                'otp_expires_at' => $otpExpiresAt,

            ]);
            // Generate an access token for the user using Laravel Passport
            // $user = User::create($data);
            $accessToken = $user->createToken('authToken')->accessToken;
            $data = [
                'token' => $accessToken,
                'user' => $user
            ];
            return Base::success('User registered successfully', $data);

        } catch (Exception $e) {
            // Handle other exceptions here (if needed)
            return Base::exception($e);
        }

        // $data['password'] = Hash::make($data['password']);

        // Generate and set OTP
        // $data['otp'] = rand(100000, 999999);
        // $data['otp_expires_at'] = now()->addMinutes(5); // OTP expiration time: 5 minutes

        // $user = User::create($data);
        // $token = $user->createToken('authToken')->accessToken;

        // Send the OTP to the user (you can use Laravel Mail here)
        // Example: Mail::to($user->email)->send(new SendOTP($user->otp));


    }

    // // Forgot Password
    // public function forgotPassword(Request $request)
    // {
    //     $request->validate(['email' => 'required|email']);

    //     $response = Password::sendResetLink($request->only('email'));

    //     if ($response === Password::RESET_LINK_SENT) {
    //         return response()->json(['message' => 'Password reset link sent'], 200);
    //     }

    //     throw ValidationException::withMessages(['email' => 'Unable to send reset link']);
    // }

    // // OTP Verification
    // public function verifyOtp(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'otp' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         throw ValidationException::withMessages($validator->errors()->toArray());
    //     }

    //     $user = User::where('email', $request->email)->first();

    //     if (!$user) {
    //         throw ValidationException::withMessages(['email' => 'User not found']);
    //     }

    //     // Check if OTP exists and if it's still valid
    //     if ($user->otp && $user->otp_expires_at >= now()) {
    //         if ($user->otp === $request->otp) {
    //             // Reset the OTP and set the OTP verification flag to true
    //             $user->otp = null;
    //             $user->otp_verified = true;
    //             $user->save();

    //             return response()->json(['message' => 'OTP verified successfully'], 200);
    //         } else {
    //             throw ValidationException::withMessages(['otp' => 'Invalid OTP']);
    //         }
    //     } else {
    //         throw ValidationException::withMessages(['otp' => 'OTP has expired']);
    //     }
    // }

    // // Reset Password
    // public function resetPassword(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required|string|min:6|confirmed',
    //         'token' => 'required|string',
    //     ]);

    //     $response = Password::reset($request->only(
    //         'email', 'password', 'password_confirmation', 'token'
    //     ), function ($user, $password) {
    //         $user->forceFill([
    //             'password' => Hash::make($password),
    //             'remember_token' => Str::random(60),
    //         ])->save();
    //     });

    //     if ($response === Password::PASSWORD_RESET) {
    //         return response()->json(['message' => 'Password reset successful'], 200);
    //     }

    //     throw ValidationException::withMessages(['email' => 'Unable to reset password']);
    // }
}

