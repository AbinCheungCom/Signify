<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_renders(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('忘记密码');
    }

    public function test_forgot_password_sends_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post('/forgot-password', ['email' => $user->email]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_page_renders(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $response = $this->get('/reset-password/'.$token.'?email='.$user->email);

        $response->assertStatus(200);
        $response->assertSee('重置密码');
    }

    public function test_reset_password_updates_password(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPass@2026',
            'password_confirmation' => 'NewPass@2026',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('NewPass@2026', $user->fresh()->password));
    }
}
