<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\User;
use App\Notifications\ResetPassword;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends ApiController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(422, ['errors' => $validator->errors()]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);
        $response = ['token' => $token, 'user' => $user];
        return $this->successResponse(201, $response, 'Registered successfully');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $response = ['token' => $token];
        return $this->successResponse(201, $response, 'logged in successfully');

    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->successResponse(201,[], 'logged out successfully');
    }
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(422, $validator->errors());
        }

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return $this->errorResponse(422, 'No user found with that email');
        }
        $token = Str::random(60);
        $hashedToken = Hash::make($token);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $hashedToken,
                'created_at' => Carbon::now(),
            ]
        );
        Notification::send($user, new ResetPassword($token));
        return $this->successResponse(200, [], 'Password reset link sent successfully');
    }
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(422, $validator->errors());
        }

        $email = $request->input('email');
        $token = $request->input('token');
        $password = $request->input('password');
        $reset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$reset || !Hash::check($token, $reset->token)) {
            return $this->errorResponse(422, 'Invalid or expired reset token');
        }
        $createdAt = Carbon::parse($reset->created_at);
        if ($createdAt->diffInMinutes(Carbon::now()) > 60) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return $this->errorResponse(422, 'Invalid or expired reset token');
        }
        $user = User::where('email', $email)->first();
        $user->forceFill([
            'password' => Hash::make($password),
            'remember_token' => Str::random(60),
        ])->save();
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return $this->successResponse(200, [], 'Password reset successfully');
    }
}
