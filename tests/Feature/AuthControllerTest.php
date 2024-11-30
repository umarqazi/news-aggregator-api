<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful login.
     *
     * @return void
     */
    public function test_successful_login(): void
    {
        // Arrange: Create a user
        $user = User::factory()->create([
            'email' => 'umarfarooq@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        // Act: Send a POST request to login endpoint
        $response = $this->postJson('/api/login', [
            'email' => 'umarfarooq@gmail.com',
            'password' => 'password123',
        ]);

        // Assert: Check response and token
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                    'token',
                ],
            ]);

        // Assert: Check user data in response
        $this->assertEquals($user->email, $response->json('data.user.email'));
    }

    /**
     * Test failed login with invalid credentials.
     *
     * @return void
     */
    public function test_failed_login_with_invalid_credentials(): void
    {
        // Arrange: Create a user
        User::factory()->create([
            'email' => 'umarfarooq@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        // Act: Send a POST request with invalid credentials
        $response = $this->postJson('/api/login', [
            'email' => 'umarfarooq@gmail.com',
            'password' => 'password111',
        ]);

        // Assert: Check error response
        $response->assertStatus(401)
            ->assertJson([
                'status' => false,
                'message' => 'Invalid credentials.',
                'errors' => NULL
            ]);
    }

    /**
     * Test successful logout.
     *
     * @return void
     */
    public function test_successful_logout(): void
    {
        // Arrange: Create a user and authenticate
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Act: Send a POST request to logout endpoint with authorization header
        $response = $this->postJson('/api/logout', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        // Assert: Check response
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Successfully logged out',
            ]);
    }

    /**
     * Test logout without authentication.
     *
     * @return void
     */
    public function test_logout_without_authentication(): void
    {
        // Act: Send a POST request to logout endpoint without a token
        $response = $this->postJson('/api/logout');

        // Assert: Check unauthorized response
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
