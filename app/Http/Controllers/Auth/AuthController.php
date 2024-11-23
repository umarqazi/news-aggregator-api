<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Log In User
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function login(LoginRequest $request)
    {
        try {
            $user = $this->userService->login($request->all());

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            if ($user) {
                return $this->successResponse(
                    data: ['user' => $user, 'token' => $token],
                    message: 'User successfully LoggedIn.'
                );
            }
        } catch (ValidationException $e) {
            return $this->errorResponse(
                message: $e->getMessage(),
                statusCode: 422,
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: $e->getMessage(),
            );
        }
    }

    /**
     * Log out User.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->userService->logout($request->user());

            return $this->successResponse(
                message: 'Successfully logged out'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'An error occurred during logout',
                statusCode: 500,
                errors: ['error' => $e->getMessage()]
            );
        }
    }
}
