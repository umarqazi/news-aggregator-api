<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserPreferenceControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test storing user preferences successfully.
     *
     * @return void
     */
    public function test_set_preferences_successfully(): void
    {
        // Arrange: Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: Send a POST request to set preferences
        $response = $this->postJson('/api/user/preferences', [
            'theme' => 'dark',
            'notifications' => true,
        ]);

        // Assert: Check response
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Preferences saved successfully.',
                'data' => [
                    'theme' => 'dark',
                    'notifications' => true,
                ],
            ]);

        // Assert: Check database (assuming preferences are saved in a table)
        $this->assertDatabaseHas('user_preferences', [
            'user_id' => $user->id,
            'theme' => 'dark',
            'notifications' => true,
        ]);
    }

    /**
     * Test retrieving user preferences successfully.
     *
     * @return void
     */
    public function test_get_preferences_successfully(): void
    {
        // Arrange: Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Simulate user preferences (assuming preferences are stored in a service or table)
        $preferences = [
            'theme' => 'dark',
            'notifications' => true,
        ];

        // Mock UserPreferenceService to return preferences
        $this->mock(\App\Services\UserPreferenceService::class, function ($mock) use ($preferences) {
            $mock->shouldReceive('getPreferences')->with(1)->andReturn($preferences);
        });

        // Act: Send GET request to fetch preferences
        $response = $this->getJson('/api/user/preferences');

        // Assert: Check response
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Preferences fetched successfully.',
                'data' => $preferences,
            ]);
    }

    /**
     * Test storing preferences without authentication.
     *
     * @return void
     */
    public function test_set_preferences_without_authentication(): void
    {
        // Act: Send POST request without authenticating
        $response = $this->postJson('/api/user/preferences', [
            'theme' => 'dark',
            'notifications' => true,
        ]);

        // Assert: Check unauthorized response
        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthenticated.',
            ]);
    }

    /**
     * Test retrieving preferences without authentication.
     *
     * @return void
     */
    public function test_get_preferences_without_authentication(): void
    {
        // Act: Send GET request without authenticating
        $response = $this->getJson('/api/user/preferences');

        // Assert: Check unauthorized response
        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthenticated.',
            ]);
    }

    /**
     * Test error handling during preference storage.
     *
     * @return void
     */
    public function test_error_handling_during_preference_storage(): void
    {
        // Arrange: Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Mock UserPreferenceService to throw an exception
        $this->mock(\App\Services\UserPreferenceService::class, function ($mock) {
            $mock->shouldReceive('storePreferences')->andThrow(new \Exception('Test exception'));
        });

        // Act: Send POST request to set preferences
        $response = $this->postJson('/api/user/preferences', [
            'theme' => 'dark',
            'notifications' => true,
        ]);

        // Assert: Check server error response
        $response->assertStatus(500)
            ->assertJson([
                'status' => 'error',
                'message' => 'Test exception',
            ]);
    }
}
