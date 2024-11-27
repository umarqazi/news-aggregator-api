<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful registration.
     *
     * @return void
     */
    public function test_successful_registration(): void
    {
        // Arrange: Valid user registration data
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Act: Send a POST request to the register endpoint
        $response = $this->postJson('/api/register', $data);

        // Assert: Check if the response is successful
        $response->assertStatus(201)
            ->assertJsonStructure([
                'statusCode',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                    'access_token',
                    'token_type',
                ],
            ]);

        // Assert: Check if the user is in the database
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ]);
    }

    /**
     * Test registration with validation errors.
     *
     * @return void
     */
    public function test_registration_with_validation_errors(): void
    {
        // Arrange: Invalid user registration data
        $data = [
            'name' => '', // Missing name
            'email' => 'invalid-email', // Invalid email
            'password' => 'short', // Password too short
        ];

        // Act: Send a POST request to the register endpoint
        $response = $this->postJson('/api/register', $data);

        // Assert: Check if the response contains validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Test registration with duplicate email.
     *
     * @return void
     */
    public function test_registration_with_duplicate_email(): void
    {
        // Arrange: Create an existing user
        User::factory()->create([
            'email' => 'duplicate@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Duplicate registration data
        $data = [
            'name' => 'Jane Doe',
            'email' => 'duplicate@example.com', // Duplicate email
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Act: Send a POST request to the register endpoint
        $response = $this->postJson('/api/register', $data);

        // Assert: Check for duplicate email error
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
