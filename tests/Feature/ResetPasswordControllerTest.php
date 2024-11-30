<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful password reset.
     *
     * @return void
     */
    public function test_successful_password_reset(): void
    {
        // Arrange: Create a user with a known password
        $user = User::factory()->create([
            'email' => 'umar@gmail.com',
            'password' => Hash::make('password111'),
        ]);

        // Simulate a valid password reset token
        $token = Hash::make('valid-reset-token');

        DB::table('password_reset_tokens')->insert([
            'email' => 'umar@gmail.com',
            'token' => $token, // Store hashed token
            'created_at' => now(),
        ]);

        // Act: Send POST request to reset password endpoint
        $response = $this->postJson('/api/reset-password', [
            'email' => 'umar@gmail.com',
            'token' => $token, // Simulating a valid token
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert: Check response
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Password reset successful.',
            ]);

        // Assert: Check that the password has been updated
        $this->assertTrue(Hash::check('password123', $user->fresh()->password));
    }

    /**
     * Test reset password with validation errors.
     *
     * @return void
     */
    public function test_reset_password_with_validation_errors(): void
    {
        // Act: Send POST request with invalid data
        $response = $this->postJson('/api/reset-password', [
            'email' => 'invalid-email', // Invalid email format
            'password' => 'short', // Too short password
            'password_confirmation' => 'mismatch', // Mismatched confirmation
        ]);

        // Assert: Check response for validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Test error handling during password reset.
     *
     * @return void
     */
    public function test_error_handling_during_password_reset(): void
    {
        // Mock the UserService to throw an exception
        $this->mock(\App\Services\UserService::class, function ($mock) {
            $mock->shouldReceive('resetPassword')->andThrow(new \Exception('Test exception'));
        });

        // Act: Send POST request to reset password endpoint
        $response = $this->postJson('/api/reset-password', [
            'email' => 'umar@gmail.com',
            'token' => 'valid-reset-token',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        // Assert: Check server error response
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The provided email does not exist in our records.',
                'errors' => []
            ]);
    }
}
