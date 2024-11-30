<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewsFeedControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful retrieval of personalized news feed.
     */
    public function test_get_personalized_feed_successfully(): void
    {
        // Arrange: Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Simulate user preferences
        UserPreference::factory()->create([
            'user_id' => $user->id,
            'preferred_sources' => ['NewsAPI', 'The Guardian'],
            'preferred_categories' => ['Sports', 'Business', 'Breaking'],
            'preferred_authors' => ['Umar Farooq'],
        ]);

        // Simulate articles
        Article::factory()->create([
            'title' => 'Breaking News',
            'content' => 'This is breaking news content.',
            'category' => 'Breaking',
            'source' => 'NewsAPI',
            'author' => 'Umar Farooq',
            'published_at' => now(),
        ]);

        Article::factory()->create([
            'title' => 'Tech Update',
            'content' => 'Latest in tech world.',
            'category' => 'Technology',
            'source' => 'The Guardian',
            'author' => 'Umar Farooq',
            'published_at' => now(),
        ]);

        // Act: Send GET request to retrieve personalized feed
        $response = $this->getJson(route('get-personalized-feed'));

        // Assert: Check response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'content',
                            'category',
                            'source',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ]);
    }

    /**
     * Test error handling when no preferences are set.
     */
    public function test_error_when_no_preferences_are_set(): void
    {
        // Arrange: Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: Send GET request without preferences
        $response = $this->getJson(route('get-personalized-feed'));

        // Assert: Check server error response
        $response->assertStatus(500)
            ->assertJson([
                'status' => false,
                'message' => 'No preferences set for personalized feed.',
            ]);
    }

    /**
     * Test unauthorized access to personalized feed.
     */
    public function test_unauthorized_access_to_personalized_feed(): void
    {
        // Act: Send GET request without authenticating
        $response = $this->getJson(route('get-personalized-feed'));

        // Assert: Check unauthorized response
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
