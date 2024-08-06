<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;


class ForgotpasswordController extends Controller
{

    // Constructor to set the email view
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function sendResetLinkEmail(Request $request)
    {
        try {
            // Validate the email input
            $request->validate(['email' => 'required|email']);

            // Send the password reset link
            $status = Password::sendResetLink(
                $request->only('email'),
            );

            // Check if the password reset link was sent successfully
            if ($status === Password::RESET_LINK_SENT) {

                return response()->json([
                    'message' => __($status)
                ], 200);
            } else {
                return response()->json([
                   'message' => __($status)
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while sending the password reset link.', 'error' => $e->getMessage()], 500);
        }
    }

    public function resetPassword(Request $request) {
       try {
        $request->validate([
            'token'     => 'required',
            'email'     => 'required|email',
            'password'  => 'required|min:8|confirmed'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);
     
                $user->save();
     
                event(new PasswordReset($user));

                $user->notify(new UserResetPasswordNotification($user->username, $user->email, $password));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => __($status)
            ], 200);
        } else {
            return response()->json([
                'status' => __($status)
            ], 200);
        }
       } catch (\Exception $e) {
        return response()->json(['message' => 'An error occurred while sending the password reset link.', 'error' => $e->getMessage()], 500);
       }
    }
}
