<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    protected User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * Find a user by email.
     *
     * @param $email
     * @return mixed
     */
    public function findUserByEmail($email): mixed
    {
        return $this->user->where('email', $email)->first();
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data): mixed
    {
        return $this->user->create($data);
    }

    /**
     * Revoke all tokens for a user.
     *
     * @param Authenticatable $user
     * @return void
     */
    public function revokeTokens(Authenticatable $user): void
    {
        $user->tokens()->delete();
    }

    /**
     * Create or update a password reset token.
     *
     * @param string $email
     * @param string $token
     * @return void
     */
    public function createPasswordResetToken(string $email, string $token): void
    {
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            ['token' => $token, 'created_at' => now()]
        );
    }

    /**
     * Retrieve a password reset token record.
     *
     * @param string $email
     * @param string $token
     * @return object|null
     */
    public function getPasswordResetToken(string $email, string $token): ?object
    {
        return DB::table('password_reset_tokens')->where('email', $email)->where('token', $token)->first();
    }

    /**
     * Delete the password reset token after usage.
     *
     * @param string $email
     * @return void
     */
    public function deletePasswordResetToken(string $email): void
    {
        DB::table('password_reset_tokens')->where('email', $email)->delete();
    }
}
