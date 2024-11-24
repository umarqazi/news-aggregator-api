<?php

namespace App\Services;

use App\Repositories\UserPreferenceRepository;

class UserPreferenceService
{
    private UserPreferenceRepository $userPreferenceRepository;

    public function __construct(UserPreferenceRepository $userPreferenceRepository)
    {
        $this->userPreferenceRepository = $userPreferenceRepository;
    }

    /**
     * Store or update the user preferences
     *
     * @param array $preferencesData
     * @param int $userId
     * @return mixed
     */
    public function storePreferences(array $preferencesData, int $userId): mixed
    {
        return $this->userPreferenceRepository->createOrUpdate($userId, $preferencesData);
    }

    /**
     * Get the preferences of the authenticated user
     *
     * @param $userId
     * @return mixed
     */
    public function getPreferences($userId): mixed
    {
        return $this->userPreferenceRepository->findByUserId($userId);
    }
}
