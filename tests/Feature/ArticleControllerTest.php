<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ArticleService;
use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Arrange: Create a user and log them in
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /**
     * Test retrieving articles successfully.
     *
     * @return void
     */
    public function test_articles_list_successfully(): void
    {
        // Arrange: Create articles
        Article::factory()->count(10)->create();

        // Act: Send GET request to fetch articles
        $response = $this->getJson('/api/articles');

        // Assert: Check response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'current_page',
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
                    'total',
                    'per_page',
                    'last_page',
                    'first_page_url',
                    'last_page_url',
                    'next_page_url',
                    'prev_page_url',
                ],
            ]);
    }

    /**
     * Test filtering articles by keyword.
     *
     * @return void
     */
    public function test_filter_articles_by_keyword(): void
    {
        // Arrange: Create articles
        Article::factory()->create(['title' => 'Laravel Testing Tips']);
        Article::factory()->create(['title' => 'Vue.js Advanced Techniques']);

        // Act: Send GET request with keyword filter
        $response = $this->getJson('/api/articles?keyword=Laravel');

        // Assert: Check response contains only filtered articles
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonFragment(['title' => 'Laravel Testing Tips']);
    }

    /**
     * Test fetching a single article successfully.
     *
     * @return void
     */
    public function test_fetch_single_article_successfully(): void
    {
        // Arrange: Create an article
        $article = Article::factory()->create([
            'title' => 'Unique Article',
            'content' => 'This is a unique article content.',
        ]);

        // Act: Send GET request for the specific article
        $response = $this->getJson("/api/articles/{$article->id}");

        // Assert: Check response contains the article data
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Article retrieved successfully',
                'data' => [
                    'id' => $article->id,
                    'title' => 'Unique Article',
                    'content' => 'This is a unique article content.',
                ],
            ]);
    }

    /**
     * Test fetching a non-existent article.
     *
     * @return void
     */
    public function test_fetch_non_existent_article(): void
    {
        // Act: Send GET request for a non-existent article
        $response = $this->getJson('/api/articles/99999');

        // Assert: Check response for 404 error
        $response->assertStatus(404)
            ->assertJson([
                'status' => false,
                'message' => 'Article not found',
                'errors' => []
            ]);
    }
}
