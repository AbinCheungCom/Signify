<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileCreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未验证邮箱的用户不能创建档案（防开放注册下的垃圾档案）
     */
    public function test_unverified_user_cannot_create_profile(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->post('/my/profile', ['name' => '张三'])
            ->assertRedirect(route('verification.notice'));

        $this->assertDatabaseMissing('entrepreneurs', ['user_id' => $user->id]);
    }

    /**
     * 已验证邮箱的用户可以创建档案
     */
    public function test_verified_user_can_create_profile(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)
            ->post('/my/profile', ['name' => '张三'])
            ->assertRedirect(route('profile.show'));

        $this->assertDatabaseHas('entrepreneurs', [
            'user_id' => $user->id,
            'name' => '张三',
        ]);
    }
}
