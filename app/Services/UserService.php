<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * Register a new user.
     */
    public function register(array $data): mixed
    {
        $data['password'] = Hash::make($data['password']);
        return $this->userRepository->create($data);
    }

    /**
     * Log in a user.
     */
    public function login(array $data): mixed
    {
        $user = $this->userRepository->findUserByEmail($data['email']);
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new \Exception('Invalid credentials.', 401);
        }

        return $user;
    }

    /**
     * Revoke all tokens for the authenticated user.
     *
     * @param Authenticatable $user
     * @return void
     * @throws \Exception
     */
    public function logout(Authenticatable $user): void
    {
        $this->userRepository->revokeTokens($user);
    }

    /**
     * Handle forgot password: generate and store reset token.
     *
     * @param string $email
     * @return string
     * @throws \Exception
     */
    /*public function forgotPassword(string $email): string
    {
        $user = $this->userRepository->findUserByEmail($email);
        if (!$user) {
            throw new \Exception('User not found.');
        }

        $token = Str::random(60);
        $this->userRepository->createPasswordResetToken($email, $token);

        return $token;
    }*/

    /**
     * Send a password reset email to the given email address.
     *
     * @param array $data
     * @return string
     */
    public function sendPasswordResetEmail(array $data): string
    {
        return Password::sendResetLink($data);
    }

    /**
     * Handle password reset using the token.
     *
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function resetPassword(array $data): bool
    {
        $resetToken = $this->userRepository->getPasswordResetToken($data['email'], $data['token']);

        if (!$resetToken) {
            throw new \Exception('Invalid or expired token.');
        }

        // Ensure the token hasn't expired (valid for 60 minutes)
        if (Carbon::parse($resetToken->created_at)->addMinutes(60)->isPast()) {
            throw new \Exception('Token expired.');
        }

        // Update the user's password
        $user = $this->userRepository->findUserByEmail($data['email']);
        $user->password = Hash::make($data['password']);
        $user->save();

        // Delete the token
        $this->userRepository->deletePasswordResetToken($data['email']);

        return true;
    }
}
