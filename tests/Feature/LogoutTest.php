<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 已登录用户提交退出表单后会话失效并跳转首页
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * 退出后可正常访问登录页，访问个人中心被拦截
     */
    public function test_profile_requires_auth_after_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $this->post('/logout');

        $response = $this->get('/my/profile');

        $response->assertRedirect('/login');
    }
}
