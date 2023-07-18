<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Notifications\OtpNotification;
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

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $accessToken = $user->createToken('authToken')->accessToken;
                $data = [
                    'token' => $accessToken,
                    'user' => $user
                ];
                return Base::success('User login successfully', $data);
            } else {
                return Base::error('Invalid Credentials');
            }

        } catch (\Exception $e) {
            return Base::exception($e);
        }
    }

    // Register
    public function register(Request $request)
    {
        try {
            $validateData = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);
            if ($validateData->fails())
                return Base::validation($validateData);

            $otp = rand(100000, 999999); // Generate a random 6-digit OTP
            $otpExpiresAt = now()->addSeconds(120);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'otp' => Hash::make($otp),
                'otp_expires_at' => $otpExpiresAt,

            ]);

            $messages = [
                'name' => $user->name,
                'body' => "Your Otp code is: " . $otp,
            ];
            $user->notify(new OtpNotification($messages));

            $accessToken = $user->createToken('authToken')->accessToken;
            $data = [
                'token' => $accessToken,
                'user' => $user
            ];
            return Base::success('User registered successfully', $data);

        } catch (Exception $e) {
            return Base::exception($e);
        }

    }

    public function verifyOtp(Request $request)
    {
        try {
            $validateData = $request->only('otp');

            $user = Auth::user();

            if ($user && Hash::check($validateData['otp'], $user->otp)) {
                // Check if OTP has expired
                if ($user->otp_expires_at && now()->gt($user->otp_expires_at)) {
                    return Base::error('OTP has expired. Please request a new OTP.');
                }
                $user->update([
                    'otp' => null,
                    'otp_expires_at' => null,
                    'otp_verified' => 1
                ]);

                return Base::success('OTP verification successful.');
            } else {
                return Base::error('Invalid OTP.');
            }

        } catch (\Exception $e) {
            return Base::exception($e);
        }
    }

    public function resendOtp()
    {
        try {
            $user = Auth::user();
            $otp = rand(100000, 9999999);
            // $otpExpiration = now()->addSeconds(120);
            $user->update([
                'otp' => Hash::make($otp),
                'otp_expires_at' => Carbon::now()->addSeconds(120),
            ]);

            $messages = [
                'name' => $user->name,
                'body' => "Your Otp code is: " . $otp,
            ];

            $user->notify(new OtpNotification($messages));

            return Base::success('OTP has been resend.');

        } catch (Exception $e) {
            return Base::exception($e);
        }
    }
    public function forgetPassword(Request $request)
    {
        // return $request;
        try {
            // $validateData = $request->validate([
            //     'email' => 'required|email|exists:users,email',
            // ]);
            $validateData = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);
            if ($validateData->fails())
                return Base::validation($validateData);

            $user = User::where('email', $request->email)->first();

            if (!$user)
                return Base::error('user not found');

            $otp = rand(100000, 9999999);

            $otpExpiration = Carbon::now()->addSeconds(120);


            $user->update([
                'otp' => Hash::make($otp),
                'otp_expires_at' => $otpExpiration,
            ]);

            $data = [
                'name' => $user->name,
                'otp' => $otp,
            ];

            $messages = [
                'name' => $user->name,
                'body' => "Your Otp code is: " . $otp,
            ];
            $user->notify(new OtpNotification($messages));

            return Base::success('OTP has been sent to your email. Please check your inbox.');

        } catch (Exception $e) {
            return Base::exception($e);
        }
    }

    public function verifyforgetPassword(Request $request)
    {
        try {
            $validateData = Validator::make($request->all(), [
                'email' => 'required|email',
                'otp' => 'required',
            ]);

            if ($validateData->fails())
                return Base::validation($validateData);

            $user = User::where('email', $request->email)->first();

            if (!$user)
                return Base::error('User not found.');

            // Check if the OTP matches and is not expired
            if ($user->otp && Hash::check($request->otp, $user->otp) && $user->otp_expires_at && now()->lt($user->otp_expires_at)) {
                $user->update([
                    'otp' => null,
                ]);
                return Base::success('Forget Password verification successful.');
            } else {
                // OTP verification failed or expired
                return Base::error('Invalid OTP or expired. Please try again.');
            }

        } catch (Exception $e) {
            return Base::exception($e);
        }
    }
    public function resetPassword(Request $request)
    {
        try {
            $validateData = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6|confirmed',
            ]);

            if ($validateData->fails())
                return Base::validation($validateData);

            $user = User::where('email', $request->email)->first();

            if (!$user)
                return Base::error('User not found.');

            // Update the user's password
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return Base::success('Password has been successfully reset.');

        } catch (Exception $e) {
            return Base::exception($e);
        }
    }




}
