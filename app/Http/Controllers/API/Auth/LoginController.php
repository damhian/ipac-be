<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required'], // This field can be email or username
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])
            ->orWhere('username', $credentials['email']) // Check both email and username
            ->first();

        // Check user and check if user already approved or not
        if(!$user){
            throw ValidationException::withMessages([
                'message' => 'There are no credentials registered with this email or username!',
            ]);
        } else if (!$user->isApproved()) {
            throw ValidationException::withMessages([
                'identity' => 'Your account is not approved yet!',
            ]);
        }

        if (auth()->attempt(['email' => $user->email, 'password' => $credentials['password']]) ||
            auth()->attempt(['username' => $user->username, 'password' => $credentials['password']])
        ) {
            $user = auth()->user();

            return (new UserResource($user))->additional([
                'token' => $user->createToken('myAppToken')->plainTextToken,
            ]);
        }

        return response()->json([
            'message' => 'Your credential does not match.',
        ], 401);
    }
}
