<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use ApiResponse;

    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Handle the forgot password request.
     *
     * @param ForgotPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $status = $this->userService->sendPasswordResetEmail($request->only('email'));

            if ($status === Password::RESET_LINK_SENT) {
                return $this->successResponse(
                    message: 'Password reset link sent successfully to your email address.'
                );
            }

            return $this->errorResponse(
                message: 'Failed to send password reset link.',
                errors: ['email' => __($status)]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'An error occurred while sending the password reset email.',
                statusCode: 500,
                errors: ['error' => $e->getMessage()]
            );
        }
    }
}
