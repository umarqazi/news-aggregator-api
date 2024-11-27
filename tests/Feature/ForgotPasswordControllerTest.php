<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful password reset link.
     *
     * @return void
     */
    public function test_successful_password_reset_link(): void
    {
        // Arrange: Create a user
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        Notification::fake(); // Mock notifications

        // Act: Send POST request to forgot password endpoint
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'user@example.com',
        ]);

        // Assert: Check response and notification sent
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Password reset link sent successfully to your email address.',
            ]);

        // Assert: Verify password reset notification was sent
        Notification::assertSentTo([$user], \Illuminate\Auth\Notifications\ResetPassword::class);
    }

    /**
     * Test failed password reset link with invalid email.
     *
     * @return void
     */
    public function test_failed_password_reset_link_with_invalid_email(): void
    {
        // Act: Send POST request with a non-existing email
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        // Assert: Check response for failure
        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Failed to send password reset link.',
            ]);

        // Assert: Verify no notifications were sent
        Notification::assertNothingSent();
    }

    /**
     * Test error handling during password reset request.
     *
     * @return void
     */
    public function test_error_handling_during_password_reset_request(): void
    {
        // Mock the UserService to throw an exception
        $this->mock(\App\Services\UserService::class, function ($mock) {
            $mock->shouldReceive('sendPasswordResetEmail')->andThrow(new \Exception('Test exception'));
        });

        // Act: Send POST request to forgot password endpoint
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'user@example.com',
        ]);

        // Assert: Check server error response
        $response->assertStatus(500)
            ->assertJson([
                'status' => 'error',
                'message' => 'An error occurred while sending the password reset email.',
            ]);
    }
}
