<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPassword;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $email = 'test' . time() . '@example.com';
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertStatus(201)
            ->assertJson(['message' => 'Registered successfully']);
    }

    public function test_user_can_login()
    {
        $email = 'test' . time() . '@example.com';
        User::factory()->create([
            'email' => $email,
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'logged in successfully']);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/logout');

        $response->assertStatus(201)
            ->assertJson(['message' => 'logged out successfully']);
    }
    public function test_user_can_request_password_reset()
    {
        Notification::fake();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response = $this->postJson('/api/password/email', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'message',
                'success',
            ])
            ->assertJson(['message' => 'Password reset link sent successfully', 'success' => true]);

        Notification::assertSentTo($user, ResetPassword::class);
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_user_can_reset_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('old-password'),
        ]);
        $token = Str::random(60);
        $hashedToken = Hash::make($token);
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => $hashedToken,
            'created_at' => Carbon::now(),
        ]);

        $response = $this->postJson('/api/password/reset', [
            'email' => 'test@example.com',
            'token' => $token,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'message',
                'success',
            ])
            ->assertJson(['message' => 'Password reset successfully', 'success' => true]);

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('new-password', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }
}
?>
