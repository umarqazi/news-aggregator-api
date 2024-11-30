<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserPreferenceControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test storing user preferences successfully.
     */
    public function test_set_preferences_successfully(): void
    {
        // Arrange: Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: Send a POST request to set preferences
        $response = $this->postJson(route('set-preferences'), [
            'preferred_sources' => ['NewsAPI', 'The Guardian'],
            'preferred_categories' => ['Sports', 'Business'],
            'preferred_authors' => ['Umar Farooq'],
        ]);

        // Assert: Check response
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Preferences saved successfully.',
                'data' => [
                    'preferred_sources' => ['NewsAPI', 'The Guardian'],
                    'preferred_categories' => ['Sports', 'Business'],
                    'preferred_authors' => ['Umar Farooq'],
                ],
            ]);

        // Assert: Check database
        $this->assertDatabaseHas('user_preferences', [
            'user_id' => $user->id,
            'preferred_sources' => json_encode(['NewsAPI', 'The Guardian']),
            'preferred_categories' => json_encode(['Sports', 'Business']),
            'preferred_authors' => json_encode(['Umar Farooq']),
        ]);
    }

    /**
     * Test retrieving user preferences successfully.
     */
    public function test_get_preferences_successfully(): void
    {
        // Arrange: Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Seed the database with preferences
        UserPreference::factory()->create([
            'user_id' => $user->id,
            'preferred_sources' => ['NewsAPI', 'The Guardian'],
            'preferred_categories' => ['Sports', 'Business'],
            'preferred_authors' => ['Umar Farooq'],
        ]);

        // Act: Send GET request to fetch preferences
        $response = $this->getJson(route('get-preferences'));

        // Assert: Check response
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Preferences fetched successfully.',
                'data' => [
                    'preferred_sources' => ['NewsAPI', 'The Guardian'],
                    'preferred_categories' => ['Sports', 'Business'],
                    'preferred_authors' => ['Umar Farooq'],
                ],
            ]);
    }

    /**
     * Test storing preferences without authentication.
     */
    public function test_set_preferences_without_authentication(): void
    {
        // Act: Send POST request without authenticating
        $response = $this->postJson(route('set-preferences'), [
            'preferred_sources' => ['NewsAPI', 'The Guardian'],
            'preferred_categories' => ['Sports', 'Business'],
            'preferred_authors' => ['Umar Farooq'],
        ]);

        // Assert: Check unauthorized response
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    /**
     * Test retrieving preferences without authentication.
     */
    public function test_get_preferences_without_authentication(): void
    {
        // Act: Send GET request without authenticating
        $response = $this->getJson(route('get-preferences'));

        // Assert: Check unauthorized response
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    /**
     * Test error handling during preference storage.
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
        $response = $this->postJson(route('set-preferences'), [
            'preferred_sources' => ['NewsAPI', 'The Guardian'],
            'preferred_categories' => ['Sports', 'Business'],
            'preferred_authors' => ['Umar Farooq'],
        ]);

        // Assert: Check server error response
        $response->assertStatus(500)
            ->assertJson([
                'status' => false,
                'message' => 'Test exception',
            ]);
    }

    /**
     * Test error handling during preference retrieval.
     */
    public function test_error_handling_during_preference_retrieval(): void
    {
        // Arrange: Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Mock UserPreferenceService to throw an exception
        $this->mock(\App\Services\UserPreferenceService::class, function ($mock) {
            $mock->shouldReceive('getPreferences')->andThrow(new \Exception('Test exception'));
        });

        // Act: Send GET request to fetch preferences
        $response = $this->getJson(route('get-preferences'));

        // Assert: Check server error response
        $response->assertStatus(500)
            ->assertJson([
                'status' => false,
                'message' => 'Test exception',
            ]);
    }
}
