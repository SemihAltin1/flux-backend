<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    /**
     * Register a new user.
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Login a user and return JWT token.
     *
     * @param array $credentials
     * @return array|null
     */
    public function login(array $credentials): ?array
    {
        $token = JWTAuth::attempt($credentials);

        if (!$token) {
            return null;
        }

        $user = JWTAuth::user();

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Send password reset link to user's email.
     *
     * @param string $email
     * @return bool
     */
    public function sendPasswordResetLink(string $email): bool
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return false;
        }

        // Generate reset token
        $token = Str::random(64);

        // Store token in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );


        return true;
    }

    /**
     * Reset user password.
     *
     * @param string $email
     * @param string $token
     * @param string $password
     * @return bool
     */
    public function resetPassword(string $email, string $token, string $password): bool
    {
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord) {
            return false;
        }

        // Check if token matches and is not expired (60 minutes)
        if (!Hash::check($token, $resetRecord->token)) {
            return false;
        }

        $createdAt = \Carbon\Carbon::parse($resetRecord->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            return false;
        }

        // Update password
        $user = User::where('email', $email)->first();
        if (!$user) {
            return false;
        }

        $user->update(['password' => Hash::make($password)]);

        // Delete the reset token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return true;
    }

    /**
     * Get authenticated user profile.
     *
     * @return User|null
     */
    public function getProfile(): ?User
    {
        try {
            return JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Logout user (invalidate token).
     *
     * @return bool
     */
    public function logout(): bool
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Refresh JWT token.
     *
     * @return string|null
     */
    public function refresh(): ?string
    {
        try {
            return JWTAuth::refresh(JWTAuth::getToken());
        } catch (\Exception $e) {
            return null;
        }
    }
    
    public function deleteAccount(): bool
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return false;
            }

            return (bool) $user->delete();
        } catch (\Exception $e) {
            return false;
        }
    }
}
