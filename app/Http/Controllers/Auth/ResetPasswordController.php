<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class ResetPasswordController extends Controller
{
    use ApiResponse;

    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Reset Password
     *
     * @param ResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->userService->resetPassword($request->validated());

            DB::commit();
            return $this->successResponse(message: 'Password reset successful.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(message: $e->getMessage());
        }
    }
}
