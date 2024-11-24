<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserPreferencesRequest;
use App\Services\UserPreferenceService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{
    use ApiResponse;
    private UserPreferenceService $userPreferenceService;

    public function __construct(UserPreferenceService $userPreferenceService)
    {
        $this->userPreferenceService = $userPreferenceService;
    }

    /**
     * Store or update the user preferences
     *
     * @param StoreUserPreferencesRequest $request
     * @return JsonResponse
     */
    public function setPreferences(StoreUserPreferencesRequest $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            $preferences = $this->userPreferenceService->storePreferences($request->validated(), $userId);

            return $this->successResponse(
                data: $preferences,
                message: 'Preferences saved successfully.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: $e->getMessage(),
                statusCode: 500,
            );
        }
    }

    /**
     * Retrieve user preferences
     *
     * @return JsonResponse
     */
    public function getPreferences(): JsonResponse
    {
        try {
            $userId = Auth::id();
            $preferences = $this->userPreferenceService->getPreferences($userId);

            return $this->successResponse(
                data: $preferences ?? [],
                message: 'Preferences fetched successfully.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: $e->getMessage(),
                statusCode: 500,
            );
        }
    }
}
