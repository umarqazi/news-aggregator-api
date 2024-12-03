<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    use ApiResponse;

    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = $this->userService->register($request->all());
            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();
            return $this->successResponse(
                data: ['user' => $user, 'access_token' => $token, 'token_type' => 'Bearer'],
                message: 'User has been Registered Successfully!',
                statusCode: 201
            );
        } catch (ValidationException $e) {
            DB::rollBack();
            return $this->errorResponse(
                message: $e->getMessage(),
                statusCode: 422,
                errors: $e->errors()
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(
                message: $e->getMessage()
            );
        }
    }
}
