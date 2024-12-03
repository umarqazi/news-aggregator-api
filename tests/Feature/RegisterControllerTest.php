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
            'name' => 'Umar Farooq',
            'email' => 'umarfarooq@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Act: Send a POST request to the register endpoint
        $response = $this->postJson('/api/register', $data);

        // Assert: Check if the response is successful
        $response->assertStatus(201)
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
                    'access_token',
                    'token_type',
                ],
            ]);

        // Assert: Check if the user is in the database
        $this->assertDatabaseHas('users', [
            'name' => 'Umar Farooq',
            'email' => 'umarfarooq@gmail.com',
        ]);
    }

    /**
     * Test registration with validation errors.
     *
     * @return void
     */
    public function test_registration_with_validation_errors(): void
    {
        $data = [
            'name' => '',
            'email' => 'umarfarooq',
            'password' => '12345',
        ];

        $response = $this->postJson('/api/register', $data);

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
        // Create an existing user with same Email
        User::factory()->create([
            'email' => 'umarfarooq@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        // Duplicate registration data
        $data = [
            'name' => 'Umar Farooq',
            'email' => 'umarfarooq@gmail.com',
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
