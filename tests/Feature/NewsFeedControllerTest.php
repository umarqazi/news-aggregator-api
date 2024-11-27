<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewsFeedControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful retrieval of personalized news feed.
     *
     * @return void
     */
    public function test_get_personalized_feed_successfully(): void
    {
        // Arrange: Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Simulate personalized articles (mocked or using a factory)
        $articles = [
            [
                'id' => 1,
                'title' => 'Breaking News',
                'content' => 'This is breaking news content.',
                'category' => 'Breaking',
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'title' => 'Tech Update',
                'content' => 'Latest in tech world.',
                'category' => 'Technology',
                'created_at' => now(),
            ],
        ];

        // Mock ArticleService to return personalized feed
        $this->mock(\App\Services\ArticleService::class, function ($mock) use ($articles) {
            $mock->shouldReceive('getPersonalizedNewsFeed')->with(1)->andReturn($articles);
        });

        // Act: Send GET request to retrieve personalized feed
        $response = $this->getJson('/api/news-feed');

        // Assert: Check response
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Personalized feed retrieved successfully.',
                'data' => $articles,
            ]);
    }

    /**
     * Test error handling during feed retrieval.
     *
     * @return void
     */
    public function test_error_handling_during_feed_retrieval(): void
    {
        // Arrange: Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Mock ArticleService to throw an exception
        $this->mock(\App\Services\ArticleService::class, function ($mock) {
            $mock->shouldReceive('getPersonalizedNewsFeed')->andThrow(new \Exception('Test exception'));
        });

        // Act: Send GET request to retrieve personalized feed
        $response = $this->getJson('/api/news-feed');

        // Assert: Check server error response
        $response->assertStatus(500)
            ->assertJson([
                'status' => 'error',
                'message' => 'Test exception',
            ]);
    }

    /**
     * Test unauthorized access to personalized feed.
     *
     * @return void
     */
    public function test_unauthorized_access_to_personalized_feed(): void
    {
        // Act: Send GET request without authenticating
        $response = $this->getJson('/api/news-feed');

        // Assert: Check unauthorized response
        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthenticated.',
            ]);
    }
}
