<?php

namespace App\Repositories;

use App\Models\UserPreference;

class UserPreferenceRepository
{
    private UserPreference $userPreference;

    public function __construct()
    {
        $this->userPreference = new UserPreference();
    }

    /**
     * @param int $userId
     * @return mixed
     */
    public function findByUserId(int $userId): mixed
    {
        return $this->userPreference->where('user_id', $userId)->first();
    }

    /**
     * @param int $userId
     * @param array $data
     * @return mixed
     */
    public function createOrUpdate(int $userId, array $data): mixed
    {
        // Find or create a new entry for the user
        $preferences = UserPreference::firstOrNew(['user_id' => $userId]);
        $preferences->fill($data);
        $preferences->save();
        return $preferences;
    }
}
